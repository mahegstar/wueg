<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'name',
        'start_date',
        'end_date',
        'description',
        'image',
        'entry',
        'prize_status',
        'status',
        'date_created',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'date_created' => 'datetime',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function questions()
    {
        return $this->hasMany(ContestQuestion::class);
    }

    public function prizes()
    {
        return $this->hasMany(ContestPrize::class);
    }

    public function leaderboard()
    {
        return $this->hasMany(ContestLeaderboard::class);
    }
}