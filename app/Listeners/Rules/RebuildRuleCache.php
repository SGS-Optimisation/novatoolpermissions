<?php

namespace App\Listeners\Rules;

use App\Events\Rules\Flagged;
use App\Events\Rules\Unflagged;
use App\Repositories\RuleRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RebuildRuleCache implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Flagged|Unflagged $event
     * @return void
     */
    public function handle( $event)
    {
        $rule = $event->rule;

        /*
         * Empty cache
         */
        \Cache::tags(['rules', $rule->clientAccount->slug])->clear();

        /*
         * Rebuild cache
         */
        $ruleRepo = new RuleRepository($rule->clientAccount);
        $ruleRepo->all();
    }
}
