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
        Schema::create('supplier_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_order_id')->constrained('supplier_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('cost_at_order', 10, 2);
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->integer('received_quantity')->nullable(); // Cantidad recibida, opcional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_order_items');
    }
};