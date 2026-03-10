<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, physical, digital
        $categories = Category::where('is_active', true)->get();

        $baseQuery = function () use ($request) {
            $q = Product::where('is_active', true);
            if ($request->filled('category')) {
                $q->where('category_id', $request->category);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%');
                });
            }
            return $q->orderBy('sort_order')->orderBy('created_at', 'desc');
        };

        $physicalProducts = null;
        $digitalProducts = null;

        if ($type === 'physical') {
            $physicalProducts = $baseQuery()->where('is_digital', false)->paginate(12)->withQueryString();
        } elseif ($type === 'digital') {
            $digitalProducts = $baseQuery()->where('is_digital', true)->paginate(12)->withQueryString();
        } else {
            $physicalProducts = $baseQuery()->where('is_digital', false)->take(12)->get();
            $digitalProducts = $baseQuery()->where('is_digital', true)->take(12)->get();
        }

        return view('products.index', compact('physicalProducts', 'digitalProducts', 'categories', 'type'));
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }
}
