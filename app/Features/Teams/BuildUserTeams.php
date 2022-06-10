<?php

namespace App\Features\Teams;

use App\Features\BaseFeature;
use App\Services\MySgs\Api\JobApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BuildUserTeams extends BaseFeature
{
    /** @var Collection */
    public $allTeams;

    public $members = [];

    /** @var Collection */
    public $allMembers;

    public function handle()
    {
        $this->allTeams = \Cache::remember('all-teams', Carbon::now()->addDay(), function () {
            return collect(JobApi::allTeams())
                ->filter(function ($entry) {
                    return !$entry->teamDeleted;
                });
        });

        /** @var Collection $chunk */
        foreach ($this->allTeams->chunk(10) as $chunk) {
            $this->getTeamMembersForTeams($chunk);
        }

        $this->allMembers = collect(array_values($this->members));

        return $this;
    }

    /**
     * @param  Collection  $teams
     * @return void
     */
    protected function getTeamMembersForTeams($teams)
    {
        $teamIds = $teams->pluck('teamId')->all();
        $data = \Cache::remember(
            'team-members-'.implode('-', $teamIds),
            Carbon::now()->addDay(),
            function () use ($teamIds) {
                return JobApi::teamMembers($teamIds);
            });

        foreach ($data->members as $member) {
            if (!isset($this->members[$member->id])) {
                $member->teams = [];
                $this->members[$member->id] = $member;
            }
        }

        foreach ($data->teams as $teamObj) {
            $team = $this->allTeams->firstWhere('teamId', $teamObj->id);

            foreach ($teamObj->members as $memberId) {
                $existingTeams = collect($this->members[$memberId]->teams);
                $this->members[$memberId]->teams = $existingTeams->add($team->teamName)->unique()->toArray();
            }
        }
    }
}
