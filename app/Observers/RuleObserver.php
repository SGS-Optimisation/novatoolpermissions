<?php

namespace App\Observers;

use App\Events\Rules\Updated;
use App\Models\Rule;

class RuleObserver
{
    /**
     * Handle the Rule "created" event.
     *
     * @param  \App\Models\Rule  $rule
     * @return void
     */
    public function created(Rule $rule)
    {
        event(new Updated($rule));
    }

    /**
     * Handle the Rule "updated" event.
     *
     * @param  \App\Models\Rule  $rule
     * @return void
     */
    public function updated(Rule $rule)
    {
        event(new Updated($rule));
    }

    /**
     * Handle the Rule "deleted" event.
     *
     * @param  \App\Models\Rule  $rule
     * @return void
     */
    public function deleted(Rule $rule)
    {
        //
    }

    /**
     * Handle the Rule "restored" event.
     *
     * @param  \App\Models\Rule  $rule
     * @return void
     */
    public function restored(Rule $rule)
    {
        event(new Updated($rule));
    }

    /**
     * Handle the Rule "force deleted" event.
     *
     * @param  \App\Models\Rule  $rule
     * @return void
     */
    public function forceDeleted(Rule $rule)
    {
        //
    }
}
