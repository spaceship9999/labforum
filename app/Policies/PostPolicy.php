<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function post(User $user, Taxonomy $taxonomy) {
        if ($user->can('access_' . $taxonomy->slug . '_taxonomy') || !$user->isAdmin() || !$taxonomy->isFreeToPublic()) {
            return false;
        }
        if (!$user->isAdmin() && !$user->can('post_in_' . $taxonomy->slug . '_taxonomy'))
            return false;


        return true;
    }
}
