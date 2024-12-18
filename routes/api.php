<?php

use Illuminate\Http\Request;
use App\HTTP\Controllers\Post;
use App\HTTP\Controllers\Auth\Admin;
use App\HTTP\Controllers\Auth\Member;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

//Member Auth
Route::post('/member/register', [Member\AuthController::class, 'register']);
Route::post('/member/login',    [Member\AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->post('/member/logout', [Member\AuthController::class, 'logout']);

//Admin Auth
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::post('/admin/register', [Admin\AuthController::class, 'register']);
});

//Create Category
Route::middleware(['auth:sanctum', 'role:admin|super_admin'])->group(function () {
    Route::post('/category', [Post\CategoryController::class, 'create']);
    Route::post('/papertype', [Post\PaperTypeController::class, 'create']);
    Route::post('/admin/login',    [Admin\AuthController::class, 'login']);
});


//Member Features
Route::middleware(['auth:sanctum', 'role:member'])->group(function () {
    Route::get('/user/{id}/post', [Post\PostController::class, 'getUserPost']);
    Route::post('/create/post', [Post\PostController::class, 'create']);
    Route::get('/user/profile', [Member\AuthController::class, 'getUserProfile']);
    
    Route::get('/private/files/{filename}', function (string $filename) {
        $filePath = 'files/'.$filename;
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        $fileContent = Storage::disk('local')->get($filePath);
        $mimeType = Storage::disk('local')->mimeType($filePath);

        return response($fileContent, 200)->header('Content-Type', $mimeType);
    });

    Route::get('/public/storage/{filename}', function (string $filename) {
        $filePath = 'profiles/'.$filename;
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found.');
        }
    
        $fileContent = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);
    
        return response($fileContent, 200)->header('Content-Type', $mimeType);
    });
});


Route::get('/user/post', [Post\PostController::class, 'getAllUserPost']);