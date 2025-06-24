@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Customers</h1>
    <div class="flex justify-between mb-4">
        <a href="{{ route('customers.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export to Excel</a>
        <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls" required class="border rounded p-1">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Import from Excel</button>
        </form>
    </div>
    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">ID</th>
                <th class="px-4 py-2 border-b">First Name</th>
                <th class="px-4 py-2 border-b">Last Name</th>
                <th class="px-4 py-2 border-b">Email</th>
                <th class="px-4 py-2 border-b">Phone</th>
                <th class="px-4 py-2 border-b">Customer Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $customer->id }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->first_name }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->last_name }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->email }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->phone }}</td>
                    <td class="px-4 py-2 border-b">{{ $customer->customer_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
