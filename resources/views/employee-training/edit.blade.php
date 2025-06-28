@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <a href="{{ route('employee-training.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Update Training Assignment</h1>
                            <p class="mt-2 text-gray-600">Update training progress and details</p>
                        </div>
                    </div>
                </div>

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('employee-training.update', $id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="attended" id="attended" value="1" {{ old('attended', $assignment->pivot->attended) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="attended" class="ml-2 block text-sm text-gray-900">
                                Employee attended the training
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="completed" id="completed" value="1" {{ old('completed', $assignment->pivot->completed) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="completed" class="ml-2 block text-sm text-gray-900">
                                Training completed successfully
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700">Score (%)</label>
                        <input type="number" name="score" id="score" value="{{ old('score', $assignment->pivot->score) }}" min="0" max="100" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('notes', $assignment->pivot->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('employee-training.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>
                            Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
