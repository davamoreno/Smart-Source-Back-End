<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->text('reason', 255);
            $table->enum('status', ['pending', 'accept', 'reject']);
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
        
        Schema::table('reports', function (Blueprint $table) {
            $table->unique(['post_id', 'user_id'], 'unique_user_post_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
