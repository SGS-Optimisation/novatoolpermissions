<?php


namespace App\Operations\Rules;


use App\Models\Rule;
use App\Models\RuleUser;
use App\Operations\BaseOperation;

class UnassignRuleReviewersOperation extends BaseOperation
{

    /**
     * UnassignRuleReviewersOperation constructor.
     */
    public function __construct(public Rule $rule, public ?array $user_ids = null)
    {
    }

    public function handle()
    {
        $users = $this->rule->users()
            ->when(!is_null($this->user_ids), function ($query) {
                $query->whereIn('user_id', $this->user_ids);
            })
            ->get();

        foreach ($users as $user) {
            $metadata = $user->contributor->metadata;
            unset($metadata['action']);

            if (empty($metadata)) {
                $metadata = null;
            }

            $this->rule->users()->updateExistingPivot($user, [
                'metadata' => $metadata
            ]);

            $this->rule->save();
        }
    }

}
