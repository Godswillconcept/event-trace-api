<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Promotion extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['event_id', 'platform', 'user_id',];

    public function event() {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
