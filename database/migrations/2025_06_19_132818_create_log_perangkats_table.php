<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_perangkats', function (Blueprint $table) {
            $table->id();
            $table->string('pegawai_id', 22)->unique();
            $table->string('id_opd', 3);
            $table->string('id_perangkat', 2);
            $table->string('tahun', 4);
            $table->string('bulan', 2);
            $table->string('tanggal', 2);
            $table->string('jam', 2);
            $table->string('menit', 2);
            $table->string('detik', 1);
            $table->string('karakter_unik', 4);
            $table->string('keseluruhan', 22);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_perangkats');
    }
};
