<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'miniature',
        'description',
        'video_id',
        'catArr',
        'isPrivate',
        'likes'
    ];

    protected $casts = [
        'catArr'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function image(): BelongsTo //Image y no miniature porque al tratar con una tabla miniature la detectaría en vez de al método
    {
        return $this->belongsTo(Image::class, 'miniature', 'id');
    }

    public function interaction()
    {
        return $this->hasOne(CourseInteraction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('user');
    }
}
