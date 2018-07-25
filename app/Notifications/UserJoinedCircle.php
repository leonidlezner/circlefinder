<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserJoinedCircle extends Notification implements ShouldQueue
{
    use Queueable;
    private $circle;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($circle)
    {
        $this->circle = $circle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
                ->subject('New member in ' . $circle_name)
                ->line(sprintf('Your circle "%s" got a new member!', $circle_name))
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
        ];
    }
}
