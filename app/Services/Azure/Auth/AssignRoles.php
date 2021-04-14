<?php


namespace App\Services\Azure\Auth;


use App\Models\User;
use Illuminate\Support\Str;

class AssignRoles
{


    public static function handle(User $user)
    {
        logger('checking roles for new user ' . $user->name);

        $TL_titles = [
            'Team Leader',
            'Director',
            'VP',
        ];

        $PM_titles = [
            'Project Manager',
            'Manager',
            'Chef de Projet',
        ];

        foreach($TL_titles as $title) {
            static::checkTitleStringForRole($user, $title, 'team-leader');
        }

        foreach($PM_titles as $title) {
            static::checkTitleStringForRole($user, $title, 'project-manager');
        }
    }

    protected static function checkTitleStringForRole(User $user, $title, $role)
    {
        if(Str::contains($user->job_title, $title) || Str::contains($title, $user->job_title)) {
            $user->assignRole($role);
        }
    }
}
