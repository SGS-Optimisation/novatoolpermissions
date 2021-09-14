<?php


namespace App\Features\Rules;


use App\Features\BaseFeature;
use App\Models\Rule;
use App\Models\User;
use App\Notifications\RuleReviewRequestedNotification;
use App\Operations\Rules\AssignRuleReviewersOperation;
use App\Operations\Rules\AutoPruneNonContributors;
use App\Operations\Rules\UnassignRuleReviewersOperation;
use Illuminate\Support\Facades\Notification;

class AssignAndNotifyRuleReviewersFeature extends BaseFeature
{

    /**
     * AssignAndNotifyRuleReviewers constructor.
     * @param  Rule  $rule
     * @param  User  $requester
     * @param  array|null  $reviewers
     */
    public function __construct(public Rule $rule, public User $requester, public ?array $reviewers = [])
    {
    }

    public function handle()
    {
        $current_reviewer_ids = $this->rule->users()->get()->filter(function ($user) {
            return isset($user->contributor->metadata['actions'])
                && $user->contributor->metadata['actions'] === 'review';
        })->pluck('id')->all();

        (new AutoPruneNonContributors($this->rule))->handle();
        (new UnassignRuleReviewersOperation($this->rule))->handle();

        $changes = (new AssignRuleReviewersOperation($this->rule, $this->reviewers))->handle();

        $users_to_notify = User::whereIn('id',
            array_diff(
                array_merge($changes['attached'], $changes['updated']),
                $current_reviewer_ids
            )
        )->get();

        Notification::send($users_to_notify, new RuleReviewRequestedNotification($this->rule, $this->requester));
    }

}
