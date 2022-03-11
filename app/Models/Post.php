<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $casts = ['data' => 'object'];
    public function getParentsAttribute()
    {
        if ($this->hasParent()) {
            return $this->getParent();
        }
    }

    public function getParent($id = null, $depth = null)
    {
        $id = !empty($id) ? $id : $this->id;
        if($depth == null) $depth = 999;
        if ($depth < 0 || !$this->hasParent($id)) return;

        $parent_id = $this->getParentId($id);
        $parent_data = DB::table('posts')->where('id', '=', $parent_id)->toArray();
        $parent_data['parent'] = $this->getParent(null, --$depth);

        return $parent_data;
    }

    protected function hasParent($id = null)
    {
        $id = !empty($id) ? $id : $this->id;

        if ($this->getParentId($id)) {
            $is_post_exists = DB::table('posts')->where('id', '=', $this->parent_post_id)->count();
            return ($is_post_exists);
        }
        return false;
    }

    private function getParentId($id)
    {
        return DB::table('posts')->select('parent_post_id')->where('id', '=', $id)->toArray()['parent_post_id'];
    }

    protected function hasChildren()
    {

    }
}
