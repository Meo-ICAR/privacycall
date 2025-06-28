<?php

namespace App\Notifications;

use App\Models\DataRemovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataRemovalRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public DataRemovalRequest $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(DataRemovalRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = route('data-removal-requests.show', $this->request);
        return (new MailMessage)
            ->subject('Data Removal Request Rejected: ' . $this->request->request_number)
            ->greeting('Hello,')
            ->line('A data removal request has been rejected.')
            ->line('Request Number: ' . $this->request->request_number)
            ->line('Subject: ' . ($this->request->customer?->full_name ?? $this->request->mandator?->full_name ?? 'N/A'))
            ->line('Rejection Reason: ' . $this->request->rejection_reason)
            ->action('View Request', $url)
            ->line('Please review the reason for rejection.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'status' => $this->request->status,
        ];
    }
}
