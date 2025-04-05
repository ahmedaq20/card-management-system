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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sales_point');
            $table->string('phone');
            $table->integer('cards_sold')->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('remaining_dues', 10, 2)->default(0);
            $table->json('payments')->nullable();
            $table->decimal('wholesale_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
