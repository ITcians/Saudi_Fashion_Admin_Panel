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
            $table->longText('post');
            $table->string('cover')->nullable();
            $table->integer('allow_comments')->default(1);
            $table->integer('visibiliy')->default(1);
            $table->integer('is_drafted')->default(1);
            $table->integer('status')->default(200);
            $table->unsignedBigInteger('created_by')->constrain('users')->onDelete('cascade');
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
