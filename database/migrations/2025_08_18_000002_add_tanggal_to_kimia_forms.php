<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('kimia_forms', 'tanggal')) {
                $table->date('tanggal')->after('no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kimia_forms', function (Blueprint $table) {
            if (Schema::hasColumn('kimia_forms', 'tanggal')) {
                $table->dropColumn('tanggal');
            }
        });
    }
};
