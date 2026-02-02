<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $videos = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = Video::where('is_active', true)->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('videos.index', compact('videos', 'categories'));
    }
}
