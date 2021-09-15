<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Models\RuleUser;
use App\Models\User;
use App\Operations\BaseOperation;

class AssignRuleReviewersOperation extends BaseOperation
{
    public array $result;

    /**
     * AssignRuleReviewersOperation constructor.
     * @param  Rule  $rule
     * @param  User[]|int[]  $reviewers
     */
    public function __construct(public Rule $rule, public array $reviewers = [])
    {
    }

    public function withReviewers(...$reviewers): static
    {
        $this->reviewers = $reviewers;
        return $this;
    }

    public function handle()
    {
        if (isset($this->reviewers[0])
            && is_a($this->reviewers[0], User::class)) {
            $this->reviewers = collect($this->reviewers)->pluck('id')->all();
        }

        $this->result = $this->rule->users()->syncWithPivotValues(
            $this->reviewers,
            [
                'metadata' => [
                    'action' => 'review'
                ]
            ],
            false
        );

        logger('rule reviewers: ' . print_r($this->reviewers, true));
        logger('rule review assignment result: '. print_r($this->result, true));

        return $this->result;
    }

}
