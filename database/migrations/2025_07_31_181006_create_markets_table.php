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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->datetime('closes_at');
            $table->boolean('resolved')->default(false);
            $table->enum('outcome', ['yes', 'no', 'unknown'])->default('unknown');
            $table->decimal('liquidity', 10, 2)->default(1000);
            $table->decimal('b', 10, 2)->default(100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
