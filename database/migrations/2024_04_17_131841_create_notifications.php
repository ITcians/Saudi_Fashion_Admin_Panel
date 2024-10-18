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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('send_to_user_id')->constrain('users')->onDelete('cascade');
            $table->unsignedBigInteger('send_from_user_id')->constrain('users')->nullable()->onDelete('cascade');
            //if send_from is null it will be considered by system
            $table->string('title');
            $table->string('body');
            $table->integer('is_read')->default(0);
            $table->string('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
