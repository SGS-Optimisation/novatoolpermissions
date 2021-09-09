<?php

namespace App\Notifications;

use App\Models\Rule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class RuleReviewRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Rule $rule, public User $requester)
    {
        //
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
        $rule = $this->rule;
        $subject = 'Rule review requested: '.Str::limit($rule->name, 20);

        return (new MailMessage)
            ->greeting('Hello '.$notifiable->given_name)
            ->subject($subject)
            ->line($this->requester->name .' requested your review for the rule:')
            ->line(new HtmlString(
                sprintf('<p><a href="%s">%s</a>',
                    $rule->url, '['.$rule->dag_id.'] '.$rule->name,
                )
            ))->salutation(new HtmlString('Regards,<br>The Dagobah Team'));
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
