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
        Schema::table('financial_payments', function (Blueprint $table) {
            $table->date('date')
            ->nullable()
            ->default(now()->toDateString()) // Set the default value to today's date
            ->after('amount'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_payments', function (Blueprint $table) {
            $table->dropColumn('date'); // Remove the date column if the migration is rolled back

        });
    }
};