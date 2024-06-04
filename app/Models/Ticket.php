<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Ticket extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'description', 'event_id', 'price', 'quantity', 'sales_start', 'sales_end',];


    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', '');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
