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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->text('titles')->nullable();
            $table->text('descriptions')->nullable();
            $table->text('currency_key')->nullable();
            $table->text('currency_symbol')->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->tinyInteger('system_status')->default(1);
            $table->tinyInteger('user_status')->default(1);
            $table->double('exchange_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
