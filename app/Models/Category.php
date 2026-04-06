<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'lang',
        'ref_key',
        'description',
        'meta_title',
        'meta_description',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
