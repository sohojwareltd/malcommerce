<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function loginAsSponsor(Request $request, User $sponsor): RedirectResponse
    {
        if ($sponsor->role !== 'sponsor' || $sponsor->trashed()) {
            abort(404);
        }

        $request->session()->put('impersonator_id', $request->user()->id);

        Auth::login($sponsor);
        $request->session()->regenerate();

        return redirect()->route('sponsor.dashboard');
    }

    public function leave(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (! $impersonatorId) {
            return redirect()->route('sponsor.dashboard')
                ->with('error', 'You are not viewing the dashboard as an admin.');
        }

        $admin = User::find($impersonatorId);

        if (! $admin || ! $admin->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('error', 'Your admin session could not be restored. Please sign in again.');
        }

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
