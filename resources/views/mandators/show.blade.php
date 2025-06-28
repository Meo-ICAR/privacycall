<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mandator->full_name }} - Mandator Details - PrivacyCall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                            PrivacyCall
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="/companies" class="text-gray-600 hover:text-gray-900">Companies</a>
                    <a href="/mandators" class="text-blue-600 font-medium">Mandators</a>
                    <a href="/gdpr" class="text-gray-600 hover:text-gray-900">GDPR</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i> Back to Mandators
            </a>
            <div class="flex justify-between items-start mt-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $mandator->full_name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $mandator->position }} at {{ $mandator->company->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('mandators.edit', $mandator) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('mandators.destroy', $mandator) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this mandator?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $mandator->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $mandator->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($mandator->phone)
                                    <a href="tel:{{ $mandator->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $mandator->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Position</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $mandator->position ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Department</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $mandator->department ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1
                                {{ $mandator->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $mandator->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Contact Preferences -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Preferences</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email Notifications</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center">
                                    @if($mandator->email_notifications)
                                        <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                    @else
                                        <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">SMS Notifications</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center">
                                    @if($mandator->sms_notifications)
                                        <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                    @else
                                        <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Preferred Contact Method</label>
                            <p class="mt-1 text-sm text-gray-900 capitalize">{{ $mandator->preferred_contact_method }}</p>
                        </div>
                    </div>
                </div>

                <!-- Disclosure Subscriptions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Disclosure Subscriptions</h2>
                        <button onclick="showAddSubscriptionModal()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Subscription
                        </button>
                    </div>

                    @if($mandator->disclosure_subscriptions && count($mandator->disclosure_subscriptions) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($mandator->disclosure_subscriptions as $subscription)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $subscription)) }}</span>
                                    <button onclick="removeSubscription('{{ $subscription }}')"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No disclosure subscriptions configured</p>
                    @endif

                    @if($mandator->last_disclosure_date)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-clock mr-2"></i>
                                Last disclosure sent: {{ $mandator->last_disclosure_date->format('M j, Y \a\t g:i A') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                @if($mandator->notes)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes</h2>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $mandator->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Profile Picture -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>
                    <div class="flex justify-center">
                        <img src="{{ $mandator->logo_url }}"
                             alt="{{ $mandator->full_name }}"
                             class="w-32 h-32 rounded-full object-cover">
                    </div>
                </div>

                <!-- Company Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Company</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $mandator->company->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $mandator->company->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->company->email }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($mandator->company->phone)
                                    <a href="tel:{{ $mandator->company->phone }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $mandator->company->phone }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Not provided</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Clone Information -->
                @if($mandator->isClone())
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Clone Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Original Mandator</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $mandator->original->full_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Original Company</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $mandator->original->company->name }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($mandator->hasClones())
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Clones</h3>
                        <p class="text-sm text-gray-600 mb-3">This mandator has been cloned to other companies:</p>
                        <div class="space-y-2">
                            @foreach($mandator->clones as $clone)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $clone->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $clone->company->name }}</p>
                                    </div>
                                    <a href="{{ route('mandators.show', $clone) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @if(Auth::user()->role === 'superadmin')
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('mandators.clone-form', $mandator) }}"
                               class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-center block">
                                <i class="fas fa-copy mr-2"></i>Clone to Another Company
                            </a>
                            <button onclick="updateLastDisclosureDate()"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-clock mr-2"></i>Update Last Disclosure Date
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Subscription Modal -->
    <div id="addSubscriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Disclosure Subscription</h3>
                    <form id="addSubscriptionForm">
                        <div class="mb-4">
                            <label for="disclosure_type" class="block text-sm font-medium text-gray-700 mb-2">Disclosure Type</label>
                            <select name="disclosure_type" id="disclosure_type" required
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select disclosure type</option>
                                <option value="gdpr_updates">GDPR Updates</option>
                                <option value="data_breach_notifications">Data Breach Notifications</option>
                                <option value="privacy_policy_changes">Privacy Policy Changes</option>
                                <option value="consent_management">Consent Management</option>
                                <option value="security_updates">Security Updates</option>
                                <option value="employee_data_processing">Employee Data Processing</option>
                                <option value="third_party_disclosures">Third Party Disclosures</option>
                                <option value="data_retention_changes">Data Retention Changes</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideAddSubscriptionModal()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Add Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAddSubscriptionModal() {
            document.getElementById('addSubscriptionModal').classList.remove('hidden');
        }

        function hideAddSubscriptionModal() {
            document.getElementById('addSubscriptionModal').classList.add('hidden');
        }

        function addSubscription(disclosureType) {
            fetch(`{{ route('mandators.add-disclosure-subscription', $mandator) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ disclosure_type: disclosureType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the subscription');
            });
        }

        function removeSubscription(disclosureType) {
            if (confirm('Are you sure you want to remove this subscription?')) {
                fetch(`{{ route('mandators.remove-disclosure-subscription', $mandator) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ disclosure_type: disclosureType })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the subscription');
                });
            }
        }

        function updateLastDisclosureDate() {
            if (confirm('Update the last disclosure date to now?')) {
                fetch(`{{ route('mandators.update-last-disclosure-date', $mandator) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the disclosure date');
                });
            }
        }

        document.getElementById('addSubscriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const disclosureType = document.getElementById('disclosure_type').value;
            if (disclosureType) {
                addSubscription(disclosureType);
                hideAddSubscriptionModal();
            }
        });
    </script>
</body>
</html>
