<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumenti_kretanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('broj_dokumenta');

            $table->string('indeksni_broj', 20);
            $table->string('vrsta_otpada');
            $table->string('q_lista', 10)->nullable();
            $table->decimal('masa_ukupno', 10, 3);
            $table->string('nacin_pakovanja')->nullable();
            $table->string('fizicko_stanje')->nullable();
            $table->string('izvestaj_broj')->nullable();
            $table->date('izvestaj_datum')->nullable();
            $table->string('odrediste')->nullable();
            $table->string('vid_prevoza')->nullable();
            $table->text('posebne_napomene')->nullable();

            $table->string('proizvodjac_pib', 9)->nullable();
            $table->string('proizvodjac_maticni', 8)->nullable();
            $table->string('proizvodjac_naziv');
            $table->string('proizvodjac_opstina')->nullable();
            $table->string('proizvodjac_mesto')->nullable();
            $table->string('proizvodjac_postanski', 5)->nullable();
            $table->string('proizvodjac_ulica')->nullable();
            $table->string('proizvodjac_telefon')->nullable();
            $table->string('proizvodjac_faks')->nullable();
            $table->string('proizvodjac_email')->nullable();
            $table->enum('vlasnik_tip', ['proizvodjac', 'vlasnik', 'operater'])->default('proizvodjac');
            $table->string('r_oznaka', 5)->nullable();
            $table->string('d_oznaka', 5)->nullable();
            $table->string('dozvola_broj')->nullable();
            $table->date('dozvola_datum')->nullable();
            $table->date('datum_predaje');
            $table->string('odgovorno_lice_b')->nullable();
            $table->string('telefon_lica_b')->nullable();

            $table->string('prevoznik_pib', 9)->nullable();
            $table->string('prevoznik_maticni', 8)->nullable();
            $table->string('prevoznik_naziv')->nullable();
            $table->string('prevoznik_opstina')->nullable();
            $table->string('prevoznik_mesto')->nullable();
            $table->string('prevoznik_postanski', 5)->nullable();
            $table->string('prevoznik_ulica')->nullable();
            $table->string('prevoznik_telefon')->nullable();
            $table->string('prevoznik_faks')->nullable();
            $table->string('prevoznik_email')->nullable();
            $table->string('vrsta_prevoznog_sredstva')->nullable();
            $table->string('registarski_broj')->nullable();
            $table->string('lokacija_utovara')->nullable();
            $table->string('ruta_via_1')->nullable();
            $table->string('ruta_via_2')->nullable();
            $table->string('ruta_via_3')->nullable();
            $table->string('lokacija_istovara')->nullable();
            $table->string('prevoznik_dozvola_broj')->nullable();
            $table->date('prevoznik_dozvola_datum')->nullable();
            $table->date('prevoznik_datum_prijema')->nullable();
            $table->string('prevoznik_odgovorno_lice_prijem')->nullable();
            $table->string('prevoznik_telefon_lica_prijem')->nullable();
            $table->date('prevoznik_datum_predaje')->nullable();
            $table->string('prevoznik_odgovorno_lice_predaja')->nullable();
            $table->string('prevoznik_telefon_lica_predaja')->nullable();

            $table->string('primalac_pib', 9)->nullable();
            $table->string('primalac_maticni', 8)->nullable();
            $table->string('primalac_naziv')->nullable();
            $table->string('primalac_opstina')->nullable();
            $table->string('primalac_mesto')->nullable();
            $table->string('primalac_postanski', 5)->nullable();
            $table->string('primalac_ulica')->nullable();
            $table->string('primalac_telefon')->nullable();
            $table->string('primalac_faks')->nullable();
            $table->string('primalac_email')->nullable();
            $table->enum('primalac_tip', ['skladiste', 'tretman', 'odlaganje'])->nullable();
            $table->string('primalac_dozvola_broj')->nullable();
            $table->date('primalac_dozvola_datum')->nullable();
            $table->date('primalac_datum_prijema')->nullable();
            $table->string('primalac_odgovorno_lice')->nullable();
            $table->string('primalac_telefon_lica')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['team_id', 'broj_dokumenta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumenti_kretanja');
    }
};
