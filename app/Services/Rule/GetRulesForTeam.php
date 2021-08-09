<?php


namespace App\Services\Rule;


use App\Models\Rule;
use App\Models\Team;

class GetRulesForTeam
{
    public static function handle(Team|int $team, $region = null)
    {
        return Rule::query()
            ->whereHas('teams', function ($query) use ($team) {
                $query->where('id', is_int($team) ? $team : $team->id);
            })
            ->when($region, function ($query) use ($region) {
                $query->whereHas('teams', function ($teamQuery) use ($region) {
                    $teamQuery->where('region', $region);
                });
            });

        /*
        return Rule::query()
            ->select([
                'rules.*',
                'teams.name as teamName', 'teams.region'
            ])
            ->join('rule_team', 'rule_id', '=', 'rules.id')
            ->join('teams', 'teams.id', '=', 'rule_team.team_id')
            ->where('teams.id', is_int($team) ? $team : $team->id);
        */
    }
}
