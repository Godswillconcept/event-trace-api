<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Faq extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['event_id', 'questions', 'answers', 'user_id'];

    protected $casts = ['answers' => 'array', 'questions' => 'array'];

    public function events() {
        return $this->belongsToMany(Event::class, 'event_faqs', 'faq_id', 'event_id');
    }

}
