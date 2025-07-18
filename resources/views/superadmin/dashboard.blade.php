<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Superadmin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-stat-card icon="fa-building" title="Total Companies" :value="$totalCompanies" :subtitle="'Active: <span class=\'font-semibold text-green-600\'>'.$activeCompanies.'</span> | Inactive: <span class=\'font-semibold text-red-600\'>'.$inactiveCompanies.'</span>'" color="blue" />
    <x-stat-card icon="fa-envelope" title="New Emails" :value="$newEmails" color="indigo" />
    <x-stat-card icon="fa-clock" title="Pending Emails" :value="$pendingEmails" color="yellow" />
    <x-stat-card icon="fa-users" title="Total Users" :value="$totalUsers" :subtitle="'Active: <span class=\'font-semibold text-green-600\'>'.$activeUsers.'</span> | Inactive: <span class=\'font-semibold text-red-600\'>'.$inactiveUsers.'</span>'" color="green" />
</div>


            <!-- Recent Companies -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Recent Companies</h3>
                @php
    $headers = ['Name', 'Status', 'Created', 'Actions'];
    $rows = $recentCompanies->map(function($company) {
        $adminUser = $company->users()->role('admin')->first();
        $nameCell = $adminUser
            ? '<span class="font-semibold">' . e($company->name) . '</span>'
            : '<span class="text-gray-400 cursor-not-allowed" title="No admin found for this company">' . e($company->name) . '</span>';
        $statusCell = $company->is_active
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-medium">Active</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-medium">Inactive</span>';
        $createdCell = e($company->created_at->diffForHumans());
        return [$nameCell, $statusCell, $createdCell, $adminUser ? $adminUser->id : null];
    });
@endphp
<x-table :headers="$headers" :rows="$rows" :actions="function($row) {
    if ($row[3]) {
        return view('components.button', [
            'type' => 'submit',
            'color' => 'blue',
            'icon' => 'fa-user-secret',
            'as' => 'form',
            'formAction' => route('impersonate.start', $row[3]),
            'formMethod' => 'POST',
            'title' => 'Impersonate admin',
            'slot' => '',
        ])->render();
    }
    return '';
}" empty="No recent companies found." />
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('companies.index') }}" class="bg-blue-50 hover:bg-blue-100 transition p-6 rounded-lg flex items-center shadow">
                    <i class="fas fa-building text-blue-600 text-2xl mr-4"></i>
                    <span class="font-medium text-blue-900">Manage Companies</span>
                </a>
                <a href="{{ route('users.index') }}" class="bg-green-50 hover:bg-green-100 transition p-6 rounded-lg flex items-center shadow">
                    <i class="fas fa-users text-green-600 text-2xl mr-4"></i>
                    <span class="font-medium text-green-900">Manage Users</span>
                </a>
                <a href="{{ route('email-logs.index') }}" class="bg-indigo-50 hover:bg-indigo-100 transition p-6 rounded-lg flex items-center shadow">
                    <i class="fas fa-envelope text-indigo-600 text-2xl mr-4"></i>
                    <span class="font-medium text-indigo-900">View Emails</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
