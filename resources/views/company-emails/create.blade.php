@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('companies.emails.index', $company) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Send New Email</h1>
                <p class="mt-2 text-gray-600">Send email from {{ $company->name }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('companies.emails.store', $company) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="to_email" class="block text-sm font-medium text-gray-700">To Email *</label>
                        <input type="email" name="to_email" id="to_email" required value="{{ old('to_email') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="recipient@example.com">
                        @error('to_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="to_name" class="block text-sm font-medium text-gray-700">To Name</label>
                        <input type="text" name="to_name" id="to_name" value="{{ old('to_name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Recipient Name">
                        @error('to_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject *</label>
                    <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Email subject">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Message *</label>
                    <textarea name="body" id="body" rows="12" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Type your message here...">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">You can select multiple files. Maximum 10MB per file.</p>
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Templates -->
                <div>
                    <label for="email_template" class="block text-sm font-medium text-gray-700">Email Template (Optional)</label>
                    <select name="email_template" id="email_template"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Select a template...</option>
                        <option value="gdpr_inquiry">GDPR Compliance Inquiry</option>
                        <option value="data_request">Data Subject Request</option>
                        <option value="breach_notification">Data Breach Notification</option>
                        <option value="consent_update">Consent Management Update</option>
                        <option value="privacy_policy">Privacy Policy Update</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('companies.emails.index', $company) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('body');
    const templateSelect = document.getElementById('email_template');

    // Auto-resize textarea
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Set initial height
    textarea.style.height = textarea.scrollHeight + 'px';

    // Email templates
    const templates = {
        gdpr_inquiry: {
            subject: 'GDPR Compliance Inquiry',
            body: `Dear [Recipient Name],

I hope this email finds you well. I am writing to inquire about your GDPR compliance procedures and how you handle personal data processing.

Please provide information on the following:

1. Your current data processing activities
2. Data retention policies and procedures
3. Security measures in place
4. Contact details for your Data Protection Officer (if applicable)
5. Your process for handling data subject requests

This information will help us ensure our compliance records are up to date and that we meet our regulatory obligations.

Thank you for your cooperation.

Best regards,
[Your Name]
[Company Name]`
        },
        data_request: {
            subject: 'Data Subject Request - Right to Access',
            body: `Dear [Recipient Name],

We have received a request from a data subject regarding their right to access their personal data under Article 15 of the GDPR.

Please provide the following information:

1. Confirmation of whether you process their personal data
2. A copy of the personal data being processed
3. Information about the processing purposes
4. Information about data retention periods
5. Information about their rights under GDPR

Please respond within 30 days as required by GDPR regulations.

Thank you for your prompt attention to this matter.

Best regards,
[Your Name]
[Company Name]`
        },
        breach_notification: {
            subject: 'Data Breach Notification',
            body: `Dear [Recipient Name],

We are writing to notify you of a potential data breach that may affect personal data you have entrusted to us.

Incident Details:
- Date of discovery: [Date]
- Nature of the breach: [Description]
- Types of data potentially affected: [List]
- Number of individuals potentially affected: [Number]

We are currently investigating this incident and have implemented additional security measures. We will provide updates as more information becomes available.

If you have any questions or concerns, please do not hesitate to contact us.

Best regards,
[Your Name]
[Company Name]`
        },
        consent_update: {
            subject: 'Consent Management Update',
            body: `Dear [Recipient Name],

We are updating our consent management procedures to ensure full compliance with GDPR requirements.

Please review and update your consent preferences for:

1. Marketing communications
2. Data processing activities
3. Third-party data sharing
4. Automated decision making

You can update your preferences by [method of updating].

If you have any questions about these changes, please contact us.

Best regards,
[Your Name]
[Company Name]`
        },
        privacy_policy: {
            subject: 'Privacy Policy Update',
            body: `Dear [Recipient Name],

We have updated our privacy policy to reflect changes in our data processing activities and to ensure full GDPR compliance.

Key changes include:
- Updated data processing purposes
- Enhanced data subject rights information
- New contact details for data protection inquiries
- Updated retention periods

The updated privacy policy is available at [URL].

Please review these changes and contact us if you have any questions.

Best regards,
[Your Name]
[Company Name]`
        }
    };

    // Handle template selection
    templateSelect.addEventListener('change', function() {
        const selectedTemplate = templates[this.value];
        if (selectedTemplate) {
            document.getElementById('subject').value = selectedTemplate.subject;
            document.getElementById('body').value = selectedTemplate.body;

            // Trigger textarea resize
            const event = new Event('input');
            textarea.dispatchEvent(event);
        }
    });
});
</script>
@endsection
