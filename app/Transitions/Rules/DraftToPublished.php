<?php


namespace App\Transitions\Rules;


use App\Models\Rule;
use App\Models\Team;
use App\Models\User;
use App\States\Rules\PublishedState;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\Transition;

class DraftToPublished extends Transition
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

        return $this->rule;
    }

    public function canTransition(): bool
    {
        $teams = $this->rule->clientAccount ? $this->rule->clientAccount->teams : [new Team()];

        $canPublishInTeam = false;
        foreach($teams as $team) {
            $canPublishInTeam |= $this->user->hasTeamPermission($team, 'publishRules');
        }

        return $this->rule->isPublishable()
            && $this->user->hasRoleWithPermission('publishRules')
            || $canPublishInTeam;
    }

}
