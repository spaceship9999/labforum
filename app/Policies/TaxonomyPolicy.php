<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Taxonomy $taxonomy) {
        return $user->can('access_' . $taxonomy->slug . '_taxonomy') || $user->isAdmin() || $taxonomy->isFreeToPublic();
    }
}
