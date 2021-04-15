<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
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
        return $user->canAny(['viewTaxonomies', 'manageTaxonomies']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Taxonomy  $taxonomy
     * @return mixed
     */
    public function view(User $user, Taxonomy $taxonomy)
    {
        return $user->can('manageTaxonomies');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('manageTaxonomies');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Taxonomy  $taxonomy
     * @return mixed
     */
    public function update(User $user, Taxonomy $taxonomy)
    {
        return $user->can('manageTaxonomies');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Taxonomy  $taxonomy
     * @return mixed
     */
    public function delete(User $user, Taxonomy $taxonomy)
    {
        return $user->can('manageTaxonomies');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Taxonomy  $taxonomy
     * @return mixed
     */
    public function restore(User $user, Taxonomy $taxonomy)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Taxonomy  $taxonomy
     * @return mixed
     */
    public function forceDelete(User $user, Taxonomy $taxonomy)
    {
        //
    }
}
