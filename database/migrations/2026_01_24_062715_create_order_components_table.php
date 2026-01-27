<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('component_id')->constrained();

            // SNAPSHOT FIELDS
            $table->string('component_name');
            $table->string('component_unit')->nullable();

            $table->decimal('quantity_per_unit', 10, 2);
            $table->decimal('total_quantity', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_components');
    }
};
