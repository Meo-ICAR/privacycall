@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Edit Employer Type</h1>
    <form action="{{ route('employer-types.update', $employerType) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ $employerType->name }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('name')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700">Icon (FontAwesome class or image URL)</label>
            <input type="text" name="icon" id="icon" value="{{ $employerType->icon }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('icon')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="flex justify-end space-x-2">
            <a href="{{ route('employer-types.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
@endsection
