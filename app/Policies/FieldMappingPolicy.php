<?php

namespace App\Policies;

use App\Models\FieldMapping;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldMappingPolicy
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
        return $user->canAny(['viewFieldMappings', 'manageFieldMappings']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FieldMapping  $fieldMapping
     * @return mixed
     */
    public function view(User $user, FieldMapping $fieldMapping)
    {
        return $user->can('manageFieldMappings');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('manageFieldMappings');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FieldMapping  $fieldMapping
     * @return mixed
     */
    public function update(User $user, FieldMapping $fieldMapping)
    {
        return $user->can('manageFieldMappings');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FieldMapping  $fieldMapping
     * @return mixed
     */
    public function delete(User $user, FieldMapping $fieldMapping)
    {
        return $user->can('manageFieldMappings');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FieldMapping  $fieldMapping
     * @return mixed
     */
    public function restore(User $user, FieldMapping $fieldMapping)
    {
        return $user->can('manageFieldMappings');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FieldMapping  $fieldMapping
     * @return mixed
     */
    public function forceDelete(User $user, FieldMapping $fieldMapping)
    {
        return $user->can('manageFieldMappings');
    }
}
