<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'courses' => $this->collection->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'user' => [
                        'id' => $course->user->id,
                        'name' => $course->user->name,
                        'username' => $course->user->username ?? null,
                        'profilePic' => $course->user->image
                            ? asset('storage/' . $course->user->image->file)
                            : null,
                    ],
                    'description' => $course->description,
                    'miniature' => $course->image
                        ? asset('storage/' . $course->image->file)
                        : null,
                    'categories' => json_decode($course->catArr, true),
                    'isPrivate' => (bool) $course->isPrivate,
                    'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                    'likes' => $course->likes,
                ];
            }),
            'total_courses' => $this->collection->count(),
        ];
    }
}
