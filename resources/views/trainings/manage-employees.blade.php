@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Manage Training Employees</h1>
                        <p class="text-gray-600 mt-2">{{ $training->title }}</p>
                    </div>
                    <a href="{{ route('trainings.show', $training) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Training
                    </a>
                </div>

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

                <!-- Training Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Training Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Type:</span>
                            <span class="ml-2 text-gray-900">{{ ucfirst(str_replace('_', ' ', $training->type)) }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Date:</span>
                            <span class="ml-2 text-gray-900">{{ $training->date ? $training->date->format('M d, Y') : 'Not scheduled' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Duration:</span>
                            <span class="ml-2 text-gray-900">{{ $training->duration ?? 'Not specified' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Employee Selection Form -->
                <form action="{{ route('trainings.update-employees', $training) }}" method="POST">
                    @csrf

                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Select Employees</h3>
                            <p class="text-sm text-gray-600 mt-1">Check the employees you want to assign to this training</p>
                        </div>

                        <div class="p-6">
                            @if($employees->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($employees as $employee)
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                            <input type="checkbox"
                                                   name="employee_ids[]"
                                                   value="{{ $employee->id }}"
                                                   id="employee_{{ $employee->id }}"
                                                   {{ in_array($employee->id, $assignedEmployeeIds) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="employee_{{ $employee->id }}" class="ml-3 flex-1 cursor-pointer">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $employee->position }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $employee->email }}
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500">No employees found in your company.</p>
                                    <a href="{{ route('employees.create') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Employee
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($employees->count() > 0)
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                <span id="selected-count">0</span> of {{ $employees->count() }} employees selected
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" id="select-all" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Select All
                                </button>
                                <button type="button" id="deselect-all" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Deselect All
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="employee_ids[]"]');
    const selectAllBtn = document.getElementById('select-all');
    const deselectAllBtn = document.getElementById('deselect-all');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('input[name="employee_ids[]"]:checked').length;
        selectedCountSpan.textContent = checkedCount;
    }

    // Update count when checkboxes change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Select all functionality
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Deselect all functionality
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Initialize count
    updateSelectedCount();
});
</script>
@endsection
