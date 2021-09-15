<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Operations\BaseOperation;

class GetDefaultRuleReviewersOperation extends BaseOperation
{

    /**
     * GetDefaultRuleReviewersOperation constructor.
     */
    public function __construct(public Rule $rule)
    {
    }

    public function handle()
    {
        $publishers = [];
        $past_reviewers = [];
        $contributors = $this->rule->users;

        foreach ($this->rule->teams as $team) {
            foreach ($team->allUsers() as $user) {
                if($team->userHasPermission($user, 'publishRules')) {

                    if($contributors->firstWhere('id', $user->id)) {
                        $user->suggestion_level = 10;
                    } else {
                        $user->suggestion_level = 1;
                    }

                    $publishers[] = $user;
                }
            }
        }

        return $publishers;
    }

}
