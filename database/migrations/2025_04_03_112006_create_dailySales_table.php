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
        Schema::create('dailySales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->date('date');
            $table->string('sales_point');
            $table->integer('quantity_sold');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('remaining_due', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_daily_sales_tables');
    }
};