<?php

namespace App\Http\Controllers;

use App\Models\JobCircular;
use App\Models\Order;
use App\Models\Product;
use App\Models\Video;
use App\Models\WorkshopSeminar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->where('only_on_categories', false)
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->get();

        $featuredJobs = JobCircular::where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $featuredWorkshops = WorkshopSeminar::where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('event_date')->orWhere('event_date', '>=', now()->toDateString());
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $featuredVideos = Video::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $recentDigitalOrders = null;

        if (Auth::check()) {
            $recentDigitalOrders = Order::with('product')
                ->where('user_id', Auth::id())
                ->whereHas('product', fn ($q) => $q->where('is_digital', true))
                ->where(function ($q) {
                    // Match access rules from Order::canAccessDigitalContent()
                    $q->where(function ($q) {
                        $q->where('payment_method', 'bkash')
                            ->where('payment_status', 'completed');
                    })->orWhere(function ($q) {
                        $q->where('payment_method', '!=', 'bkash')
                            ->whereIn('status', ['processing', 'shipped', 'delivered']);
                    });
                })
                ->orderByDesc('created_at')
                ->take(4)
                ->get();
        }

        return view('home', compact(
            'products',
            'featuredProducts',
            'featuredJobs',
            'featuredWorkshops',
            'featuredVideos',
            'recentDigitalOrders'
        ));
    }
}
