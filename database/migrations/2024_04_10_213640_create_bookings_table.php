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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('user_id');
            $table->foreignUuid('photographer_id');
            $table->string('acara');
            $table->string('lokasi');
            $table->dateTime('sesi_foto');
            $table->dateTime('tanggal_booking');
            $table->integer('durasi');
            $table->string('konsep');
            $table->enum('status', ['ditolak', 'selesai', 'menunggu_dp', 'menunggu_konfirmasi', 'proses_editing', 'proses', 'menunggu_pelunasan']);
            $table->integer('total_harga');
            $table->integer('total_dp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
