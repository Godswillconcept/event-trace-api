<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Transaction extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['booking_id', 'status', 'payment_method'];

    public function booking() {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
