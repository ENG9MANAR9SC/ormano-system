<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // cases table have many sessions
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->text('desc')->nullable();
            $table->text('type')->nullable();
            $table->integer('price')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('admin_id')->constrained('admins');
            $table->foreignId('court_id')->constrained('courts');
            $table->date('started_at')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
