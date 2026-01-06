<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        if ($product->stock_quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock available.'])->withInput();
        }
        
        // Get order form settings (product-specific with fallback to global)
        $deliveryOptions = $product->order_delivery_options 
            ? json_decode($product->order_delivery_options, true) 
            : json_decode(\App\Models\Setting::get('order_delivery_options', '[]'), true);
        $minQuantity = (int) ($product->order_min_quantity ?: \App\Models\Setting::get('order_min_quantity', 0));
        $maxQuantity = (int) ($product->order_max_quantity ?: \App\Models\Setting::get('order_max_quantity', 0));
        
        // Calculate delivery charge
        $deliveryCharge = 0;
        if ($request->has('delivery_option') && !empty($deliveryOptions)) {
            $selectedOption = $deliveryOptions[$request->delivery_option] ?? null;
            if ($selectedOption) {
                $deliveryCharge = (float) ($selectedOption['charge'] ?? 0);
            }
        }
        
        // Calculate total price
        $subtotal = $product->price * $request->quantity;
        $totalPrice = $subtotal + $deliveryCharge;
        
        // Validate min/max order quantity
        if ($minQuantity > 0 && $request->quantity < $minQuantity) {
            return back()->withErrors(['quantity' => "Minimum order quantity is {$minQuantity} items."])->withInput();
        }
        
        if ($maxQuantity > 0 && $request->quantity > $maxQuantity) {
            return back()->withErrors(['quantity' => "Maximum order quantity is {$maxQuantity} items."])->withInput();
        }
        
        // Normalize phone number
        try {
            $normalizedPhone = $this->normalizePhone($request->customer_phone);
        } catch (\Exception $e) {
            return back()->withErrors(['customer_phone' => $e->getMessage()])->withInput();
        }
        
        // Find or create user by phone number
        $user = User::where('phone', $normalizedPhone)->first();
        
        if (!$user) {
            // Create new user with sponsor role (orders create sponsors by default)
            $user = User::create([
                'name' => $request->customer_name,
                'phone' => $normalizedPhone,
                'role' => 'sponsor',
                'password' => null, // OTP-based auth, password not required
                'address' => $request->address,
            ]);
        } else {
            // Update user name/address and ensure role is sponsor
            $updateData = [];
            if ($request->customer_name && $user->name !== $request->customer_name) {
                $updateData['name'] = $request->customer_name;
            }
            if ($request->address && $user->address !== $request->address) {
                $updateData['address'] = $request->address;
            }
            if ($user->role !== 'sponsor') {
                $updateData['role'] = 'sponsor';
            }
            if (!empty($updateData)) {
                $user->update($updateData);
            }
        }
        
        // Get referral code from session (set by middleware)
        $referralCode = Session::get('referral_code');
        $sponsorId = null;
        
        if ($referralCode) {
            $sponsor = User::where('affiliate_code', $referralCode)->first();
            if ($sponsor) {
                $sponsorId = $sponsor->id;
            }
        }
        
        $order = Order::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price,
            'total_price' => $totalPrice,
            'customer_name' => $request->customer_name,
            'customer_phone' => $normalizedPhone,
            'address' => $request->address,
            'user_id' => $user->id,
            'sponsor_id' => $sponsorId,
            'referral_code' => $referralCode,
            'status' => 'pending',
        ]);
        
        // Update stock
        $product->decrement('stock_quantity', $request->quantity);
        if ($product->stock_quantity <= 0) {
            $product->update(['in_stock' => false]);
        }
        
        // Send SMS confirmation
        try {
            $smsService = app(SmsService::class);
            $message = "আপনার অর্ডার #{$order->order_number} গ্রহণ করা হয়েছে। মোট: ৳" . number_format($order->total_price, 0) . "। ধন্যবাদ!";
            $smsResult = $smsService->send($order->customer_phone, $message);
            
            if (!$smsResult['success']) {
                \Log::warning('Failed to send order confirmation SMS', [
                    'order_id' => $order->id,
                    'phone' => $order->customer_phone,
                    'error' => $smsResult['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('SMS sending exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            // Don't fail the order if SMS fails
        }
        
        return redirect()->route('orders.success', $order->order_number)
            ->with('success', 'Order placed successfully!');
    }
    
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('orders.success', compact('order'));
    }
    
    /**
     * Normalize and validate Bangladesh phone number
     * Formats: 01795560431 -> 8801795560431, 8801795560431 -> 8801795560431, +8801795560431 -> 8801795560431
     *
     * @param string $phone Phone number in any format
     * @return string Normalized phone number (880XXXXXXXXXX)
     * @throws \Exception If phone number is invalid
     */
    protected function normalizePhone($phone)
    {
        if (empty($phone)) {
            throw new \Exception('Phone number is required');
        }

        // Remove all non-numeric characters except + (we'll handle + separately)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = str_replace('+', '', $phone);
        
        // If it starts with 880, validate format
        if (strpos($phone, '880') === 0) {
            // Validate Bangladesh mobile format: 880 + 1 + 9 digits = 13 digits total
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it starts with 0, replace with 880
        if (strpos($phone, '0') === 0) {
            $phone = '880' . substr($phone, 1);
            // Validate: should be 13 digits total and 4th digit should be 1
            if (strlen($phone) === 13 && $phone[3] === '1') {
                return $phone;
            }
            throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
        }
        
        // If it doesn't start with 0 or 880, add 880 prefix
        $phone = '880' . $phone;
        // Validate: should be 13 digits total and 4th digit should be 1
        if (strlen($phone) === 13 && $phone[3] === '1') {
            return $phone;
        }
        
        throw new \Exception('Invalid Bangladesh phone number format. Please enter a valid 11-digit mobile number.');
    }
}
