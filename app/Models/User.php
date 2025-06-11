<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profilePic',
        'liked',
        'followed',
        'ended',
    ];

    protected $casts = [
        'liked',
        'followed',
        'ended',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }

    public function Courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'profilePic', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isAdmin()
    {
        return $this->id === 1;
    }
}
