<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //
    public function checkRoles() {
        $user = request()->user();
        var_dump($user->hasRole('admin', 'editor'));
    }

    public function getSafeUserDetails(Request $request) {
        $user = $request->user();
        $roles = $user->roles;
        $role_arr = array();

        if (!empty($roles)) {
            foreach ($roles as $role) {
                $role_arr[] = array(
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                );
            }
        }

        return response(
            array(
                'id' => $user->id,
                'role' => $role_arr,
                'username' => $user->username,
                'email' => $user->email,
                'register_date' => $user->created_at,
            )
        );
    }
}
