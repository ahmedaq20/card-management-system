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
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade'); // علاقة مع جدول البائعين
            $table->date('date'); // حقل التاريخ
            $table->integer('quantity_sold'); // عدد البطاقات المباعة
            $table->decimal('amount_paid', 10, 2); // المبلغ المحصل
            // $table->decimal('unit_price', 10, 2); // سعر الوحدة
            $table->text('notes')->nullable(); // الملاحظات (اختياري)
            $table->timestamps(); // تواريخ الإنشاء والتحديث
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