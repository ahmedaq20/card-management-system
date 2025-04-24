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
        Schema::table('mikrotik_users', function (Blueprint $table) {
            $table->date('start_date_of_subscription')->nullable()->after('date_of_subscription'); // New column
            $table->date('end_date_of_subscription')->nullable()->after('start_date_of_subscription'); // New column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mikrotik_users', function (Blueprint $table) {
            $table->dropColumn('start_date_of_subscription'); // Drop column
            $table->dropColumn('end_date_of_subscription');   // Drop column
        });
    }
};
