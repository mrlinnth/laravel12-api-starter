<?php

namespace App\Data;

use App\Data\PostData;
use App\Data\UserData;
use Spatie\LaravelData\Data;

class CommentData extends Data
{
    public function __construct(
        public string $content,
        public PostData $post,
        public UserData $user,
    ) {}
}
