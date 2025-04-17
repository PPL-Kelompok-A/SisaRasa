<?php

namespace App\Policies;

use App\Models\Food;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FoodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'mitra';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Food $food): bool
    {
        return $user->id === $food->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'mitra';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Food $food): bool
    {
        return $user->id === $food->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Food $food): bool
    {
        return $user->id === $food->user_id;
    }
}
