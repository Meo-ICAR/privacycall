@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route("supplier-inspections.index") }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Supplier Inspection Details</h1>
                    <p class="mt-2 text-gray-600">View and manage inspection information</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route("supplier-inspections.edit", $inspection) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Inspection
                </a>
                <form action="{{ route("supplier-inspections.destroy", $inspection) }}" method="POST" class="inline" onsubmit="return confirm(\"Are you sure you want to delete this inspection?\")">
                    @csrf
                    @method("DELETE")
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Inspection Information</h3>
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Company</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $inspection->company->name }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $inspection->supplier->name }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Inspection Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $inspection->inspection_date }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspection->status) }}</dd>
                </div>
            </dl>
            
            @if($inspection->notes)
            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Notes</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700">{{ $inspection->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
