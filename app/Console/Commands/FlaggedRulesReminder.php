<?php

namespace App\Console\Commands;

use App\Operations\Rules\CollectFlaggedRulesOperation;
use App\Models\User;
use App\Notifications\FlaggedRulesReminderNotification;
use Illuminate\Console\Command;

class FlaggedRulesReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules:flagged:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders about flagged rules to their contributors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $flagged_collector = (new CollectFlaggedRulesOperation)->handle();

        foreach($flagged_collector->users_rules_dict as $user_id => $user_rules_list) {
            /** @var User $user */
            $user = $user_rules_list['user'];
            $this->info('notifying ' . $user->name);

            $user->notify(new FlaggedRulesReminderNotification($user_rules_list['rules']));

        }
    }
}
