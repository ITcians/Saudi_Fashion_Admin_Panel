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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('customer_id'); // in this column data comes from users table
            $table->integer('address_id');
            $table->integer('color_id');
            $table->integer('size_id');
            $table->bigInteger('quantity');
            $table->bigInteger('invoice_id');
            $table->string('status')->default(403); // when desginer accept the order the status will be 200
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
