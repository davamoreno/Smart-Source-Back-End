<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Post;
use App\Http\Controllers\Auth\Admin;
use App\Http\Controllers\Auth\Member;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

//Member Auth
Route::post('/member/register', [Member\AuthController::class, 'register']);
Route::post('/member/login', [Member\AuthController::class, 'login']);
Route::middleware(['auth:sanctum', 'role:member'])->post('/member/logout', [Member\AuthController::class, 'logout']);

//Super Admin Create Admin
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    Route::post('/admin/register', [Admin\AuthController::class, 'register']);
});

// Super Admin and Admin Login
Route::post('/admin/login', [Admin\AuthController::class, 'login']);

//Admin and Super Admin Management
Route::middleware(['auth:sanctum', 'role:admin|super_admin'])->group(function () {
    Route::get('/admin/profile', [Admin\AuthController::class, 'getAdminProfile']);
    Route::post('/admin/logout', [Admin\AuthController::class, 'logout']);
    Route::post('/category', [Post\CategoryController::class, 'create']);
    Route::post('/papertype', [Post\PaperTypeController::class, 'create']);
    Route::post('/university', [Post\Admin\UniversityController::class, 'store']);
    Route::post('/faculty', [Post\Admin\FacultyController::class, 'store']);
    Route::delete('/delete/papertype/{id}', [Post\PaperTypeController::class, 'destroy']);
    Route::delete('/delete/category/{id}', [Post\CategoryController::class, 'destroy']);
    Route::delete('/delete/university/{id}', [Post\Admin\UniversityController::class, 'destroy']);
    Route::get('/post/pending', [Post\PostController::class, 'showPostPending']);
    Route::put('/post/validation/{id}', [Post\PostController::class, 'validatePost']);
    Route::put('/post/report/handle/{id}', [Post\ReportController::class, 'validatePostReport']);
    Route::get('/post/report/pending', [Post\ReportController::class, 'getAllReport']);
});

Route::get('/user/post/{slug}', [Post\PostController::class, 'getDetailPost']);
Route::get('/post/report/{id}', [Post\ReportController::class, 'getReport']);

//Member Features
Route::middleware(['auth:sanctum', 'role:member'])->group(function () {
    Route::post('/create/post', [Post\PostController::class, 'create']);
    Route::get('/user/profile', [Member\AuthController::class, 'getUserProfile']);
    Route::post('/member/image', [Member\AuthController::class, 'createUserImage']);
    Route::post('/post/report/{id}', [Post\ReportController::class, 'userReportPost']);
    Route::post('/post/comment/{slug}', [Post\CommentController::class, 'mainComment']);
    Route::post('/post/comment/{slug}/{commentId}', [Post\CommentController::class, 'addReplyComment']);
    Route::get('/post/comment/{slug}', [Post\CommentController::class, 'showMainComment']);
    Route::get('/post/comment/{slug}/{commentId}', [Post\CommentController::class, 'showReplyComment']);
    Route::post('/edit/profileImage', [Member\AuthController::class, 'editUserImage']);
    Route::get('/post/bookmark', [Post\BookmarkController::class, 'show']);
    Route::post('/post/bookmark/{post}', [Post\BookmarkController::class, 'create']);
    Route::delete('/post/bookmark/{post}', [Post\BookmarkController::class, 'delete']);
    Route::post('/post/like/{slug}', [Post\LikeController::class, 'create']);
    Route::delete('/post/like/{slug}', [Post\LikeController::class, 'delete']);
    Route::get('/user/mypost', [Post\PostController::class, 'getMyPost']);
    Route::post('/user/edit/profile', [Member\AuthController::class, 'updateProfile']);
    Route::put('/user/post/edit/{slug}',  [Post\PostController::class, 'update']);
    Route::delete('/user/post/delete/{slug}', [Post\PostController::class, 'delete']);

    Route::get('public/files/{filename}', function (string $filename) {
        $filePath = 'files/'.$filename;
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        $fileContent = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);

        return response($fileContent, 200)->header('Content-Type', $mimeType);
    });

    Route::post('/user/history/{slug}', [Post\HistoryController::class, 'create']);
});

Route::get('/post/like/{post}', [Post\LikeController::class, 'show']);
Route::get('/user/post', [Post\PostController::class, 'showUserPost']);
Route::get('/user/post/deny', [Post\PostController::class, 'showDenyPost']);
Route::get('/get/faculties', [Post\Admin\FacultyController::class, 'index']);
Route::get('/get/universities', [Post\Admin\UniversityController::class, 'index']);
Route::get('/get/categories', [Post\CategoryController::class, 'index']);
Route::get('/get/papertypes', [Post\PaperTypeController::class, 'getPaperTypes']);