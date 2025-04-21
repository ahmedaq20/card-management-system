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
        Schema::create('mikrotik_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mikrotik_user_id'); 
            $table->decimal('amount', 10, 2); 
            $table->date('payment_date'); 
            $table->string('description')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('mikrotik_user_id')->references('id')->on('mikrotik_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_payments');
    }
};