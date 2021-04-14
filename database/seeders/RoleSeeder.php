<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Silvanite\Brandenburg\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Sysadmin',
                'slug' => 'sysadmin'
            ],
            [
                'name' => 'Team Leader',
                'slug' => 'team-leader'
            ],
            [
                'name' => 'Project Manager',
                'slug' => 'project-manager'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
