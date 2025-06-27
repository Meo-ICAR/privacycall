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

class SendSupplierMailMerge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailLog;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to sent
            $this->emailLog->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Send the email
            Mail::to($this->emailLog->recipient_email)
                ->send(new SupplierMailMerge($this->emailLog));

            // Update status to delivered (in a real app, you'd use webhooks from your email provider)
            $this->emailLog->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);

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
