<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number')->unique();
        $table->string('client_name');
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->integer('quantity');
        $table->date('order_date');
        $table->date('expected_delivery');
        $table->decimal('total_amount', 10, 2);
        $table->enum('status', [
            'Order Received',
            'In Production',
            'Ready for Dispatch',
            'Dispatched'
        ])->default('Order Received');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
