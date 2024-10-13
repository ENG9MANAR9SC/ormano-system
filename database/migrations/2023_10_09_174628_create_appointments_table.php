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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->integer('duration')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('notes')->nullable();
            $table->integer('type');
            $table->integer('pricing')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->constrained('admins');
            $table->text('properties')->nullable();


            $table->float('cost')->nullable();
            $table->float('discount')->nullable();
            $table->tinyInteger('discount_type')->default(1);
            $table->integer('currency')->nullable();

            $table->unsignedBigInteger('orderable_id')->nullable();

            $table->foreignId('supervisor_id')->nullable()->constrained('admins');

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
        Schema::dropIfExists('appointments');
    }
};
