<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function form()
    {
        return view('admin.test-email');
    }

    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
        ]);

        try {
            Mail::raw('Test email from Laravel superadmin panel.', function ($message) use ($request) {
                $message->to($request->to)
                        ->subject('Test Email');
            });

            return back()->with('success', 'Test email sent to ' . $request->to);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }
}
