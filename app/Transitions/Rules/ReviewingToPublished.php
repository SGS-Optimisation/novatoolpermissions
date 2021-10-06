<?php


namespace App\Transitions\Rules;


use App\Models\Rule;
use App\Models\Team;
use App\Models\Term;
use App\Models\User;
use App\Operations\Rules\UnassignRuleReviewersOperation;
use App\States\Rules\PublishedState;
use Spatie\ModelStates\Transition;

class ReviewingToPublished extends Transition
{

    private Rule $rule;

    private User $user;

    /**
     * DraftToPublished constructor.
     * @param  Rule  $rule
     * @param  User  $user
     */
    public function __construct(Rule $rule, User $user)
    {
        $this->rule = $rule;
        $this->user = $user;
    }

    public function handle(): Rule
    {
        $this->rule->state = PublishedState::class;
        $this->rule->save();

        (new UnassignRuleReviewersOperation($this->rule))->handle();

        return $this->rule;
    }

    public function canTransition(): bool
    {
        $team = $this->rule->clientAccount ? $this->rule->clientAccount->team : new Team();


        return $this->rule->isPublishable()
            && $this->user->hasRoleWithPermission('publishRules')
            || $this->user->hasTeamPermission($team, 'publishRules');
    }

}
