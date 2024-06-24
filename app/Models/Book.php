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
        'content',
        'content_audio'
    ];

    public function favorites(){
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
