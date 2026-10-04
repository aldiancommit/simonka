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
        Schema::create('penjadwalan_ulangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasis')->cascadeOnDelete();
            $table->date('tanggal_lama');
            $table->date('tanggal_baru');
            $table->time('waktu_mulai_baru');
            $table->time('waktu_selesai_baru')->nullable();
            $table->text('alasan');
            $table->string('status')->default('Menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjadwalan_ulangs');
    }
};
