<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Use different path based on upload type if specified, otherwise default to sections
            $uploadType = $request->input('type', 'sections'); // 'sections' or 'products'
            $path = $image->storeAs('products/' . $uploadType, $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::disk('public')->url($path),
                'path' => $path,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No image uploaded',
        ], 400);
    }
}
