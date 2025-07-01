<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index()
    {
        $emailLogs = EmailLog::tenant()->paginate(20);
        return view('email_logs.index', compact('emailLogs'));
    }
}
