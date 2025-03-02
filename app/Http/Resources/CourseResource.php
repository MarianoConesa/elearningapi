<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username ?? null,
                'profilePic' => $this->user->image
                    ? asset('storage/' . $this->user->image->file)
                    : null,
            ],
            'miniature' => $this->image
                ? asset('storage/' . $this->image->file)
                : null,
            'video' => $this->video
                ? asset('storage/' . $this->video->file)
                : null,
            'categories' => json_decode($this->catArr, true),
            'isPrivate' => (bool) $this->isPrivate,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'likes' => $this->likes,
        ];
    }
}
