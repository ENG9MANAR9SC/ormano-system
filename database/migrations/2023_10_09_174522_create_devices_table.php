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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('titles', 400)->default('');
            $table->string('descriptions')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->double('price');
            $table->integer('currency');
            $table->text('properties')->nullable();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->constrained('admins');
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
        Schema::dropIfExists('devices');
    }
};
