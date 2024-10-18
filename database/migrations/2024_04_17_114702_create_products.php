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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText("description");
            $table->longText("care_advice");
            $table->text("material");
            $table->integer('status')->default(300); // 300 this is incomplete || When complete the submission process status 200
            $table->double('price')->default(0.0);
            $table->unsignedBigInteger('created_by')->constrain('users')->onDelete("cascade");
            $table->unsignedBigInteger('category_id')->constrain('categories')->onDelete("cascade")->default(0);
            $table->unsignedBigInteger('sub_category_id')->constrain('sub_categories')->onDelete("cascade")->default(0);
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
