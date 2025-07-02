<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'code',
        'status',
        'type',
        'default_active',
    ];

    protected $casts = [
        'status' => 'boolean',
        'default_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    public function contests()
    {
        return $this->hasMany(Contest::class);
    }
}