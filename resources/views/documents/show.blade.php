@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $document->title }}</h1>
        <p class="mt-2 text-gray-600">Document details and information</p>
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
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Document Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Document Information</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->documentType->name ?? 'Unknown' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->company->name ?? 'Unknown' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Upload Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->created_at ? $document->created_at->format('Y-m-d H:i') : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->file_size ? number_format($document->file_size / 1024, 2) . ' KB' : 'N/A' }}</dd>
                        </div>
                        @if($document->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->description }}</dd>
                        </div>
                        @endif
                        @if($document->tags)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tags</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->tags }}</dd>
                        </div>
                        @endif
                        @if($document->expiry_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $document->expiry_date }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- File Preview -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">File Preview</h3>
                    <div class="border-2 border-gray-300 border-dashed rounded-lg p-6 text-center">
                        <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                        <p class="text-sm text-gray-600 mb-4">{{ $document->file_name }}</p>
                        <a href="{{ Storage::url($document->file_path) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700" target="_blank">
                            <i class="fas fa-download mr-2"></i>
                            Download
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('documents.edit', $document) }}" class="inline-flex items-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50">
                    Edit
                </a>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50" onclick="return confirm('Are you sure you want to delete this document?')">
                        Delete
                    </button>
                </form>
                <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
