@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Roles & Permissions Management</h1>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <div class="bg-white shadow rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200 mb-8">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->getRoleNames()->join(', ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $user->getPermissionNames()->join(', ') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="document.getElementById('edit-modal-{{ $user->id }}').classList.remove('hidden')" class="text-blue-600 hover:text-blue-900">Edit</button>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div id="edit-modal-{{ $user->id }}" class="fixed z-10 inset-0 overflow-y-auto hidden">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full p-6 z-50">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Edit Roles & Permissions for {{ $user->name }}</h3>
                            <form method="POST" action="{{ route('roles.permissions.assign') }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($roles as $role)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" @if($user->hasRole($role->name)) checked @endif class="form-checkbox">
                                                <span class="ml-2">{{ ucfirst($role->name) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($permissions as $permission)
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @if($user->hasPermissionTo($permission->name)) checked @endif class="form-checkbox">
                                                <span class="ml-2">{{ ucfirst($permission->name) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="document.getElementById('edit-modal-{{ $user->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
