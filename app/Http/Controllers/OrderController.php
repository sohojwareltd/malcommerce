<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
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
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'district' => 'required|string|max:255',
            'upazila' => 'required|string|max:255',
            'city_village' => 'required|string|max:255',
            'post_code' => 'required|string|max:10',
            'address' => 'required|string',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        if ($product->stock_quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Insufficient stock available.'])->withInput();
        }
        
        // Get referral code from session (set by middleware)
        $referralCode = Session::get('referral_code');
        $sponsorId = null;
        
        if ($referralCode) {
            $sponsor = \App\Models\User::where('affiliate_code', $referralCode)->first();
            if ($sponsor) {
                $sponsorId = $sponsor->id;
            }
        }
        
        $order = Order::create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $request->quantity,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'district' => $request->district,
            'upazila' => $request->upazila,
            'city_village' => $request->city_village,
            'post_code' => $request->post_code,
            'address' => $request->address,
            'sponsor_id' => $sponsorId,
            'referral_code' => $referralCode,
            'status' => 'pending',
        ]);
        
        // Update stock
        $product->decrement('stock_quantity', $request->quantity);
        if ($product->stock_quantity <= 0) {
            $product->update(['in_stock' => false]);
        }
        
        // TODO: Send SMS with invitation link
        // This would integrate with an SMS service
        
        return redirect()->route('orders.success', $order->order_number)
            ->with('success', 'Order placed successfully!');
    }
    
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('orders.success', compact('order'));
    }
}
