@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Email Template</h1>
        <p class="mt-2 text-gray-600">Update the email template "{{ $emailTemplate->name }}"</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Template Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $emailTemplate->name) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company -->
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700">Company (Optional - leave empty for global template)</label>
                        <select name="company_id" id="company_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Global Template</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $emailTemplate->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" id="category" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="general" {{ old('category', $emailTemplate->category) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="supplier" {{ old('category', $emailTemplate->category) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="customer" {{ old('category', $emailTemplate->category) == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="employee" {{ old('category', $emailTemplate->category) == 'employee' ? 'selected' : '' }}>Employee</option>
                            <option value="notification" {{ old('category', $emailTemplate->category) == 'notification' ? 'selected' : '' }}>Notification</option>
                            <option value="gdpr" {{ old('category', $emailTemplate->category) == 'gdpr' ? 'selected' : '' }}>GDPR</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="sm:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject', $emailTemplate->subject) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Body -->
                    <div class="sm:col-span-2">
                        <label for="body" class="block text-sm font-medium text-gray-700">Email Body</label>
                        <textarea name="body" id="body" rows="12" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('body', $emailTemplate->body) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Use variables like @{{supplier_name}}, @{{company_name}}, @{{user_name}}, etc. in your template.
                        </p>
                        @error('body')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Available Variables -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Available Variables</label>
                        <div class="mt-1 bg-gray-50 p-4 rounded-md">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <strong>Basic Variables:</strong>
                                    <ul class="mt-1 space-y-1">
                                        <li><code>@{{supplier_name}}</code> - Supplier Name</li>
                                        <li><code>@{{supplier_email}}</code> - Supplier Email</li>
                                        <li><code>@{{company_name}}</code> - Your Company Name</li>
                                        <li><code>@{{user_name}}</code> - Current User Name</li>
                                        <li><code>@{{current_date}}</code> - Current Date</li>
                                    </ul>
                                </div>
                                <div>
                                    <strong>Additional Variables:</strong>
                                    <ul class="mt-1 space-y-1">
                                        <li><code>@{{custom_message}}</code> - Custom Message</li>
                                        <li><code>@{{recipient_name}}</code> - Recipient Name</li>
                                        <li><code>@{{recipient_email}}</code> - Recipient Email</li>
                                        <li><code>@{{template_name}}</code> - Template Name</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active Template
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Only active templates can be used for sending emails.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('email-templates.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
