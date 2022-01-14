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

    /**
     * DraftToPublished constructor.
     * @param  Rule  $rule
     * @param  User  $user
     */
    public function __construct(public Rule $rule, public ?User $user = null)
    {
        if (!$this->user) {
            $this->user = request()->user();
        }
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
