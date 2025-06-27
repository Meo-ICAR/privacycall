@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Supplier Mail Merge</h1>
        <p class="mt-2 text-gray-600">Send personalized emails to multiple suppliers at once</p>
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

    <form id="mailMergeForm" method="POST" action="{{ route('supplier-mail-merge.send') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Configuration -->
            <div class="space-y-6">
                <!-- Supplier Selection -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Suppliers</h3>

                        <div class="mb-4">
                            <div class="flex items-center space-x-4 mb-4">
                                <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800">
                                    Select All
                                </button>
                                <button type="button" id="deselectAll" class="text-sm text-gray-600 hover:text-gray-800">
                                    Deselect All
                                </button>
                                <span id="selectedCount" class="text-sm text-gray-500">0 suppliers selected</span>
                            </div>

                            <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-md">
                                @foreach($suppliers as $supplier)
                                    <label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}"
                                               class="supplier-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $supplier->email }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template Selection -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Email Template</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="template_id" class="block text-sm font-medium text-gray-700">Select Template</label>
                                <select id="template_id" name="template_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choose a template...</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="custom_message" class="block text-sm font-medium text-gray-700">Custom Message (Optional)</label>
                                <textarea id="custom_message" name="custom_message" rows="4"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Add any additional message or specific details..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Options -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Send Options</h3>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="send_immediately" name="send_immediately" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="send_immediately" class="ml-2 block text-sm text-gray-900">
                                    Send immediately (emails will be queued and sent in the background)
                                </label>
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" id="previewBtn"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-2"></i>
                                    Preview
                                </button>
                                <button type="submit" id="sendBtn"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Send Mail Merge
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Preview -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Preview</h3>

                    <div id="previewContent" class="space-y-4">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-envelope text-4xl mb-4"></i>
                            <p>Select a template and suppliers to preview the email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const supplierCheckboxes = document.querySelectorAll('.supplier-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const selectedCountSpan = document.getElementById('selectedCount');
    const templateSelect = document.getElementById('template_id');
    const customMessageTextarea = document.getElementById('custom_message');
    const previewBtn = document.getElementById('previewBtn');
    const previewContent = document.getElementById('previewContent');
    const sendBtn = document.getElementById('sendBtn');

    // Update selected count
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.supplier-checkbox:checked').length;
        selectedCountSpan.textContent = `${selectedCount} suppliers selected`;
    }

    // Select all suppliers
    selectAllBtn.addEventListener('click', function() {
        supplierCheckboxes.forEach(checkbox => checkbox.checked = true);
        updateSelectedCount();
    });

    // Deselect all suppliers
    deselectAllBtn.addEventListener('click', function() {
        supplierCheckboxes.forEach(checkbox => checkbox.checked = false);
        updateSelectedCount();
    });

    // Update count when checkboxes change
    supplierCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Preview functionality
    previewBtn.addEventListener('click', function() {
        const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
        const templateId = templateSelect.value;
        const customMessage = customMessageTextarea.value;

        if (!templateId) {
            alert('Please select a template');
            return;
        }

        if (selectedSuppliers.length === 0) {
            alert('Please select at least one supplier');
            return;
        }

        // Show loading
        previewContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading preview...</p></div>';

        // Make AJAX request
        fetch('{{ route("supplier-mail-merge.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                template_id: templateId,
                supplier_ids: selectedSuppliers,
                custom_message: customMessage
            })
        })
        .then(response => response.json())
        .then(data => {
            previewContent.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject:</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded border">${data.subject}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Body:</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded border max-h-96 overflow-y-auto whitespace-pre-wrap">${data.body}</div>
                    </div>
                    <div class="text-sm text-gray-500">
                        This email will be sent to ${data.recipient_count} supplier(s).
                    </div>
                </div>
            `;
        })
        .catch(error => {
            previewContent.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Error loading preview</p></div>';
        });
    });

    // Form validation
    document.getElementById('mailMergeForm').addEventListener('submit', function(e) {
        const selectedSuppliers = document.querySelectorAll('.supplier-checkbox:checked');
        const templateId = templateSelect.value;

        if (selectedSuppliers.length === 0) {
            e.preventDefault();
            alert('Please select at least one supplier');
            return;
        }

        if (!templateId) {
            e.preventDefault();
            alert('Please select a template');
            return;
        }

        if (!confirm(`Are you sure you want to send emails to ${selectedSuppliers.length} supplier(s)?`)) {
            e.preventDefault();
            return;
        }

        // Disable send button to prevent double submission
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    });
});
</script>
@endpush
@endsection
