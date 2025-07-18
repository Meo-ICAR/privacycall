<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ImpersonationController extends Controller
{
    // Start impersonation
    public function start(Request $request, User $user)
    {
        $current = Auth::user();
        if (!$current->hasRole('superadmin')) {
            abort(403, 'Only superadmin can impersonate.');
        }
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'Cannot impersonate another superadmin.');
        }
        session(['impersonate_original_id' => $current->id]);
        Auth::login($user);
        return redirect('/')->with('success', 'Now impersonating ' . $user->name . '.');
    }

    // Stop impersonation
    public function stop(Request $request)
    {
        $originalId = session('impersonate_original_id');
        if ($originalId) {
            Auth::loginUsingId($originalId);
            session()->forget('impersonate_original_id');
            if (auth()->user()->hasRole('superadmin')) {
                return redirect()->route('companies.index')->with('success', 'Stopped impersonating.');
            }
            return redirect()->route('roles.permissions.index')->with('success', 'Stopped impersonating.');
        }
        return redirect()->route('roles.permissions.index')->with('error', 'Not impersonating anyone.');
    }

    // Logout or stop impersonation
    public function logoutOrStop(Request $request)
    {
        if (session('impersonate_original_id')) {
            $originalId = session('impersonate_original_id');
            Auth::loginUsingId($originalId);
            session()->forget('impersonate_original_id');
            return redirect()->route('superadmin.dashboard')->with('success', 'Stopped impersonating and returned to superadmin.');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // or your login page
    }
}
