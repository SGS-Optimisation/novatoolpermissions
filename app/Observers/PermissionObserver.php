<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use Silvanite\Brandenburg\Permission;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     *
     * @param  \Silvanite\Brandenburg\Permission  $permission
     * @return void
     */
    public function created(Permission $permission)
    {
        Cache::tags('roles')->clear();
    }

    /**
     * Handle the Permission "updated" event.
     *
     * @param  \Silvanite\Brandenburg\Permission  $permission
     * @return void
     */
    public function updated(Permission $permission)
    {
        Cache::tags('roles')->clear();
    }

    /**
     * Handle the Permission "deleted" event.
     *
     * @param  \Silvanite\Brandenburg\Permission  $permission
     * @return void
     */
    public function deleted(Permission $permission)
    {
        Cache::tags('roles')->clear();
    }

    /**
     * Handle the Permission "restored" event.
     *
     * @param  \Silvanite\Brandenburg\Permission  $permission
     * @return void
     */
    public function restored(Permission $permission)
    {
        Cache::tags('roles')->clear();
    }

    /**
     * Handle the Permission "force deleted" event.
     *
     * @param  \Silvanite\Brandenburg\Permission  $permission
     * @return void
     */
    public function forceDeleted(Permission $permission)
    {
        //
    }
}
