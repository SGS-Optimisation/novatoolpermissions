<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Models\User;
use App\Operations\BaseOperation;

class GetRuleReviewersOperation extends BaseOperation
{


    /**
     * AssignRuleReviewersOperation constructor.
     * @param  Rule  $rule
     * @param  User[]  $reviewers
     */
    public function __construct(public Rule $rule)
    {
    }

    public function handle()
    {
        $users = $this->rule->users;
        $reviewers = [];

        foreach ($users as $user) {
            if (isset($user->contributor->metadata)
                && $user->contributor->metadata['action'] === 'review') {
                $reviewers[] = $user;
            }
        }

        return $reviewers;
    }

}
