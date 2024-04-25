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
        Schema::create('galleries', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->timestamps();
            $table->foreignUuid('booking_id');
            $table->foreignUuid('user_id');
            $table->foreignUuid('photographer_id');
            $table->string('name_photographer');
            $table->json('photos');
            $table->integer('total_images');
            $table->integer('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
