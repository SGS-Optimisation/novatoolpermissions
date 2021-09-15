<?php


namespace App\Transitions\Rules;


use App\Features\Rules\AssignAndNotifyRuleReviewersFeature;
use App\Models\Rule;
use App\Models\Team;
use App\Models\User;
use App\Notifications\RuleReviewRequestedNotification;
use App\Operations\Rules\AssignRuleReviewersOperation;
use App\States\Rules\PublishedState;
use App\States\Rules\ReviewingState;
use Illuminate\Support\Facades\Notification;
use Spatie\ModelStates\Transition;

class RequestReview extends Transition
{

    /**
     * RequestReview constructor.
     * @param  Rule  $rule
     * @param  User  $user
     * @param  array|null  $reviewers
     */
    public function __construct(public Rule $rule, public User $user, public ?array $reviewers = [])
    {
    }

    public function handle(): Rule
    {
        $this->rule->state = ReviewingState::class;
        $this->rule->save();

        (new AssignAndNotifyRuleReviewersFeature($this->rule, $this->user, $this->reviewers))->handle();

        return $this->rule;
    }

    public function canTransition(): bool
    {
        return true;
    }

}
