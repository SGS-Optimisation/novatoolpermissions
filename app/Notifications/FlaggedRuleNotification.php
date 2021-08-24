<?php

namespace App\Notifications;

use App\Models\Rule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FlaggedRuleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Rule $rule)
    {

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
        $last_reason_key = array_key_last($rule->metadata['flag_reason']);
        $flag_reason = $rule->metadata['flag_reason'][$last_reason_key];

        $subject = 'Rule flagged: '.Str::limit($rule->name, 20);

        return (new MailMessage)
            ->greeting('Hello '.$notifiable->given_name)
            ->subject($subject)
            ->line('The following flagged rule needs your attention:')
            ->line(new HtmlString(
                sprintf('<p><a href="%s">%s</a><br>The flag reason was: "%s" (%s on %s)</p><br>',
                    $rule->url, '['.$rule->dag_id.'] '.$rule->name,
                    $flag_reason['reason'], $flag_reason['user'], $flag_reason['date']
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
