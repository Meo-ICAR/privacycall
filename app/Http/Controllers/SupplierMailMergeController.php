<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Jobs\SendSupplierMailMerge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierMailMergeController extends Controller
{
    /**
     * Display the mail merge interface.
     */
    public function index()
    {
        $user = Auth::user();
        $suppliers = Supplier::where('company_id', $user->company_id)
            ->orderBy('name')
            ->get();

        $templates = EmailTemplate::where('company_id', $user->company_id)
            ->where('category', 'supplier')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // If no supplier templates exist, create default ones
        if ($templates->isEmpty()) {
            $this->createDefaultTemplates($user->company_id);
            $templates = EmailTemplate::where('company_id', $user->company_id)
                ->where('category', 'supplier')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('supplier_mail_merge.index', compact('suppliers', 'templates'));
    }

    /**
     * Preview the email with sample data.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'exists:suppliers,id',
            'custom_message' => 'nullable|string'
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $suppliers = Supplier::whereIn('id', $request->supplier_ids)->get();

        // Get first supplier for preview
        $sampleSupplier = $suppliers->first();

        $previewData = $this->prepareEmailData($template, $sampleSupplier, $request->custom_message);

        return response()->json([
            'subject' => $previewData['subject'],
            'body' => $previewData['body'],
            'recipient_count' => $suppliers->count()
        ]);
    }

    /**
     * Send the mail merge.
     */
    public function send(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'exists:suppliers,id',
            'custom_message' => 'nullable|string',
            'send_immediately' => 'boolean'
        ]);

        $user = Auth::user();
        $template = EmailTemplate::findOrFail($request->template_id);
        $suppliers = Supplier::whereIn('id', $request->supplier_ids)
            ->where('company_id', $user->company_id)
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($suppliers as $supplier) {
            try {
                $emailData = $this->prepareEmailData($template, $supplier, $request->custom_message);

                // Create email log
                $emailLog = EmailLog::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'recipient_email' => $supplier->email,
                    'recipient_name' => $supplier->name,
                    'subject' => $emailData['subject'],
                    'body' => $emailData['body'],
                    'template_name' => $template->name,
                    'status' => 'pending',
                    'metadata' => [
                        'supplier_id' => $supplier->id,
                        'template_id' => $template->id,
                        'custom_message' => $request->custom_message
                    ]
                ]);

                // Queue the email job
                SendSupplierMailMerge::dispatch($emailLog);

                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to queue email for supplier: ' . $supplier->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('supplier-mail-merge.index')
            ->with('success', "Mail merge queued successfully! {$sentCount} emails queued, {$failedCount} failed.");
    }

    /**
     * Prepare email data with variables replaced.
     */
    private function prepareEmailData($template, $supplier, $customMessage = null)
    {
        $data = [
            'supplier_name' => $supplier->name,
            'supplier_email' => $supplier->email,
            'supplier_phone' => $supplier->phone ?? 'N/A',
            'company_name' => Auth::user()->company->name ?? 'Your Company',
            'user_name' => Auth::user()->name,
            'current_date' => now()->format('F d, Y'),
            'custom_message' => $customMessage ?? ''
        ];

        return [
            'subject' => $template->replaceVariables($data),
            'body' => $template->replaceVariables($data)
        ];
    }

    /**
     * Create default email templates for suppliers.
     */
    private function createDefaultTemplates($companyId)
    {
        $templates = [
            [
                'name' => 'Supplier Welcome',
                'subject' => 'Welcome to {{company_name}} - Supplier Onboarding',
                'body' => "Dear {{supplier_name}},

Welcome to {{company_name}}! We're excited to have you as one of our valued suppliers.

This email confirms that your supplier account has been set up in our system. You can expect to receive regular communications from us regarding orders, updates, and important information.

If you have any questions or need assistance, please don't hesitate to contact us.

{{custom_message}}

Best regards,
{{user_name}}
{{company_name}}",
                'category' => 'supplier'
            ],
            [
                'name' => 'Supplier Update',
                'subject' => 'Important Update from {{company_name}}',
                'body' => "Dear {{supplier_name}},

We hope this email finds you well. We wanted to share some important updates with you regarding our partnership.

{{custom_message}}

Please review this information carefully and let us know if you have any questions or concerns.

Thank you for your continued partnership.

Best regards,
{{user_name}}
{{company_name}}",
                'category' => 'supplier'
            ],
            [
                'name' => 'Supplier Request',
                'subject' => 'Request from {{company_name}}',
                'body' => "Dear {{supplier_name}},

We hope you're doing well. We have a request that we'd like to discuss with you.

{{custom_message}}

Please let us know your thoughts and availability for further discussion.

Thank you for your time and consideration.

Best regards,
{{user_name}}
{{company_name}}",
                'category' => 'supplier'
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create([
                'company_id' => $companyId,
                'name' => $template['name'],
                'subject' => $template['subject'],
                'body' => $template['body'],
                'category' => $template['category'],
                'variables' => [
                    'supplier_name' => 'Supplier Name',
                    'supplier_email' => 'Supplier Email',
                    'supplier_phone' => 'Supplier Phone',
                    'company_name' => 'Your Company Name',
                    'user_name' => 'Current User Name',
                    'current_date' => 'Current Date',
                    'custom_message' => 'Custom Message'
                ],
                'is_active' => true
            ]);
        }
    }
}
