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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('model_id');

            $table->unsignedBigInteger('orderable_id');
            $table->string('orderable_type');

            $table->integer('type');
            $table->integer('status')->default(1);

            $table->foreignId('user_id')->constrained('users');
            $table->unsignedDouble('sub_total')->default(0);
            $table->unsignedDouble('grand_total')->default(0);
            $table->unsignedDouble('total_paid')->default(0);
            $table->unsignedDouble('total_remaining')->default(0);
            $table->unsignedDouble('discount')->default(0);

            $table->date('date');

            $table->json('fees');

            $table->string('notes')->nullable();

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
        Schema::dropIfExists('orders');
    }
};
