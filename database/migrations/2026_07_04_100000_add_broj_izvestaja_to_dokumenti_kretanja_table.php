<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumenti_kretanja', function (Blueprint $table) {
            $table->string('broj_izvestaja', 40)->nullable()->after('broj_dokumenta');
            $table->unsignedInteger('redni_broj')->nullable()->after('broj_izvestaja');
            $table->string('format_broja', 20)->default('osnovni')->after('redni_broj');
            $table->string('lokacija_oznaka', 20)->nullable()->after('format_broja');

            // Numeracija je po klijentu (dva klijenta mogu imati isti "001/2026").
            $table->unique(['team_id', 'broj_izvestaja']);
            $table->index(['team_id', 'format_broja', 'redni_broj']);
        });
    }

    public function down(): void
    {
        Schema::table('dokumenti_kretanja', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'format_broja', 'redni_broj']);
            $table->dropUnique(['team_id', 'broj_izvestaja']);
            $table->dropColumn(['broj_izvestaja', 'redni_broj', 'format_broja', 'lokacija_oznaka']);
        });
    }
};
