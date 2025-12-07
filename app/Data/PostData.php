<?php

namespace App\Data;

use App\Enums\PostStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class PostData extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        #[WithCast(EnumCast::class, type: PostStatus::class)]
        public PostStatus $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $published_at,
        public UserData $user,
        /** @var array<CommentData> */
        public array $comments,
        /** @var array<TagData> */
        public array $tags,
    ) {}
}
