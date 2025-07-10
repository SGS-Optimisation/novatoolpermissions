<?php

namespace Silvanite\NovaToolPermissions;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Silvanite\NovaToolPermissions\Role;

class NovaToolPermissions extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            Role::class,
        ]);
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return [
            'label' => 'Permissions',
            'uriKey' => 'permissions',
        ];
    }
}
