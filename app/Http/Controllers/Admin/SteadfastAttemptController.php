<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SteadfastAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SteadfastAttemptController extends Controller
{
    public function index(Request $request): View
    {
        $query = SteadfastAttempt::query()->with('order:id,order_number,total_price');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('success')) {
            $query->where('success', $request->boolean('success'));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('notification_type', 'like', "%{$s}%")
                    ->orWhere('error_message', 'like', "%{$s}%")
                    ->orWhere('ip_address', 'like', "%{$s}%")
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$s}%"));
            });
        }

        $attempts = $query->latest()->paginate(30)->withQueryString();

        return view('admin.steadfast-attempts.index', compact('attempts'));
    }

    public function show(SteadfastAttempt $steadfastAttempt): View
    {
        $steadfastAttempt->load('order');
        return view('admin.steadfast-attempts.show', ['attempt' => $steadfastAttempt]);
    }
}
