<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'read_time',
        'total_views',
        'week_views',
        'cover',
        'content',
        'content_audio'
    ];

    public function scopeInCategories($query, array $categoryIds)
    {
        return $query->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        });
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function keepReadings()
    {
        return $this->belongsToMany(User::class, 'keep_readings')->withTimestamps();
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function chapters(){
        return $this->hasMany(Chapter::class);
    }
}
