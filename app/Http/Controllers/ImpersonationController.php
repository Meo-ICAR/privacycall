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
            return redirect('/')->with('success', 'Stopped impersonating.');
        }
        return redirect('/')->with('error', 'Not impersonating anyone.');
    }
}
