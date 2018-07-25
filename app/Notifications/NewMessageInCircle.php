<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMessageInCircle extends Notification
{
    use Queueable;

    private $user;
    private $circle;
    private $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($circle, $user, $message)
    {
        $this->circle = $circle;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $circle_name = good_title($this->circle);

        return (new MailMessage)
                ->subject('New comment in ' . $circle_name)
                ->line(sprintf('New comment was posted by %s in circle "%s"!', $this->user->name, $circle_name))
                ->action('Show ' . $circle_name, route('circles.show', ['uuid' => $this->circle->uuid]))
                ->line('Thank you for using CircleFinder!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'circle_uuid' => $this->circle->uuid,
            'circle_url' => route('circles.show', ['uuid' => $this->circle->uuid]),
            'message_body' => $this->message->body,
            'user_name' => $this->user->name,
        ];
    }
}
