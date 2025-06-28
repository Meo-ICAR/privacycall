<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Supplier;
use App\Services\EmailIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UnifiedEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailIntegrationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display the unified email dashboard.
     */
    public function dashboard(Company $company = null)
    {
        $user = Auth::user();

        // If no company is specified, try to get user's company
        if (!$company) {
            $company = $user->company;
        }

        // If user is superadmin and has no company, show aggregated stats
        if (!$company && $user->hasRole('superadmin')) {
            return $this->showSuperadminDashboard();
        }

        // If user has no company and is not superadmin, redirect with error
        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        // Get email statistics
        $stats = $this->getEmailStats($company);

        // Get recent incoming emails
        $recentIncoming = CompanyEmail::where('company_id', $company->id)
            ->orderBy('received_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent outgoing emails
        $recentOutgoing = EmailLog::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get email templates
        $templates = EmailTemplate::where('company_id', $company->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get suppliers for quick mail merge
        $suppliers = Supplier::where('company_id', $company->id)
            ->whereNotNull('email')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('emails.unified-dashboard', compact(
            'company',
            'stats',
            'recentIncoming',
            'recentOutgoing',
            'templates',
            'suppliers'
        ));
    }

    /**
     * Show superadmin dashboard with aggregated stats from all companies.
     */
    protected function showSuperadminDashboard()
    {
        // Get all companies with emails
        $companies = Company::whereHas('emails')->withCount('emails')->orderBy('emails_count', 'desc')->limit(10)->get();

        // Get aggregated stats
        $totalEmails = CompanyEmail::count();
        $totalUnread = CompanyEmail::where('status', 'unread')->count();
        $totalGdprRelated = CompanyEmail::where('is_gdpr_related', true)->count();
        $totalHighPriority = CompanyEmail::whereIn('priority', ['high', 'urgent'])->count();

        $stats = [
            'total' => $totalEmails,
            'unread' => $totalUnread,
            'gdpr_related' => $totalGdprRelated,
            'high_priority' => $totalHighPriority,
        ];

        // Get recent emails from all companies
        $recentIncoming = CompanyEmail::with('company')
            ->orderBy('received_at', 'desc')
            ->limit(5)
            ->get();

        $recentOutgoing = EmailLog::with('company')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get all active email templates
        $templates = EmailTemplate::where('is_active', true)
            ->with('company')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Get suppliers from companies with emails
        $suppliers = Supplier::whereHas('company.emails')
            ->whereNotNull('email')
            ->with('company')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return view('emails.unified-dashboard', compact(
            'companies',
            'stats',
            'recentIncoming',
            'recentOutgoing',
            'templates',
            'suppliers'
        ));
    }

    /**
     * Display all emails (incoming and outgoing) with filtering.
     */
    public function index(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No company associated with your account.');
        }

        $type = $request->get('type', 'all'); // all, incoming, outgoing
        $status = $request->get('status', '');
        $priority = $request->get('priority', '');
        $category = $request->get('category', '');
        $search = $request->get('search', '');

        // Build queries
        $incomingQuery = CompanyEmail::where('company_id', $company->id);
        $outgoingQuery = EmailLog::where('company_id', $company->id);

        // Apply filters
        if ($status) {
            $incomingQuery->where('status', $status);
            $outgoingQuery->where('status', $status);
        }

        if ($priority) {
            $incomingQuery->where('priority', $priority);
        }

        if ($category) {
            $incomingQuery->where('category', $category);
        }

        if ($search) {
            $incomingQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('from_email', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });

            $outgoingQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $incomingEmails = $type !== 'outgoing' ? $incomingQuery->orderBy('received_at', 'desc')->paginate(15) : null;
        $outgoingEmails = $type !== 'incoming' ? $outgoingQuery->orderBy('created_at', 'desc')->paginate(15) : null;

        // Get filter options
        $statusOptions = $this->getStatusOptions();
        $priorityOptions = $this->getPriorityOptions();
        $categoryOptions = $this->getCategoryOptions();

        return view('emails.unified-index', compact(
            'company',
            'incomingEmails',
            'outgoingEmails',
            'type',
            'status',
            'priority',
            'category',
            'search',
            'statusOptions',
            'priorityOptions',
            'categoryOptions'
        ));
    }

    /**
     * Show email details (works for both incoming and outgoing).
     */
    public function show($id, $type = 'incoming', Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        // If superadmin is viewing an email and no company is specified,
        // try to get company from the email itself
        if (!$company && $user->hasRole('superadmin')) {
            if ($type === 'incoming') {
                $email = CompanyEmail::findOrFail($id);
                $company = $email->company;
            } else {
                $email = EmailLog::findOrFail($id);
                $company = $email->company;
            }
        }

        // If still no company, redirect with error
        if (!$company) {
            return redirect()->route('dashboard')->with('error', 'No company associated with this email.');
        }

        // For superadmins, check if they want to impersonate the company admin
        if ($user->hasRole('superadmin') && !session('impersonate_original_id')) {
            $companyAdmin = $this->getCompanyAdmin($company);
            if ($companyAdmin) {
                // Store the email viewing context for potential impersonation
                session([
                    'email_viewing_company_id' => $company->id,
                    'email_viewing_admin_id' => $companyAdmin->id,
                    'email_viewing_admin_name' => $companyAdmin->name,
                    'email_viewing_return_url' => request()->url()
                ]);
            }
        }

        if ($type === 'incoming') {
            $email = CompanyEmail::where('company_id', $company->id)->findOrFail($id);
        } else {
            $email = EmailLog::where('company_id', $company->id)->findOrFail($id);
        }

        return view('emails.unified-show', compact('email', 'type', 'company'));
    }

    /**
     * Get the admin user for a company.
     */
    protected function getCompanyAdmin(Company $company)
    {
        return User::where('company_id', $company->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->first();
    }

    /**
     * Impersonate as company admin when viewing emails (superadmin only).
     */
    public function impersonateForEmail(Company $company)
    {
        $user = Auth::user();

        if (!$user->hasRole('superadmin')) {
            abort(403, 'Only superadmin can impersonate.');
        }

        $companyAdmin = $this->getCompanyAdmin($company);

        if (!$companyAdmin) {
            return back()->with('error', 'No admin found for this company.');
        }

        if ($companyAdmin->hasRole('superadmin')) {
            return back()->with('error', 'Cannot impersonate another superadmin.');
        }

        // Store original user and start impersonation
        session(['impersonate_original_id' => $user->id]);
        Auth::login($companyAdmin);

        // Get the return URL from session or default to unified email index
        $returnUrl = session('email_viewing_return_url', route('emails.index', $company));

        // Clear the email viewing session data
        session()->forget([
            'email_viewing_company_id',
            'email_viewing_admin_id',
            'email_viewing_admin_name',
            'email_viewing_return_url'
        ]);

        return redirect($returnUrl)->with('success', 'Now impersonating ' . $companyAdmin->name . ' to view emails as company admin.');
    }

    /**
     * Reply to an incoming email.
     */
    public function reply(Request $request, CompanyEmail $email)
    {
        $request->validate([
            'reply_body' => 'required|string|max:5000',
            'template_id' => 'nullable|exists:email_templates,id',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max per file
        ]);

        $user = Auth::user();

        try {
            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    // Generate unique filename
                    $filename = uniqid() . '_' . $file->getClientOriginalName();
                    $storagePath = 'email-reply-attachments/' . $email->company_id . '/' . $filename;

                    // Store the file
                    $file->storeAs('email-reply-attachments/' . $email->company_id, $filename);

                    // Create EmailReplyAttachment record
                    $attachmentRecord = \App\Models\EmailReplyAttachment::create([
                        'company_email_id' => $email->id,
                        'user_id' => $user->id,
                        'filename' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'storage_path' => $storagePath,
                    ]);

                    // Add to attachments array for email service
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => $storagePath,
                    ];
                }
            }

            // Send reply using email service
            $sent = $this->emailService->sendReply($email, $user, $request->reply_body, $attachments);

            if ($sent) {
                return redirect()->route('emails.show', ['id' => $email->id, 'type' => 'incoming'])
                    ->with('success', 'Reply sent successfully!');
            } else {
                return back()->withErrors(['reply' => 'Failed to send reply. Please try again.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['reply' => 'Error sending reply: ' . $e->getMessage()]);
        }
    }

    /**
     * Send a new email using configured email provider.
     */
    public function send(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $request->validate([
            'to_email' => 'required|email',
            'to_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'template_id' => 'nullable|exists:email_templates,id',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max per file
        ]);

        try {
            // Check if company has email configured
            if (!$company->hasEmailConfigured()) {
                return back()->withErrors(['email' => 'Email is not configured for this company. Please configure email settings first.']);
            }

            // Handle file uploads
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    // Generate unique filename
                    $filename = uniqid() . '_' . $file->getClientOriginalName();
                    $storagePath = 'email-attachments/' . $company->id . '/' . $filename;

                    // Store the file
                    $file->storeAs('email-attachments/' . $company->id, $filename);

                    // Add to attachments array for email service
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => $storagePath,
                    ];
                }
            }

            // Prepare email data
            $emailData = [
                'to_email' => $request->to_email,
                'to_name' => $request->to_name,
                'subject' => $request->subject,
                'body' => $request->body,
                'priority' => $request->priority ?? 'normal',
                'attachments' => $attachments
            ];

            // Send email using configured provider
            $sent = $this->emailService->sendEmail($emailData);

            if ($sent) {
                // Log the email
                $emailLog = EmailLog::create([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'recipient_email' => $request->to_email,
                    'recipient_name' => $request->to_name,
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'template_name' => $request->template_id ? EmailTemplate::find($request->template_id)->name : null,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'metadata' => [
                        'template_id' => $request->template_id,
                        'priority' => $request->priority,
                        'type' => 'manual'
                    ]
                ]);

                // Save attachments if any
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = uniqid() . '_' . $file->getClientOriginalName();
                        $storagePath = 'email-attachments/' . $company->id . '/' . $filename;

                        \App\Models\EmailReplyAttachment::create([
                            'company_email_id' => null, // Not a reply to existing email
                            'email_log_id' => $emailLog->id,
                            'user_id' => $user->id,
                            'filename' => $filename,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'storage_path' => $storagePath,
                        ]);
                    }
                }

                return redirect()->route('emails.dashboard', $company)
                    ->with('success', 'Email sent successfully!');
            } else {
                return back()->withErrors(['email' => 'Failed to send email. Please check your email configuration.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Error sending email: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick mail merge to selected suppliers.
     */
    public function quickMailMerge(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $request->validate([
            'supplier_ids' => 'required|array|min:1',
            'supplier_ids.*' => 'exists:suppliers,id',
            'template_id' => 'required|exists:email_templates,id',
            'custom_message' => 'nullable|string|max:1000'
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $suppliers = Supplier::whereIn('id', $request->supplier_ids)
            ->where('company_id', $company->id)
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($suppliers as $supplier) {
            try {
                $emailData = $this->prepareEmailData($template, $supplier, $request->custom_message);

                // Send email using configured provider
                $sent = $this->emailService->sendEmail($emailData);

                if ($sent) {
                    // Log the email
                    EmailLog::create([
                        'company_id' => $company->id,
                        'user_id' => $user->id,
                        'recipient_email' => $supplier->email,
                        'recipient_name' => $supplier->name,
                        'subject' => $emailData['subject'],
                        'body' => $emailData['body'],
                        'template_name' => $template->name,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'metadata' => [
                            'supplier_id' => $supplier->id,
                            'template_id' => $template->id,
                            'custom_message' => $request->custom_message,
                            'type' => 'mail_merge'
                        ]
                    ]);

                    $sentCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to send mail merge email for supplier: ' . $supplier->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return redirect()->route('emails.dashboard', $company)
            ->with('success', "Quick mail merge completed! {$sentCount} emails sent, {$failedCount} failed.");
    }

    /**
     * Fetch new emails for the company.
     */
    public function fetchEmails(Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        if (!$company->hasEmailConfigured()) {
            return back()->withErrors(['fetch' => 'Email is not configured for this company.']);
        }

        try {
            $result = $this->emailService->fetchEmailsForCompany($company);

            if ($result['success']) {
                return back()->with('success', "Fetched {$result['processed']} new emails successfully!");
            } else {
                return back()->withErrors(['fetch' => 'Failed to fetch emails: ' . $result['error']]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['fetch' => 'Error fetching emails: ' . $e->getMessage()]);
        }
    }

    /**
     * Get email statistics for the company.
     */
    protected function getEmailStats(Company $company): array
    {
        try {
            $incomingStats = $this->emailService->getEmailStats($company);
        } catch (\Exception $e) {
            // Fallback to direct database queries if service fails
            $incomingStats = [
                'total' => CompanyEmail::where('company_id', $company->id)->count(),
                'unread' => CompanyEmail::where('company_id', $company->id)->where('status', 'unread')->count(),
                'read' => CompanyEmail::where('company_id', $company->id)->where('status', 'read')->count(),
                'replied' => CompanyEmail::where('company_id', $company->id)->where('status', 'replied')->count(),
                'gdpr_related' => CompanyEmail::where('company_id', $company->id)->where('is_gdpr_related', true)->count(),
                'high_priority' => CompanyEmail::where('company_id', $company->id)->where('priority', 'high')->count(),
                'urgent_priority' => CompanyEmail::where('company_id', $company->id)->where('priority', 'urgent')->count(),
            ];
        }

        $outgoingStats = [
            'total' => EmailLog::where('company_id', $company->id)->count(),
            'sent' => EmailLog::where('company_id', $company->id)->where('status', 'sent')->count(),
            'delivered' => EmailLog::where('company_id', $company->id)->where('status', 'delivered')->count(),
            'failed' => EmailLog::where('company_id', $company->id)->where('status', 'failed')->count(),
            'today' => EmailLog::where('company_id', $company->id)
                ->whereDate('created_at', today())
                ->count(),
        ];

        return [
            'incoming' => $incomingStats,
            'outgoing' => $outgoingStats,
            'total' => ($incomingStats['total'] ?? 0) + $outgoingStats['total'],
            'unread' => $incomingStats['unread'] ?? 0,
            'gdpr_related' => $incomingStats['gdpr_related'] ?? 0,
            'high_priority' => ($incomingStats['high_priority'] ?? 0) + ($incomingStats['urgent_priority'] ?? 0),
        ];
    }

    /**
     * Prepare email data with template variables.
     */
    protected function prepareEmailData($template, $supplier, $customMessage = null): array
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
     * Get status filter options.
     */
    protected function getStatusOptions(): array
    {
        return [
            'unread' => 'Unread',
            'read' => 'Read',
            'replied' => 'Replied',
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
        ];
    }

    /**
     * Get priority filter options.
     */
    protected function getPriorityOptions(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * Get category filter options.
     */
    protected function getCategoryOptions(): array
    {
        return [
            'complaint' => 'Complaint',
            'inquiry' => 'Inquiry',
            'request' => 'Request',
            'notification' => 'Notification',
            'gdpr' => 'GDPR Related',
        ];
    }

    /**
     * Download an email attachment.
     */
    public function downloadAttachment($id, $type = 'incoming')
    {
        $user = Auth::user();
        $company = $user->company;

        if ($type === 'incoming') {
            $attachment = \App\Models\EmailDocument::whereHas('companyEmail', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->findOrFail($id);
        } else {
            $attachment = \App\Models\EmailReplyAttachment::whereHas('companyEmail', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->findOrFail($id);
        }

        // Check if file exists
        if (!Storage::exists($attachment->storage_path)) {
            abort(404, 'File not found');
        }

        // Return file download
        return Storage::download($attachment->storage_path, $attachment->original_name);
    }
}
