<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use Silvanite\Brandenburg\Role;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     *
     * @param  \Silvanite\Brandenburg\Role  $role
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Handle the Role "updated" event.
     *
     * @param  \Silvanite\Brandenburg\Role  $role
     * @return void
     */
    public function updated(Role $role)
    {
        Cache::tags('roles')->clear();
    }

    /**
     * Handle the Role "deleted" event.
     *
     * @param  \Silvanite\Brandenburg\Role  $role
     * @return void
     */
    public function deleted(Role $role)
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     *
     * @param  \Silvanite\Brandenburg\Role  $role
     * @return void
     */
    public function restored(Role $role)
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     *
     * @param  \Silvanite\Brandenburg\Role  $role
     * @return void
     */
    public function forceDeleted(Role $role)
    {
        //
    }
}
