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
        // TODO: Implement handle() method.
    }

}
