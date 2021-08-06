<?php


namespace App\Services\Rule;


use App\Models\Rule;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class GetRulesByTeam
{

    public static function handle(Team $team)
    {
        return Rule::query()
            ->select(['rules.id', 'rules.name', 'rules.created_at', 'rules.updated_at'])
            ->distinct()
            ->join('rule_user', 'rule_user.rule_id', '=', 'rules.id')
            ->join('users', 'users.id', '=', 'rule_user.user_id')
            ->leftJoin('team_user', 'team_user.user_id', '=', 'users.id')
            ->join('teams', function (\Illuminate\Database\Query\JoinClause $join) {
                $join->on('teams.user_id', '=', 'users.id')
                    ->orOn('teams.id', '=', 'team_user.team_id')
                ;
            })
            ->whereRaw('rules.client_account_id=teams.client_account_id')
            ->whereNull('rules.deleted_at')
            ->whereRaw('teams.id=?', [$team->id])
            ;


    }
}
