<?php

namespace App\Models;

use App\Http\Controllers\TaxonomyController;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'object',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function taxonomy() {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');
    }
}
