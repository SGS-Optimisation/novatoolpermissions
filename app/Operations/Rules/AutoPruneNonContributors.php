<?php


namespace App\Operations\Rules;


use Altek\Accountant\Models\Ledger;
use App\Models\Rule;
use App\Operations\BaseOperation;

/**
 * Class AutoPruneNonContributors
 * @package App\Operations\Rules
 *
 * Removes all users who have not contributed to the rule,
 * by checking from the rule's ledger/audit logs
 */
class AutoPruneNonContributors extends BaseOperation
{


    /**
     * AutoPruneNonContributors constructor.
     */
    public function __construct(public Rule $rule)
    {
    }

    public function handle()
    {
        $users = $this->rule->users;

        $changes = $this->rule->ledgers()->with('user')->get();
        $real_contributors = $changes->pluck('user_id')->all();

        $non_contributors = $users->reject(function($user) use ($real_contributors) {
            return in_array($user->id, $real_contributors);
        })->pluck('id')->all();

        $this->rule->users()->detach($non_contributors);
    }

}
