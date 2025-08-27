<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel daftar tabel per form
        Schema::create('kimia_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('kimia_forms')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Tambahkan kolom table_id ke kimia_columns & kimia_entries
        if (!Schema::hasColumn('kimia_columns', 'table_id')) {
            Schema::table('kimia_columns', function (Blueprint $table) {
                $table->foreignId('table_id')->nullable()->after('form_id')->constrained('kimia_tables')->nullOnDelete();
            });
        }
        if (!Schema::hasColumn('kimia_entries', 'table_id')) {
            Schema::table('kimia_entries', function (Blueprint $table) {
                $table->foreignId('table_id')->nullable()->after('form_id')->constrained('kimia_tables')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kimia_entries', 'table_id')) {
            Schema::table('kimia_entries', function (Blueprint $table) {
                $table->dropConstrainedForeignId('table_id');
            });
        }
        if (Schema::hasColumn('kimia_columns', 'table_id')) {
            Schema::table('kimia_columns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('table_id');
            });
        }
        Schema::dropIfExists('kimia_tables');
    }
};
