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
       Schema::create('components', function (Blueprint $table) {
    $table->id();
    $table->string('component_code')->unique();
    $table->string('component_name');
    $table->string('category');
    $table->string('unit');
    $table->integer('current_stock')->default(0);
    $table->integer('minimum_stock')->default(0);
    $table->decimal('price', 10, 2);
    $table->string('vendor')->nullable();
    $table->text('specifications')->nullable();
    $table->boolean('status')->default(1);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
