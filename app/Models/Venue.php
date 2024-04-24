<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;


class Venue extends Model
{
    // use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'address',  'phone', 'website', 'email', 'description', 'photos', 'capacity', 'amenities',  'status', 'slug', 'owner_id'];

    protected $cast = ['amenities' => 'array', 'photos' => 'array'];


    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($venue) {
            $venue->slug = Str::slug($venue->name);
        });
    }
}
