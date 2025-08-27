<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan untuk Kimia
            if (Schema::hasColumn('kimia_forms', 'judul_tabel')) {
                $table->dropColumn('judul_tabel');
            }
            if (Schema::hasColumn('kimia_forms', 'tgl_inokulasi')) {
                $table->dropColumn('tgl_inokulasi');
            }
            if (Schema::hasColumn('kimia_forms', 'tgl_pengamatan')) {
                $table->dropColumn('tgl_pengamatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            // Kembalikan kolom jika perlu rollback
            $table->string('judul_tabel')->nullable();
            $table->date('tgl_inokulasi')->nullable();
            $table->date('tgl_pengamatan')->nullable();
        });
    }
};
