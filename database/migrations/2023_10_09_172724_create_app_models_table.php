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
		Schema::create('app_models', function (Blueprint $table) {
			$table->id()->autoIncrement();
			$table->string('title', 400)->nullable();
			$table->string('slug', 400)->nullable();
			$table->text('description')->nullable();
			$table->text('path');
			$table->string('status');
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
		Schema::dropIfExists('app_models');
	}
};
