<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Mail\SupplierMailMerge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailIntegrationService;
use App\Models\Company;

class SendSupplierMailMerge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailLog;
    protected $emailService;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
        $this->emailService = app(EmailIntegrationService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $company = $this->emailLog->company;
            // Update status to sent
            $this->emailLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Prepare email data
            $emailData = [
                'to_email' => $this->emailLog->recipient_email,
                'to_name' => $this->emailLog->recipient_name,
                'subject' => $this->emailLog->subject,
                'body' => $this->emailLog->body,
                'priority' => $this->emailLog->metadata['priority'] ?? 'normal',
            ];

            // Use the configured provider
            $sent = false;
            if ($company && $company->hasEmailConfigured()) {
                $sent = $this->emailService->sendEmail($emailData, $company);
            } else {
                // Fallback to Laravel's default mail if not configured
                \Mail::to($this->emailLog->recipient_email)
                    ->send(new \App\Mail\SupplierMailMerge($this->emailLog));
                $sent = true;
            }

            // Update status
            if ($sent) {
                $this->emailLog->update([
                    'status' => 'delivered',
                    'delivered_at' => now()
                ]);
            } else {
                $this->emailLog->update([
                    'status' => 'failed',
                    'error_message' => 'Failed to send via configured provider.'
                ]);
            }

        } catch (\Exception $e) {
            // Update status to failed
            $this->emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
