<?php


namespace App\Services\Rule;


use App\Models\Rule;
use App\Models\Team;

class GetRulesForRegion
{
    public static function handle($region = null)
    {
        return Rule::query()
            ->when($region, function($query) use ($region) {
                $query->whereHas('teams', function($teamQuery) use ($region) {
                    $teamQuery->where('region', $region);
                });
            })
            ;


    }
}
