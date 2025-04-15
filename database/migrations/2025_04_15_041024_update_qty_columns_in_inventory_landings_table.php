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
        Schema::table('inventory_landings', function (Blueprint $table) {
            $table->dropColumn(['qty_out', 'qty_remaining']);
            $table->integer('qty_borrowed')->default(0)->after('tanggal_kembali');
            $table->integer('qty_returned')->default(0)->after('qty_borrowed');
            $table->integer('qty_pending_return')->default(0)->after('qty_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_landings', function (Blueprint $table) {
            $table->integer('qty_out')->default(0)->after('tanggal_kembali');
            $table->integer('qty_remaining')->default(0)->after('qty_out');
            $table->dropColumn(['qty_borrowed', 'qty_returned', 'qty_pending_return']);
        });
    }
};
