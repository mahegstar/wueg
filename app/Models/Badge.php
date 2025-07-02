<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'type',
        'badge_label',
        'badge_icon',
        'badge_note',
        'badge_reward',
        'badge_counter',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }
}