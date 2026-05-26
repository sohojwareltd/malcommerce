<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('type') === 'digital') {
            return redirect()->route('courses.index');
        }

        $categories = Category::where('is_active', true)->get();

        $query = Product::where('is_active', true)->where('is_digital', false);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $physicalProducts = $query->orderBy('sort_order')->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('physicalProducts', 'categories'));
    }
    
    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_digital', false)
            ->firstOrFail();
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }
}
