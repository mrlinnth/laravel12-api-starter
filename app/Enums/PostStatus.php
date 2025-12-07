<?php

namespace App\Enums;

use App\Traits\EnumArray;

enum PostStatus: string
{
    use EnumArray;

    case draft = 'draft';
    case published = 'published';
    case archived = 'archived';

    public static function default(): self
    {
        return self::draft;
    }
}
