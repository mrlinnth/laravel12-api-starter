<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'OK']);
});


Route::apiResource('tags', App\Http\Controllers\Api\TagController::class);

Route::apiResource('posts', App\Http\Controllers\Api\PostController::class);

Route::apiResource('comments', App\Http\Controllers\Api\CommentController::class);
