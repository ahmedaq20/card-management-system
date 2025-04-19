<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mikrotik_users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('phone')->nullable();
            $table->string('user_in_network')->nullable();
            $table->string('password_in_network')->nullable();
            $table->string('last_ip_address')->nullable(); // Renamed from ip_address
            $table->string('last_mac')->nullable(); // New column for MAC address
            $table->date('date_of_subscription')->default(now()->toDateString());
            $table->boolean('is_active')->default(true);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_users');
    }
};