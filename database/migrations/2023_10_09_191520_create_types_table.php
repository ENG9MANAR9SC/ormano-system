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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 400)->nullable();
            $table->string('slug', 400)->nullable();
            $table->text('descriptions')->nullable();
            $table->text('properties')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('app_model_id')->constrained();
            $table->unsignedInteger('attribute_set_id')->nullable();
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
        Schema::dropIfExists('types');
    }
};
