<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'likes_count',
        'follows_count',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
