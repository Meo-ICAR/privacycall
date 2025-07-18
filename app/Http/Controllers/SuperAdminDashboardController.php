<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $inactiveCompanies = Company::where('is_active', false)->count();
        $newEmails = CompanyEmail::whereNull('read_at')->count();
        $pendingEmails = CompanyEmail::where('status', 'pending')->count();
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $recentCompanies = Company::latest()->take(5)->get();

        return view('superadmin.dashboard', compact(
            'totalCompanies',
            'activeCompanies',
            'inactiveCompanies',
            'newEmails',
            'pendingEmails',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'recentCompanies'
        ));
    }
}
