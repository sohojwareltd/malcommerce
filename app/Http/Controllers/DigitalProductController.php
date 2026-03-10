<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DigitalProductController extends Controller
{

    /**
     * Download digital file for an order (auth required, must own order).
     */
    public function download(Request $request, Order $order)
    {
        $request->validate(['order_id' => 'sometimes|exists:orders,id']);

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to download.');
        }
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }
        if (!$order->canAccessDigitalContent()) {
            return back()->with('error', 'This content is not available yet. It will be available after payment is confirmed or order is processed.');
        }

        $product = $order->product;
        if (!$product->hasDigitalFile()) {
            return back()->with('error', 'This product does not have a downloadable file.');
        }

        $path = $product->digital_file_path;
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File not found. Please contact support.');
        }

        $filename = basename($path);
        return Storage::disk('public')->download($path, $filename);
    }

    /**
     * Show link/text for a digital order (auth required, must own order).
     */
    public function showLink(Request $request, Order $order)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view your content.');
        }
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }
        if (!$order->canAccessDigitalContent()) {
            return back()->with('error', 'This content is not available yet. It will be available after payment is confirmed or order is processed.');
        }

        $product = $order->product;
        if (!$product->hasDigitalLink()) {
            return back()->with('error', 'This product does not have link or text content.');
        }

        return view('digital.link', [
            'order' => $order,
            'product' => $product,
            'digitalLinkText' => $product->digital_link_text,
        ]);
    }
}
