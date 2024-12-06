<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\HTTP\Controllers\Auth\Member;
use App\HTTP\Controllers\Auth\Admin;
use App\HTTP\Controllers\Post;

//Member Auth
Route::post('/member/register', [Member\AuthController::class, 'register']);
Route::post('/member/login',    [Member\AuthController::class, 'login']);

//Admin Auth
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::post('/admin/register', [Admin\AuthController::class, 'register']);
});

Route::post('/admin/login',    [Admin\AuthController::class, 'login']);

//Post
Route::middleware(['auth:sanctum', 'role:member'])->group(function () {
    Route::get('/user/post', [Post\PostController::class, 'index']);
    Route::post('/create/post', [Post\PostController::class, 'create']);
});

