<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;


class Event extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'date_time',
        'description',
        'photos',
        'venue_id',
        'category_id',
        'organizer_id',
        'guest_count',
        'slug'
    ];
    protected $casts = [
        'photos' => 'array'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id', 'id');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'event_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($event) {
            $event->slug = Str::slug($event->name);
        });
    }
}
