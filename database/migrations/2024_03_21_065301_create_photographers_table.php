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
        Schema::create('photographers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('user_id');
            $table->string('email')->unique();
            $table->string('no_hp');
            $table->string('no_telegram');
            $table->enum('type', ['personal', 'tim']);
            $table->json('specialization');
            $table->json('camera');
            $table->integer('start_price');
            $table->integer('end_price');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photographers');
    }
};
