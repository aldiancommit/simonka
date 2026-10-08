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
        Schema::table('penjadwalan_ulangs', function (Blueprint $table) {
            $table->date('snapshot_tanggal_lama')->nullable()->after('waktu_selesai_baru');
            $table->time('snapshot_waktu_mulai_lama')->nullable()->after('snapshot_tanggal_lama');
            $table->time('snapshot_waktu_selesai_lama')->nullable()->after('snapshot_waktu_mulai_lama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjadwalan_ulangs', function (Blueprint $table) {
            $table->dropColumn([
                'snapshot_tanggal_lama',
                'snapshot_waktu_mulai_lama',
                'snapshot_waktu_selesai_lama',
            ]);
        });
    }
};
