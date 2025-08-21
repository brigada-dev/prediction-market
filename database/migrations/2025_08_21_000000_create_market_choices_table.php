<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('party')->nullable();
            $table->string('slug')->index();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Optional: add nullable foreign key to positions for multi-choice
        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'choice_id')) {
                $table->foreignId('choice_id')->nullable()->constrained('market_choices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            if (Schema::hasColumn('positions', 'choice_id')) {
                $table->dropConstrainedForeignId('choice_id');
            }
        });
        Schema::dropIfExists('market_choices');
    }
};


