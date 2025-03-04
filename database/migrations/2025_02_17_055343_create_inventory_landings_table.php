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
        Schema::create('inventory_landings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_landings_id')->nullable();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('mastercoach_id');
            $table->unsignedBigInteger('coach_id');
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->integer('qty_in')->nullable();
            $table->integer('qty_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_landings');
    }
};
