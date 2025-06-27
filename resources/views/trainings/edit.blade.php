@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Training</h1>
        <p class="mt-2 text-gray-600">Update training session details</p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('trainings.update', $training) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Training Title *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $training->title) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Training Type *</label>
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select training type</option>
                            <option value="online" {{ old('type', $training->type) == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="in_person" {{ old('type', $training->type) == 'in_person' ? 'selected' : '' }}>In Person</option>
                            <option value="hybrid" {{ old('type', $training->type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Training Date</label>
                        <input type="date" id="date" name="date" value="{{ old('date', $training->date ? $training->date->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                        <input type="text" id="duration" name="duration" value="{{ old('duration', $training->duration) }}" placeholder="e.g., 2 hours, 1 day" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="provider" class="block text-sm font-medium text-gray-700">Training Provider</label>
                        <input type="text" id="provider" name="provider" value="{{ old('provider', $training->provider) }}" placeholder="e.g., Training Institute, Internal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('provider')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $training->location) }}" placeholder="e.g., Conference Room A, Zoom Meeting" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Organized By Customer (Optional)</label>
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $training->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $training->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Training</label>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Provide a detailed description of the training content and objectives" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $training->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Any additional notes or special requirements" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $training->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('trainings.show', $training) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Update Training
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
