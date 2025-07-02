<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'language_id',
        'question',
        'question_type',
        'optiona',
        'optionb',
        'optionc',
        'optiond',
        'optione',
        'answer',
        'level',
        'note',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function reports()
    {
        return $this->hasMany(QuestionReport::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}