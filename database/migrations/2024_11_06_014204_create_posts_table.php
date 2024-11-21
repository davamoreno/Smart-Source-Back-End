<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paper_type_id')->constrained('post_paper_types')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('post_categories')->cascadeOnDelete();


            $table->string('title', 255);
            $table->string('description', 255);
            $table->string('slug')->unique();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');

            $table->enum('file_type', ['word', 'pdf', 'excel']);
            $table->enum('status', ['pending', 'published', 'denied'])->default('pending');

            $table->timestamp('approve_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
