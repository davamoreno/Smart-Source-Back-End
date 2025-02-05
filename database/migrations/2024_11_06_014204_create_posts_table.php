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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('paper_type_id')->nullable()->constrained('paper_types')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('approve_by')->nullable()->constrained('users')->nullOnDelete();

            $table->bigInteger('likes_count')->default(0);
            $table->string('title', 255);
            $table->string('description', 255);
            $table->string('slug')->unique();
            $table->enum('status', ['pending', 'allow', 'deny'])->default('pending');
            $table->enum('report_status', ['null' , 'pending', 'accept', 'reject'])->default('null');

            $table->timestamp('approve_at')->nullable();
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
