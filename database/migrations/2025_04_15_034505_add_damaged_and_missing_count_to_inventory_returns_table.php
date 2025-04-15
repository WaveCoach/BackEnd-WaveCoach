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
        Schema::table('inventory_returns', function (Blueprint $table) {
            $table->integer('damaged_count')->default(0)->after('status');
            $table->integer('missing_count')->default(0)->after('damaged_count');
            $table->integer('image_return')->default(0)->after('missing_count');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_returns', function (Blueprint $table) {
            $table->dropColumn(['damaged_count', 'missing_count', 'image_return']);
        });
    }
};
