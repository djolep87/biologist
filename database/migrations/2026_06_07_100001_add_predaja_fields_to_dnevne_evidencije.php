<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dnevne_evidencije', function (Blueprint $table) {
            $table->boolean('predat_operateru')->default(false)->after('izvoz');
            $table->foreignId('dokument_kretanja_id')
                ->nullable()
                ->after('predat_operateru')
                ->constrained('dokumenti_kretanja')
                ->nullOnDelete();
            $table->date('datum_predaje_operateru')->nullable()->after('dokument_kretanja_id');
            $table->string('operater_naziv')->nullable()->after('datum_predaje_operateru');
            $table->string('operater_dozvola_broj')->nullable()->after('operater_naziv');
            $table->string('r_oznaka', 5)->nullable()->after('predat_operateru_d');
            $table->string('d_oznaka', 5)->nullable()->after('r_oznaka');
        });
    }

    public function down(): void
    {
        Schema::table('dnevne_evidencije', function (Blueprint $table) {
            $table->dropForeign(['dokument_kretanja_id']);
            $table->dropColumn([
                'predat_operateru',
                'dokument_kretanja_id',
                'datum_predaje_operateru',
                'operater_naziv',
                'operater_dozvola_broj',
                'r_oznaka',
                'd_oznaka',
            ]);
        });
    }
};
