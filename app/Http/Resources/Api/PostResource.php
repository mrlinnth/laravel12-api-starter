<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'published_at' => $this->published_at,
            'comments' => CommentCollection::make($this->whenLoaded('comments')),
            'tags' => TagCollection::make($this->whenLoaded('tags')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
