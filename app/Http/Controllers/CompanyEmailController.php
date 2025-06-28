<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Services\EmailIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompanyEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailIntegrationService $emailService)
    {
        $this->emailService = $emailService;
        $this->middleware('auth');
        $this->middleware('role:admin|superadmin');
    }

    /**
     * Display a listing of emails for a company.
     */
    public function index(Request $request, Company $company)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only view emails for your own company.');
        }

        $query = CompanyEmail::where('company_id', $company->id)
            ->with(['user'])
            ->orderBy('received_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('gdpr_related')) {
            if ($request->gdpr_related === 'true') {
                $query->gdprRelated();
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('from_email', 'like', "%{$search}%")
                  ->orWhere('from_name', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $emails = $query->paginate(20);
        $stats = $this->emailService->getEmailStats($company);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $emails,
                'stats' => $stats
            ]);
        }

        return view('company-emails.index', compact('company', 'emails', 'stats'));
    }

    /**
     * Display the specified email.
     */
    public function show(Company $company, CompanyEmail $email)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only view emails for your own company.');
        }

        // Check if email belongs to the company
        if ($email->company_id !== $company->id) {
            abort(404);
        }

        // Mark email as read if it's unread
        if ($email->status === 'unread') {
            $email->markAsRead();
        }

        // Load thread emails
        $threadEmails = $email->thread_id ?
            CompanyEmail::where('thread_id', $email->thread_id)
                ->where('company_id', $company->id)
                ->orderBy('received_at', 'asc')
                ->get() : collect();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $email->load('user'),
                'thread_emails' => $threadEmails
            ]);
        }

        return view('company-emails.show', compact('company', 'email', 'threadEmails'));
    }

    /**
     * Show the form for creating a new email.
     */
    public function create(Company $company)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only create emails for your own company.');
        }

        return view('company-emails.create', compact('company'));
    }

    /**
     * Store a newly created email.
     */
    public function store(Request $request, Company $company)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only send emails for your own company.');
        }

        $validator = Validator::make($request->all(), [
            'to_email' => 'required|email',
            'to_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $emailData = $validator->validated();

            // Handle attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('email_attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }

            // Send the email
            $sent = $this->emailService->sendEmail([
                'to_email' => $emailData['to_email'],
                'to_name' => $emailData['to_name'] ?? null,
                'subject' => $emailData['subject'],
                'body' => $emailData['body'],
                'attachments' => $attachments,
            ]);

            if (!$sent) {
                throw new \Exception('Failed to send email');
            }

            DB::commit();

            Log::info("Email sent by user " . auth()->id() . " for company " . $company->id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully'
                ], 201);
            }

            return redirect()->route('companies.emails.index', $company)
                ->with('success', 'Email sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending email: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending email: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error sending email: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for replying to an email.
     */
    public function reply(Company $company, CompanyEmail $email)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only reply to emails for your own company.');
        }

        // Check if email belongs to the company
        if ($email->company_id !== $company->id) {
            abort(404);
        }

        return view('company-emails.reply', compact('company', 'email'));
    }

    /**
     * Send a reply to an email.
     */
    public function sendReply(Request $request, Company $company, CompanyEmail $email)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only reply to emails for your own company.');
        }

        // Check if email belongs to the company
        if ($email->company_id !== $company->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'reply_body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $replyData = $validator->validated();

            // Handle attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('email_attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ];
                }
            }

            // Send the reply
            $sent = $this->emailService->sendReply(
                $email,
                auth()->user(),
                $replyData['reply_body'],
                $attachments
            );

            if (!$sent) {
                throw new \Exception('Failed to send reply');
            }

            DB::commit();

            Log::info("Reply sent by user " . auth()->id() . " for email " . $email->id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reply sent successfully'
                ]);
            }

            return redirect()->route('companies.emails.show', [$company, $email])
                ->with('success', 'Reply sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending reply: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending reply: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error sending reply: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified email (mark as read, archive, etc.).
     */
    public function update(Request $request, Company $company, CompanyEmail $email)
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only update emails for your own company.');
        }

        // Check if email belongs to the company
        if ($email->company_id !== $company->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:mark_read,mark_replied,archive,add_notes,update_priority,update_category',
            'notes' => 'nullable|string',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'category' => 'nullable|in:complaint,inquiry,notification,general',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator);
        }

        try {
            $action = $request->action;
            $updateData = [];

            switch ($action) {
                case 'mark_read':
                    $email->markAsRead();
                    break;

                case 'mark_replied':
                    $email->markAsReplied();
                    break;

                case 'archive':
                    $email->archive();
                    break;

                case 'add_notes':
                    $updateData['notes'] = $request->notes;
                    break;

                case 'update_priority':
                    $updateData['priority'] = $request->priority;
                    break;

                case 'update_category':
                    $updateData['category'] = $request->category;
                    break;
            }

            if (!empty($updateData)) {
                $email->update($updateData);
            }

            Log::info("Email {$email->id} updated with action: {$action} by user " . auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email updated successfully',
                    'data' => $email->fresh()
                ]);
            }

            return back()->with('success', 'Email updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating email: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating email: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error updating email: ' . $e->getMessage());
        }
    }

    /**
     * Fetch new emails for a company.
     */
    public function fetchEmails(Company $company): JsonResponse
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only fetch emails for your own company.'
            ], 403);
        }

        try {
            $result = $this->emailService->fetchEmailsForCompany($company);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ?
                    "Successfully processed {$result['processed']} emails" :
                    $result['error'],
                'processed' => $result['processed'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching emails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching emails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get email statistics for a company.
     */
    public function stats(Company $company): JsonResponse
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only view stats for your own company.'
            ], 403);
        }

        try {
            $stats = $this->emailService->getEmailStats($company);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting email stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error getting email stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified email from storage.
     */
    public function destroy(Company $company, CompanyEmail $email): JsonResponse
    {
        // Check if user has access to this company
        if (auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete emails for your own company.'
            ], 403);
        }

        // Check if email belongs to the company
        if ($email->company_id !== $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found.'
            ], 404);
        }

        try {
            $email->delete();

            Log::info("Email {$email->id} deleted by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Email deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting email: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting email: ' . $e->getMessage()
            ], 500);
        }
    }
}
