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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('customer_id'); // in this column data comes from users table
            $table->integer('designer_id'); // in this column data comes from users table how created a product
            $table->integer('address_id');
            $table->integer('color_id');
            $table->integer('size_id');
            $table->bigInteger('quantity');
            $table->bigInteger('invoice_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
