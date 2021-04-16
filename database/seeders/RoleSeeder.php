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

        $permissions = [
            'sysadmin' => [
                'viewNova', 'viewRoles', 'manageRoles', 'assignRoles', 'viewUsers', 'manageUsers',
                'canBeGivenAccess',
                'forceCreateRules', 'createRules', 'updateRules', 'deleteRules',
                'createClientAccounts', 'updateClientAccounts', 'deleteClientAccounts',
                'accessPM',
                'viewTaxonomies', 'manageTaxonomies',
                'viewTerms', 'manageTerms',
                'viewFieldMappings', 'manageFieldMappings',
                'manageSettings',
                'manageTeams',
            ],
            'team-leader' => [
                'viewNova', 'viewRoles', 'assignRoles', 'viewUsers', 'manageUsers',
                'canBeGivenAccess',
                'createRules', 'updateRules', 'deleteRules',
                'createClientAccounts', 'updateClientAccounts',
                'accessPM',
                'viewTaxonomies', 'manageTaxonomies',
                'viewTerms', 'manageTerms',
                'viewFieldMappings',
                'manageTeams',
            ],
            'project-manager' => [
                'viewNova', 'viewUsers',
                'canBeGivenAccess',
                'createRules', 'updateRules',
                'createClientAccounts', 'updateClientAccounts',
                'accessPM',
                'viewTaxonomies',
                'viewTerms',
                'viewFieldMappings',
            ],
        ];

        foreach ($roles as $role) {
            /** @var Role $role */
            $role = Role::firstOrCreate($role);

            $role->setPermissions($permissions[$role->slug]);
        }
    }
}
