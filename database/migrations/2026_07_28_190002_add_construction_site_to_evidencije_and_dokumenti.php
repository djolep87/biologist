<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dnevne_evidencije', function (Blueprint $table) {
            $table->foreignId('construction_site_id')
                ->nullable()
                ->after('team_id')
                ->constrained('construction_sites')
                ->onDelete('cascade');

            $table->string('nacin_nastanka')->nullable()->after('opis_otpada');
            $table->string('napomena')->nullable()->after('nacin_nastanka');
        });

        Schema::table('dokumenti_kretanja', function (Blueprint $table) {
            $table->foreignId('construction_site_id')
                ->nullable()
                ->after('team_id')
                ->constrained('construction_sites')
                ->onDelete('cascade');

            $table->string('broj_gradevinske_dozvole_dko')
                ->nullable()
                ->after('construction_site_id');
        });
    }

    public function down(): void
    {
        Schema::table('dokumenti_kretanja', function (Blueprint $table) {
            $table->dropConstrainedForeignId('construction_site_id');
            $table->dropColumn('broj_gradevinske_dozvole_dko');
        });

        Schema::table('dnevne_evidencije', function (Blueprint $table) {
            $table->dropConstrainedForeignId('construction_site_id');
            $table->dropColumn(['nacin_nastanka', 'napomena']);
        });
    }
};
