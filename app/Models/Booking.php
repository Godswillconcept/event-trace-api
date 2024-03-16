<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Booking extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['user_id', 'event_id', 'quantity', 'price', 'description', 'ticket_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event() {
        return $this->belongsTo(Event::class,'event_id');
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class,'transaction_id');
    }
}
