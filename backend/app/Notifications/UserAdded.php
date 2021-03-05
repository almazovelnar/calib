<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAdded extends Notification implements ShouldQueue {

    use Queueable;

    public $password;
    public $owner;

    public function __construct(string $password, string $owner) {
        $this->password = $password;
        $this->owner = $owner;
    }

    /**
     * Get the notification’s delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) : array {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable) : MailMessage {
        return ( new MailMessage )->greeting('Hi '. ucwords(mb_strtolower($notifiable->name)) .' !')
                                  ->subject('Your are new admin now!')
                                  ->replyTo(env('MAIL_REPLY_TO_ADDRESS'), env('MAIL_REPLY_TO_NAME'))
                                  ->level('success')
                                  ->line('You was assigned as a new Caliber Control Panel Admin by ' . $this->owner)
                                  ->line('Your current password is ' . $this->password . '')
                                  ->action('Open Control Panel', env("APP_URL"))
                                  ->line('Thanks for choosing us!');
    }

}
