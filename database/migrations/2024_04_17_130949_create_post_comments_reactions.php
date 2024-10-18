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
        Schema::create('post_comments_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id')->constrain('post_comments')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->constrain('users')->onDelete('cascade');
            $table->string('reaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments_reactions');
    }
};
