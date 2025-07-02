<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'maincat_id',
        'subcategory_name',
        'slug',
        'image',
        'status',
        'is_premium',
        'coins',
        'row_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_premium' => 'boolean',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'maincat_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public static function isUniqueSlug($slug, $id = null)
    {
        $query = self::where('slug', $slug);
        
        if ($id) {
            $query->where('id', '!=', $id);
        }
        
        return $query->count() === 0;
    }
}