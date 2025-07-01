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
        Schema::create('so_dts', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_uuid');
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->unsignedBigInteger('item_unit_id');
            $table->unsignedBigInteger('item_id');
            $table->string('ref_type');
            $table->string('remark')->nullable();
            $table->decimal('qty', 15, 2);
            $table->decimal('price_sell', 15, 2);
            $table->decimal('price_buy', 15, 2);
            $table->decimal('disc_perc', 5, 2)->nullable();
            $table->decimal('disc_am', 15, 2)->nullable();
            $table->decimal('total_am', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('so_dts');
    }
};
