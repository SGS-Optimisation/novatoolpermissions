<?php


namespace App\Features\Rules;


use App\Features\BaseFeature;
use App\Models\Rule;
use App\Models\User;
use App\Notifications\RuleReviewRequestedNotification;
use App\Operations\Rules\AssignRuleReviewersOperation;
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
        $newAssignees = (new AssignRuleReviewersOperation($this->rule, $this->reviewers))->handle();

        $users = User::whereIn('id', $newAssignees)->get();

        logger('sending notification for review assignment');
        Notification::send($users, new RuleReviewRequestedNotification($this->rule, $this->requester));
    }

}
