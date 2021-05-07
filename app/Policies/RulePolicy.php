<?php

namespace App\Policies;

use App\Models\ClientAccount;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rule  $rule
     * @return mixed
     */
    public function view(User $user, Rule $rule)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  ClientAccount  $clientAccount
     * @return mixed
     */
    public function create(User $user, ClientAccount $clientAccount = null)
    {
        return $clientAccount
            && $user->can('createRules') && $user->belongsToTeam($clientAccount->team)
            || $user->can('forceCreateRules');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rule  $rule
     * @return mixed
     */
    public function update(User $user, Rule $rule)
    {
        return $user->can('updateRules') && $user->belongsToTeam($rule->clientAccount->team)
            || $user->can('forceCreateRules');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rule  $rule
     * @return mixed
     */
    public function delete(User $user, Rule $rule)
    {
        return $user->can('deleteRules')
            && $user->belongsToTeam($rule->clientAccount->team);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rule  $rule
     * @return mixed
     */
    public function restore(User $user, Rule $rule)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Rule  $rule
     * @return mixed
     */
    public function forceDelete(User $user, Rule $rule)
    {
        //
    }
}
