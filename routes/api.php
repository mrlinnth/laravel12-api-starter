<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);

Route::apiResource('comments', App\Http\Controllers\Api\CommentController::class);

Route::apiResource('tags', App\Http\Controllers\Api\TagController::class);
