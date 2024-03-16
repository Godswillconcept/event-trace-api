<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;


class Category extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'slug', 'description'];

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
