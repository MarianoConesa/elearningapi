<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'file',
        'course_id',
        'title',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
