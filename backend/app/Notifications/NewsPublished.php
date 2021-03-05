<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsPublished extends Notification implements ShouldQueue {

    use Queueable;

    public $details;

    public function __construct(array $details) {
        $this->details = $details;
    }

    /**
     * Get the notification’s delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) : array {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return string[]
     */
    public function toArray() {
        return [
            'text' => 'Новость опубликована : "' . $this->details['name']."\".",
        ];
    }

}
