<?php

namespace App\Policies;

use App\Models\ReplicationInnovation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplicationInnovationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ReplicationInnovation  $replicationInnovation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ReplicationInnovation $replicationInnovation)
    {
        // Allow viewing if the user is the person in charge or has admin/superadmin role
        return $user->id === $replicationInnovation->person_in_charge || $user->role === 'Admin' || $user->role === 'Superadmin';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ReplicationInnovation  $replicationInnovation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ReplicationInnovation $replicationInnovation)
    {
        return $user->id === $replicationInnovation->person_in_charge || $user->role === 'Admin' || $user->role === 'Superadmin';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ReplicationInnovation  $replicationInnovation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ReplicationInnovation $replicationInnovation)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ReplicationInnovation  $replicationInnovation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ReplicationInnovation $replicationInnovation)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ReplicationInnovation  $replicationInnovation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ReplicationInnovation $replicationInnovation)
    {
        //
    }
}