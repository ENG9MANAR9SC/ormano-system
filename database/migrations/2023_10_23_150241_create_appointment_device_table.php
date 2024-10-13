<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_device', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');

            $table->integer('discount')->default(0);
            $table->tinyInteger('discount_type')->default(1);

            $table->unsignedDouble('quantity')->default(1);
            $table->unsignedDouble('price');
            $table->integer('currency');
            $table->unsignedDouble('exchange_rate')->default(1);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_device');
    }
};
