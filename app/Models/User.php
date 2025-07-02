<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firebase_id',
        'fcm_id',
        'web_fcm_id',
        'profile',
        'mobile',
        'type',
        'status',
        'coins',
        'refer_code',
        'friends_code',
        'app_language',
        'web_language',
        'date_registered',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_registered' => 'datetime',
    ];

    public function badges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function statistics()
    {
        return $this->hasOne(UserStatistic::class);
    }

    public function battleStatistics()
    {
        return $this->hasMany(BattleStatistic::class);
    }
}