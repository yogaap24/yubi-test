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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_buyer_no')->unique();
            $table->date('order_at');
            $table->date('shipping_at')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_type_id');
            $table->unsignedBigInteger('currency_id');
            $table->mediumText('ship_dest');
            $table->string('status');
            $table->decimal('exchange_rate', 15, 3);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total_discount', 15, 2);
            $table->decimal('grand_total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
