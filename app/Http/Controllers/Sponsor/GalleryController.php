<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $sponsor = Auth::user();

        $targetUserId = $request->get('user_id');

        $query = GalleryPhoto::with(['user', 'uploader'])
            ->where(function ($q) use ($sponsor) {
                $q->where('uploaded_by_id', $sponsor->id)
                  ->orWhere('user_id', $sponsor->id)
                  ->orWhereIn('user_id', $sponsor->referrals()->pluck('id'));
            })
            ->latest();

        if ($targetUserId) {
            $allowedUserIds = $sponsor->referrals()->pluck('id')->push($sponsor->id);
            if ($allowedUserIds->contains((int) $targetUserId)) {
                $query->where('user_id', $targetUserId);
            }
        }

        $photos = $query->paginate(24)->withQueryString();

        $referrals = $sponsor->referrals()->orderBy('name')->get();

        return view('sponsor.gallery.index', compact('photos', 'sponsor', 'referrals', 'targetUserId'));
    }

    public function store(Request $request)
    {
        $sponsor = Auth::user();

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'caption' => 'nullable|string|max:255',
        ]);

        $targetUser = User::findOrFail($request->user_id);

        if ($targetUser->id !== $sponsor->id && $targetUser->sponsor_id !== $sponsor->id) {
            abort(403, 'You can only upload photos for yourself or your referred users.');
        }

        try {
            $path = \App\Services\ImageResizeService::resizeAndStore(
                $request->file('photo'),
                'gallery',
                800,
                800,
                85
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload photo. ' . $e->getMessage());
        }

        GalleryPhoto::create([
            'user_id' => $targetUser->id,
            'uploaded_by_id' => $sponsor->id,
            'path' => $path,
            'caption' => $request->caption,
        ]);

        return redirect()
            ->route('sponsor.gallery.index', ['user_id' => $targetUser->id])
            ->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(GalleryPhoto $photo)
    {
        $sponsor = Auth::user();

        if ($photo->uploaded_by_id !== $sponsor->id && $photo->user_id !== $sponsor->id) {
            abort(403, 'You are not allowed to delete this photo.');
        }

        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return redirect()->back()->with('success', 'Photo deleted successfully.');
    }
}

