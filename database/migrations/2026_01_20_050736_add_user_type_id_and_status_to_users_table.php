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
    Schema::table('users', function (Blueprint $table) {

        if (!Schema::hasColumn('users', 'user_type_id')) {
            $table->foreignId('user_type_id')
                  ->after('id')
                  ->constrained('user_types');
        }

        if (!Schema::hasColumn('users', 'status')) {
            $table->tinyInteger('status')
                  ->default(1)
                  ->after('user_type_id');
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {

        if (Schema::hasColumn('users', 'user_type_id')) {
            $table->dropForeign(['user_type_id']);
            $table->dropColumn('user_type_id');
        }

        if (Schema::hasColumn('users', 'status')) {
            $table->dropColumn('status');
        }
    });
}

};
