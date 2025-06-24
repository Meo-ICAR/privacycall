@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Create Customer Inspection</h2>
    <form method="POST" action="{{ route('customer-inspections.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold">Company</label>
            <input type="text" class="form-input w-full bg-gray-100" value="{{ $company->name }}" readonly>
            <input type="hidden" name="company_id" value="{{ $company->id }}">
        </div>
        <div class="mb-4">
            <label for="customer_id" class="block font-semibold">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select w-full" required>
                <option value="">Select a customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="inspection_date" class="block font-semibold">Inspection Date</label>
            <input type="date" name="inspection_date" id="inspection_date" class="form-input w-full" required>
        </div>
        <div class="mb-4">
            <label for="notes" class="block font-semibold">Notes</label>
            <textarea name="notes" id="notes" class="form-textarea w-full"></textarea>
        </div>
        <div class="mb-4">
            <label for="status" class="block font-semibold">Status</label>
            <select name="status" id="status" class="form-select w-full" required>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
            </select>
        </div>
        <div class="mb-6">
            <label class="block font-semibold mb-2">Employees</label>
            <div id="employees-list">
                @foreach($employees as $employee)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="employees[{{ $employee->id }}][id]" value="{{ $employee->id }}" class="mr-2">
                        <span class="mr-2">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->position }})</span>
                        <input type="text" name="employees[{{ $employee->id }}][position]" placeholder="Position" class="form-input mr-2" value="{{ $employee->position }}">
                        <input type="date" name="employees[{{ $employee->id }}][hire_date]" placeholder="Hire Date" class="form-input" value="{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '' }}">
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Inspection</button>
    </form>
</div>
@endsection
