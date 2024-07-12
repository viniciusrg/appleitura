<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function user(){
        return $this->belongsToMany(User::class, 'users');
    }

    public function books(){
        return $this->belongsToMany(Book::class, 'books');
    }
}
