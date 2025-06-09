<?php

namespace App\Http\Resources;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
{
    $user = $this->user;
    $profilePicUrl = null;

    if ($user && is_numeric($user->profilePic)) {
        $image = Image::find($user->profilePic);
        if ($image && $image->file) {
            $profilePicUrl = asset('storage/' . $image->file);
        }
    }

    return [
        'id' => $this->id,
        'user_id' => $this->user_id,
        'course_id' => $this->course_id,
        'content' => $this->content,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'user' => [
            'id' => $user->id ?? null,
            'name' => $user->name ?? null,
            'username' => $user->username ?? null,
            'profilePic' => $profilePicUrl,
        ],
    ];
}
}
