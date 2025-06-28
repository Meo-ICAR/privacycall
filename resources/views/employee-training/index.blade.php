@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Employee Training</h1>
                        <p class="text-gray-600 mt-2">Manage employee training assignments and track progress</p>
                    </div>
                    <a href="{{ route('employee-training.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Assign Training
                    </a>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Total Employees</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $stats['total_employees'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-graduation-cap text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Total Trainings</p>
                                <p class="text-2xl font-bold text-green-900">{{ $stats['total_trainings'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-link text-purple-600 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Total Assignments</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $stats['total_assignments'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-yellow-600 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Completed</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $stats['completed_trainings'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <form method="GET" action="{{ route('employee-training.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                            <select name="employee_id" id="employee_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="training_id" class="block text-sm font-medium text-gray-700 mb-1">Training</label>
                            <select name="training_id" id="training_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Trainings</option>
                                @foreach($trainings as $training)
                                    <option value="{{ $training->id }}" {{ $trainingId == $training->id ? 'selected' : '' }}>
                                        {{ $training->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="attended" {{ $status === 'attended' ? 'selected' : '' }}>Attended</option>
                                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="not_attended" {{ $status === 'not_attended' ? 'selected' : '' }}>Not Attended</option>
                                <option value="not_completed" {{ $status === 'not_completed' ? 'selected' : '' }}>Not Completed</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                            @if(request()->hasAny(['employee_id', 'training_id', 'status']))
                                <a href="{{ route('employee-training.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Employee Training Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Employee
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Training
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notes
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employeesWithTrainings as $employee)
                                    @forelse($employee->trainings as $training)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-blue-800">
                                                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $employee->position }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $training->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $training->type }}</div>
                                                @if($training->date)
                                                    <div class="text-xs text-gray-400">{{ $training->date->format('M d, Y') }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    @if($training->pivot->attended)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check mr-1"></i>Attended
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-times mr-1"></i>Not Attended
                                                        </span>
                                                    @endif
                                                    @if($training->pivot->completed)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-graduation-cap mr-1"></i>Completed
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i>In Progress
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($training->pivot->score !== null)
                                                    <span class="font-medium">{{ $training->pivot->score }}%</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if($training->pivot->notes)
                                                    <div class="max-w-xs truncate" title="{{ $training->pivot->notes }}">
                                                        {{ $training->pivot->notes }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('employee-training.edit', $employee->id . '-' . $training->id) }}"
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('employee-training.destroy', $employee->id . '-' . $training->id) }}"
                                                          method="POST"
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to remove this training assignment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        @if($loop->first)
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                    No training assignments found for this employee.
                                                </td>
                                            </tr>
                                        @endif
                                    @endforelse
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No employee training records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
