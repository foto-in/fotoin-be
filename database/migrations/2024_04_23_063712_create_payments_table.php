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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('booking_id');
            $table->enum('status', ['paid', 'unpaid']);
            $table->integer('total_payment');
            $table->enum('method_payment', ['dummy', 'gopay', 'dana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
