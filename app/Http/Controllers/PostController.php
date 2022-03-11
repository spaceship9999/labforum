<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
    //
    function getPost($id) {
        if ($this->hasPost($id)) {
            $post = Post::where('posts.id', $id)
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->join('taxonomies', 'posts.tax_id', '=', 'taxonomies.id')
                ->select('users.id as user_id', 'username', 'role_id', 'taxonomies.name AS tax_name',
                    'taxonomies.introduction AS tax_introduction', 'taxonomies.data'
                    ,'posts.*')
                ->get()
                ->toArray();

            if(!Auth::check()) {
               if ($post[0]['visible'] === 0 || $post[0]['is_deleted'] === 1 || $post[0]['is_draft'] === 1)
                   return $this->postNotVisible();
            }
            return $post;
        }else{
            return response($this->postNotExist(), 404);
        }
    }

    function getChildrenPost() {

    }


    function hasPost($id) {
        return Post::where('id' , $id)
            ->where('is_draft', '<>', 1)
            ->where('is_deleted', '<>', 1)
            ->where('visible', '<>',  0)
            ->count();
    }

    function haveChildrenPost($id)
    {

    }

    function postNotExist()
    {
        return array(
            'error' => 'post_not_exists',
        );
    }

    function postNotVisible() {
        return array(
            'error' => 'post_not_visible',
        );
    }
}
