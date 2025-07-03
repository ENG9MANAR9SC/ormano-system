<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('admin_id')->constrained('admins');
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreignId('case_id')->constrained('cases');
            $table->string('ip_address', 45)->nullable();
            $table->longText('payload');
            $table->integer('last_activity');
            $table->text('user_agent')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
