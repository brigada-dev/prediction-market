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
        Schema::create('token_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_provider'); // 'stripe' or 'coinbase'
            $table->string('payment_id')->unique(); // Stripe payment intent ID or Coinbase charge ID
            $table->integer('tokens_purchased'); // Number of tokens purchased
            $table->decimal('amount_paid', 10, 2); // Amount paid in fiat currency
            $table->string('currency', 3); // USD, EUR, etc.
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->json('payment_data')->nullable(); // Store additional payment provider data
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('payment_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_purchases');
    }
};
