<?php

namespace App\Console\Commands;

use App\Features\Teams\BuildUserTeams;
use App\Models\User;
use Illuminate\Console\Command;

class PopulateUserJobTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:jobteam:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate job teams for users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $builder = (new BuildUserTeams)->handle();

        /** @var User $user */
        foreach (User::lazy(100) as $user) {
            $member = $builder->allMembers
                ->where('firstName', $user->given_name)
                ->where('lastName', $user->surname)
                ->first();

            if ($member) {
                $user->jobteams = $member->teams;
            } else {
                $user->jobteams = [];
            }
            $user->save();
        }

        return 0;
    }
}
