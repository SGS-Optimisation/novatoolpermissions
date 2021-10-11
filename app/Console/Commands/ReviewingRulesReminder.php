<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Notifications\ReviewingRulesReminderNotification;
use App\States\Rules\ReviewingState;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ReviewingRulesReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules:reviewing:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $teams = Team::whereNotNull('client_account_id')->get();

        /** @var Team $team */
        foreach ($teams as $team) {
            $rules = $team->rules()->where('state', ReviewingState::$name)->get();

            if (count($rules)) {
                Notification::send(
                    $team->allUsersWhoCan('publishRules'),
                    new ReviewingRulesReminderNotification($team, $rules)
                );
            }
        }
    }
}
