<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Session Reminder')
            ->line('Your session is scheduled to start in 5 minutes.')
            ->line('Session Details:')
            ->line('Student: ' . $this->session->student->name)
            ->line('Time: ' . $this->session->time)
            ->action('View Session', url('/sessions/' . $this->session->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'session_id' => $this->session->id,
            'time' => $this->session->time,
        ];
    }
}
