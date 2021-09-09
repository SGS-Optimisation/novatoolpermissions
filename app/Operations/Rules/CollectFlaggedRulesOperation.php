<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Operations\BaseOperation;

class CollectFlaggedRulesOperation extends BaseOperation
{
    public $users_rules_dict;

    public function handle()
    {
        $rules = Rule::isFlagged()->with(['contributors'])->get();
        $users_rules_dict = [];

        foreach ($rules as $rule) {
            foreach ($rule->contributors as $contributor) {
                if (!isset($users_rules_dict[$contributor->id])) {
                    $users_rules_dict[$contributor->id] = [
                        'user' => $contributor,
                        'rules' => [],
                    ];
                }

                $users_rules_dict[$contributor->id]['rules'][] = $rule;

            }
        }

        $this->users_rules_dict = $users_rules_dict;

        return $this;
    }

}
