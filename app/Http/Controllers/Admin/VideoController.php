<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $videos = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $categories = Video::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.videos.index', compact('videos', 'categories'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'youtube_link' => 'required|string|max:500',
            'thumbnail' => 'nullable|image|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        } elseif (!empty($validated['thumbnail_url'])) {
            $thumbnailPath = $validated['thumbnail_url'];
        }

        Video::create([
            'youtube_link' => $validated['youtube_link'],
            'thumbnail' => $thumbnailPath,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video added successfully.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'youtube_link' => 'required|string|max:500',
            'thumbnail' => 'nullable|image|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $thumbnailPath = $video->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail && !str_starts_with($video->thumbnail, 'http')) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('videos', 'public');
        } elseif (!empty($validated['thumbnail_url'])) {
            if ($video->thumbnail && !str_starts_with($video->thumbnail, 'http')) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $thumbnailPath = $validated['thumbnail_url'];
        }

        $video->update([
            'youtube_link' => $validated['youtube_link'],
            'thumbnail' => $thumbnailPath,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        if ($video->thumbnail && !str_starts_with($video->thumbnail, 'http')) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        $video->delete();
        return redirect()->route('admin.videos.index')->with('success', 'Video deleted successfully.');
    }
}
