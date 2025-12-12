<?php

namespace App\Data;

use App\Data\CommentData;
use App\Data\TagData;
use App\Data\UserData;
use App\Enums\Status;
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
        #[WithCast(EnumCast::class, type: Status::class)]
        public Status $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public ?CarbonImmutable $published_at,
        /** @var array<CommentData> */
        public array $comments,
        /** @var array<TagData> */
        public array $tags,
        public UserData $user,
    ) {}
}
