<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTransaction extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Transaction Created')
            ->line('A new transaction has been created:')
            ->line('Title: '.$this->transaction->title)
            ->line('Amount: '.$this->transaction->amount)
            ->line('Date: '.$this->transaction->created_at);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
