<?php

namespace App\Notifications;

use App\Models\DataRemovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataRemovalRequestCreated extends Notification implements ShouldQueue
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
        $subject = 'New Data Removal Request: ' . $this->request->request_number;
        $lines = [
            'A new data removal (right to be forgotten) request has been submitted.',
            'Request Number: ' . $this->request->request_number,
            'Type: ' . ucfirst(str_replace('_', ' ', $this->request->request_type)),
            'Priority: ' . ucfirst($this->request->priority),
            'Status: ' . ucfirst(str_replace('_', ' ', $this->request->status)),
            'Subject: ' . ($this->request->customer?->full_name ?? $this->request->mandator?->full_name ?? 'N/A'),
            'Reason: ' . $this->request->reason_for_removal,
            'Due Date: ' . ($this->request->due_date?->format('Y-m-d') ?? 'N/A'),
        ];
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello,')
            ->line(...$lines)
            ->action('View Request', $url)
            ->line('Please review and process this request as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id,
            'request_number' => $this->request->request_number,
            'type' => $this->request->request_type,
            'priority' => $this->request->priority,
            'status' => $this->request->status,
        ];
    }
}
