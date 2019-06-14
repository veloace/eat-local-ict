<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PlaceClaimRejected extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $placeName;
    private $adminComments;


    public function __construct($placeName, $adminComments)
    {
        //
        $this->placeName = $placeName;
        $this->adminComments = $adminComments;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your EatLocalICT Place Ownership Claim Has Been Denied.')
                    ->line("We have reviewed your ownership claim for {$this->placeName} and determined that it was invalid, or we need more information from you.")
                    ->line("The Admin who reviewed your request gave the following comment:{$this->adminComments}")
                    ->line('If you have questions, you can contact our support team at support@eatlocalict.com')
                    ->line('Thank you for using EatLocalICT!');
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
            //
        ];
    }
}
