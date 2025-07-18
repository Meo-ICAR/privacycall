@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('suppliers.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Supplier</h1>
                <p class="mt-2 text-gray-600">Update supplier details and information</p>
            </div>
        </div>
    </div>

    <!-- Supplier Edit Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="supplierEditForm" class="space-y-8" enctype="multipart/form-data" method="POST" action="{{ route('suppliers.update', $supplier->id) }}">
                @csrf
                @method('PUT')

                <!-- Hidden company_id for admin users -->
                @if(!auth()->user()->hasRole('superadmin'))
                    <input type="hidden" name="company_id" value="{{ $supplier->company_id }}">
                @endif

                <!-- Basic Information Section -->
                <div x-data="{ open: true }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Basic Information
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Logo Upload -->
                        <div class="sm:col-span-2">
                            <label for="logo" class="block text-sm font-medium text-gray-700">Supplier Logo</label>
                            <input type="file" name="logo" id="logo" accept="image/*"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <div class="mt-2">
                                <img id="logoPreview" src="{{ $supplier->logo_url ?? '#' }}" alt="Logo Preview" class="h-24 w-24 object-contain rounded border border-gray-200 {{ !$supplier->logo_url ? 'hidden' : '' }}" />
                            </div>
                        </div>

                        <!-- Supplier Number -->
                        <div>
                            <label for="supplier_number" class="block text-sm font-medium text-gray-700">Supplier Number</label>
                            <input type="text" name="supplier_number" id="supplier_number" value="{{ old('supplier_number', $supplier->supplier_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter supplier number">
                            @error('supplier_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Supplier Name *</label>
                            <input type="text" name="name" id="name" required value="{{ old('name', $supplier->name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter supplier name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Legal Name -->
                        <div>
                            <label for="legal_name" class="block text-sm font-medium text-gray-700">Legal Name</label>
                            <input type="text" name="legal_name" id="legal_name" value="{{ old('legal_name', $supplier->legal_name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter legal name">
                            @error('legal_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Registration Number -->
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-gray-700">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number', $supplier->registration_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter registration number">
                            @error('registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- VAT Number -->
                        <div>
                            <label for="vat_number" class="block text-sm font-medium text-gray-700">VAT Number</label>
                            <input type="text" name="vat_number" id="vat_number" value="{{ old('vat_number', $supplier->vat_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter VAT number">
                            @error('vat_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Type -->
                        <div>
                            <label for="supplier_type_id" class="block text-sm font-medium text-gray-700">Supplier Type *</label>
                            <select name="supplier_type_id" id="supplier_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">Select Type</option>
                                @foreach($supplierTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('supplier_type_id', $supplier->supplier_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Category -->
                        <div>
                            <label for="supplier_category" class="block text-sm font-medium text-gray-700">Supplier Category</label>
                            <select name="supplier_category" id="supplier_category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Category</option>
                                <option value="primary" {{ old('supplier_category', $supplier->supplier_category) == 'primary' ? 'selected' : '' }}>Primary</option>
                                <option value="secondary" {{ old('supplier_category', $supplier->supplier_category) == 'secondary' ? 'selected' : '' }}>Secondary</option>
                                <option value="emergency" {{ old('supplier_category', $supplier->supplier_category) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('supplier_category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Status -->
                        <div>
                            <label for="supplier_status" class="block text-sm font-medium text-gray-700">Supplier Status</label>
                            <select name="supplier_status" id="supplier_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('supplier_status', $supplier->supplier_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('supplier_status', $supplier->supplier_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('supplier_status', $supplier->supplier_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="approved" {{ old('supplier_status', $supplier->supplier_status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ old('supplier_status', $supplier->supplier_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                            @error('supplier_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier Since -->
                        <div>
                            <label for="supplier_since" class="block text-sm font-medium text-gray-700">Supplier Since</label>
                            <input type="date" name="supplier_since" id="supplier_since" value="{{ old('supplier_since', $supplier->supplier_since ? $supplier->supplier_since->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('supplier_since')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Contact Information
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Contact Person Name -->
                        <div>
                            <label for="contact_person_name" class="block text-sm font-medium text-gray-700">Contact Person Name</label>
                            <input type="text" name="contact_person_name" id="contact_person_name" value="{{ old('contact_person_name', $supplier->contact_person_name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter contact person name">
                            @error('contact_person_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Person Email -->
                        <div>
                            <label for="contact_person_email" class="block text-sm font-medium text-gray-700">Contact Person Email</label>
                            <input type="email" name="contact_person_email" id="contact_person_email" value="{{ old('contact_person_email', $supplier->contact_person_email) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter contact person email">
                            @error('contact_person_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Person Phone -->
                        <div>
                            <label for="contact_person_phone" class="block text-sm font-medium text-gray-700">Contact Person Phone</label>
                            <input type="text" name="contact_person_phone" id="contact_person_phone" value="{{ old('contact_person_phone', $supplier->contact_person_phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter contact person phone">
                            @error('contact_person_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- General Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">General Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter general email">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- General Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">General Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter general phone">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="url" name="website" id="website" value="{{ old('website', $supplier->website) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="https://example.com">
                            @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Address Information
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Address Line 1 -->
                        <div class="sm:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                            <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $supplier->address_line_1) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter address line 1">
                            @error('address_line_1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address Line 2 -->
                        <div class="sm:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                            <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $supplier->address_line_2) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter address line 2">
                            @error('address_line_2')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $supplier->city) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter city">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                            <input type="text" name="state" id="state" value="{{ old('state', $supplier->state) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter state/province">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $supplier->postal_code) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter postal code">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <input type="text" name="country" id="country" value="{{ old('country', $supplier->country) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter country">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Business Information Section -->
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Business Information
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Payment Terms -->
                        <div>
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                            <input type="text" name="payment_terms" id="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="e.g., Net 30, Net 60">
                            @error('payment_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Credit Limit -->
                        <div>
                            <label for="credit_limit" class="block text-sm font-medium text-gray-700">Credit Limit</label>
                            <input type="number" step="0.01" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter credit limit">
                            @error('credit_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Order Date -->
                        <div>
                            <label for="last_order_date" class="block text-sm font-medium text-gray-700">Last Order Date</label>
                            <input type="date" name="last_order_date" id="last_order_date" value="{{ old('last_order_date', $supplier->last_order_date ? $supplier->last_order_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('last_order_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Total Orders -->
                        <div>
                            <label for="total_orders" class="block text-sm font-medium text-gray-700">Total Orders</label>
                            <input type="number" name="total_orders" id="total_orders" value="{{ old('total_orders', $supplier->total_orders) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter total orders">
                            @error('total_orders')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </button>
                    <div x-show="open" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- GDPR Consent Date -->
                        <div>
                            <label for="gdpr_consent_date" class="block text-sm font-medium text-gray-700">GDPR Consent Date</label>
                            <input type="datetime-local" name="gdpr_consent_date" id="gdpr_consent_date"
                                   value="{{ old('gdpr_consent_date', $supplier->gdpr_consent_date ? $supplier->gdpr_consent_date->format('Y-m-d\TH:i') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('gdpr_consent_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data Processing Purpose -->
                        <div class="sm:col-span-2">
                            <label for="data_processing_purpose" class="block text-sm font-medium text-gray-700">Data Processing Purpose</label>
                            <textarea name="data_processing_purpose" id="data_processing_purpose" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Describe the purpose of data processing">{{ old('data_processing_purpose', $supplier->data_processing_purpose) }}</textarea>
                            @error('data_processing_purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data Retention Period -->
                        <div>
                            <label for="data_retention_period" class="block text-sm font-medium text-gray-700">Data Retention Period (days)</label>
                            <input type="number" name="data_retention_period" id="data_retention_period" value="{{ old('data_retention_period', $supplier->data_retention_period) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Enter retention period in days">
                            @error('data_retention_period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GDPR Consent Checkboxes -->
                        <div class="sm:col-span-2">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="data_processing_consent" id="data_processing_consent" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('data_processing_consent', $supplier->data_processing_consent) ? 'checked' : '' }}>
                                    <label for="data_processing_consent" class="ml-2 block text-sm text-gray-900">Data Processing Consent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="third_party_sharing_consent" id="third_party_sharing_consent" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('third_party_sharing_consent', $supplier->third_party_sharing_consent) ? 'checked' : '' }}>
                                    <label for="third_party_sharing_consent" class="ml-2 block text-sm text-gray-900">Third Party Sharing Consent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="data_retention_consent" id="data_retention_consent" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('data_retention_consent', $supplier->data_retention_consent) ? 'checked' : '' }}>
                                    <label for="data_retention_consent" class="ml-2 block text-sm text-gray-900">Data Retention Consent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="data_processing_agreement_signed" id="data_processing_agreement_signed" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('data_processing_agreement_signed', $supplier->data_processing_agreement_signed) ? 'checked' : '' }}>
                                    <label for="data_processing_agreement_signed" class="ml-2 block text-sm text-gray-900">Data Processing Agreement Signed</label>
                                </div>
                            </div>
                        </div>

                        <!-- Data Processing Agreement Date -->
                        <div>
                            <label for="data_processing_agreement_date" class="block text-sm font-medium text-gray-700">DPA Signed Date</label>
                            <input type="datetime-local" name="data_processing_agreement_date" id="data_processing_agreement_date"
                                   value="{{ old('data_processing_agreement_date', $supplier->data_processing_agreement_date ? $supplier->data_processing_agreement_date->format('Y-m-d\TH:i') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('data_processing_agreement_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Protection Officer Section -->
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Data Protection Officer
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="data_protection_officer" class="block text-sm font-medium text-gray-700">Data Protection Officer</label>
                            <input type="text" name="data_protection_officer" id="data_protection_officer" value="{{ old('data_protection_officer', $supplier->data_protection_officer) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter DPO name">
                        </div>
                        <div>
                            <label for="dpo_contact_email" class="block text-sm font-medium text-gray-700">DPO Contact Email</label>
                            <input type="email" name="dpo_contact_email" id="dpo_contact_email" value="{{ old('dpo_contact_email', $supplier->dpo_contact_email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter DPO email">
                        </div>
                        <div>
                            <label for="dpo_contact_phone" class="block text-sm font-medium text-gray-700">DPO Contact Phone</label>
                            <input type="text" name="dpo_contact_phone" id="dpo_contact_phone" value="{{ old('dpo_contact_phone', $supplier->dpo_contact_phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter DPO phone">
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Third Country Associations -->
                <div x-data="{ open: false }" class="border-b border-gray-200 pb-6 mb-4">
                    <button type="button" @click="open = !open" class="w-full text-left flex items-center justify-between text-lg font-medium text-gray-900 mb-4 focus:outline-none">
                        Third Country Associations
                        <svg :class="{'rotate-180': open}" class="h-5 w-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open">
                        <div>
                            <label for="third_countries" class="block text-sm font-medium text-gray-700">Associated Third Countries</label>
                        <select name="third_countries[]" id="third_countries" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($thirdCountries as $country)
                                <option value="{{ $country->id }}" {{ in_array($country->id, $supplier->thirdCountries->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="reasons-container" class="mt-4 space-y-4">
                        @foreach($supplier->thirdCountries as $country)
                            <div id="reason-for-{{$country->id}}">
                                <label for="reasons[{{$country->id}}]" class="block text-sm font-medium text-gray-700">Reason for {{ $country->country_name }}</label>
                                <textarea name="reasons[{{$country->id}}]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $country->pivot->reason }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status and Notes Section -->
                <div class="pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status and Notes</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Active Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Supplier</label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Enter any additional notes about the supplier">{{ old('notes', $supplier->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Logo preview
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');
    logoInput.addEventListener('change', function(e) {
        const [file] = logoInput.files;
        if (file) {
            logoPreview.src = URL.createObjectURL(file);
            logoPreview.classList.remove('hidden');
        } else {
            logoPreview.src = '#';
            logoPreview.classList.add('hidden');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const thirdCountriesSelect = document.getElementById('third_countries');
        const reasonsContainer = document.getElementById('reasons-container');
        const allCountries = @json($thirdCountries->keyBy('id'));

        thirdCountriesSelect.addEventListener('change', function() {
            const selectedIds = Array.from(thirdCountriesSelect.selectedOptions).map(option => option.value);

            // Remove reason fields for unselected countries
            Array.from(reasonsContainer.children).forEach(child => {
                const countryId = child.id.replace('reason-for-', '');
                if (!selectedIds.includes(countryId)) {
                    child.remove();
                }
            });

            // Add reason fields for newly selected countries
            selectedIds.forEach(countryId => {
                if (!document.getElementById(`reason-for-${countryId}`)) {
                    const country = allCountries[countryId];
                    const reasonDiv = document.createElement('div');
                    reasonDiv.id = `reason-for-${countryId}`;
                    reasonDiv.innerHTML = `
                        <label for="reasons[${countryId}]" class="block text-sm font-medium text-gray-700">Reason for ${country.country_name}</label>
                        <textarea name="reasons[${countryId}]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    `;
                    reasonsContainer.appendChild(reasonDiv);
                }
            });
        });
    });
</script>
@endsection
