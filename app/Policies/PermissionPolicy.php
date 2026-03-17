<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       //return true;
       return $user->hasPermissionTo('permissao-leitura');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        //return true;
        return $user->hasPermissionTo('permissao-leitura');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return true;
        return $user->hasPermissionTo('permissao-criacao');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        //return true;
        return $user->hasPermissionTo('permissao-atualizar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        //return true;
        return $user->hasPermissionTo('permissao-delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return true;
    }
}
