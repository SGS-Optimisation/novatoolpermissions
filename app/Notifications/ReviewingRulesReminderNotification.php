<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ReviewingRulesReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * ReviewingRulesReminderNotification constructor.
     * @param  Team  $team
     * @param  Collection  $rules
     */
    public function __construct(public Team $team, public $rules)
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
        $count = count($this->rules);

        $subject = sprintf( '[%s] Review awaiting for %s %s',
            $this->team->name,
            $count,
            ($count > 1 ? 'rules' : 'rule')
        );

        $message = (new MailMessage)
            ->greeting('Hello ' . $notifiable->given_name)
            ->subject($subject)
            ->line(sprintf('The following  %s awaiting review:', count($this->rules) > 1 ? 'rules are' : 'rule is'));

        $this->addRules($message);

        return $message->salutation(new HtmlString('Regards,<br>The Dagobah Team'));
    }

    public function addRules(&$message) {
        foreach ($this->rules as $rule) {

            $message->line(new HtmlString(
                sprintf('<p><a href="%s">%s</a>',
                    $rule->url, '['.$rule->dag_id.'] '.$rule->name
                )
            ));
        }
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
