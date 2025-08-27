<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('kimia_forms', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('tanggal');
            }
            if (!Schema::hasColumn('kimia_forms', 'catatan')) {
                $table->text('catatan')->nullable()->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            if (Schema::hasColumn('kimia_forms', 'catatan')) {
                $table->dropColumn('catatan');
            }
            if (Schema::hasColumn('kimia_forms', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};

