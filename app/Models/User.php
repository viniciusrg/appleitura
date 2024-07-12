<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'email',
        'password',
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
    ];

    public function favorites()
    {
        return $this->belongsToMany(Book::class, 'favorites')->withTimestamps();
    }

    public function keepReadings()
    {
        return $this->belongsToMany(Book::class, 'keep_readings')->withTimestamps();
    }

    public function question()
    {
        return $this->hasOne(Question::class);
    }

    public function categories()
    {
        return $this->BelongsToMany(Category::class, 'user_category');
    }

    public function permission()
    {
        return $this->hasOne(Permission::class);
    }
}
