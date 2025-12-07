<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasUserId
{
    /**
     * Auto save auth user id to model.
     */
    public static function bootHasUserId()
    {
        static::creating(function ($model) {
            if (Auth::user() !== null) {
                $model->user_id = Auth::user()->id;
            }
        });
    }
}
