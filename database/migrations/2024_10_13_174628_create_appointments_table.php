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
            $table->foreignId('admin_id')->constrained('admins');
            $table->foreignId('case_id')->constrained('cases');
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->constrained('admins');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->text('properties')->nullable();
            $table->float('cost')->nullable();
            $table->float('discount')->nullable();

            $table->unsignedBigInteger('orderable_id')->nullable();

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
