<?php

namespace App\Notifications;

use App\Models\Rule;
use App\States\Rules\PublishedState;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Inspiring;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FlaggedRulesReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public array $rules)
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
        $subject = 'Reminder for flagged ' . (count($this->rules) > 1 ? 'rules' : 'rule: ' . Str::limit($this->rules[0]->name, 20));
        $message = (new MailMessage)
            ->greeting('Hello ' . $notifiable->given_name)
            ->subject($subject)
            ->line(sprintf('The following flagged %s your attention:', count($this->rules) > 1 ? 'rules need' : 'rule needs'));

        $published_rules = collect($this->rules)->where('state', PublishedState::$name);
        $unpublished_rules = collect($this->rules)->where('state', '!=', PublishedState::$name);

        /** @var Rule $rule */
        if(count($published_rules) && count($unpublished_rules)) {
            $message->line(new HtmlString('<strong>Published Rules</strong>'));
        }

        $this->addRules($published_rules, $message);

        if(count($published_rules) && count($unpublished_rules)) {
            $message->line(new HtmlString('<strong>Unpublished Rules</strong>'));
        }

        $this->addRules($unpublished_rules, $message);

        return $message->salutation(new HtmlString('Regards,<br>The Dagobah Team'));
    }

    public function addRules($rules, &$message) {
        foreach ($rules as $rule) {
            $last_reason_key = array_key_last($rule->metadata['flag_reason']);
            $flag_reason = $rule->metadata['flag_reason'][$last_reason_key];

            $message->line(new HtmlString(
                sprintf('<p><a href="%s">%s</a><br>The flag reason was: "%s" (%s on %s)</p><br>',
                    $rule->url, '['.$rule->dag_id.'] '.$rule->name, $flag_reason['reason'], $flag_reason['user'], $flag_reason['date']
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
