<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dnevne_evidencije', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->integer('godina');
            $table->integer('mesec');
            $table->string('indeksni_broj', 20);
            $table->string('naziv_otpada');
            $table->text('opis_otpada')->nullable();
            $table->enum('karakter_otpada', ['inertan', 'neopasan', 'opasan']);
            $table->enum('fizicko_stanje', [
                'cvrsta-prah',
                'cvrsta-komadi',
                'viskozna-pasta',
                'tecna',
                'talog',
            ]);
            $table->string('lice_koje_vodi');

            $table->date('datum');
            $table->decimal('proizvedena_kolicina', 10, 2)->default(0);
            $table->decimal('predata_kolicina', 10, 2)->default(0);
            $table->decimal('stanje_na_skladistu', 10, 2)->default(0);
            $table->boolean('predat_sakupljacu')->default(false);
            $table->boolean('predat_operateru_r')->default(false);
            $table->boolean('predat_operateru_d')->default(false);
            $table->boolean('izvoz')->default(false);
            $table->string('naziv_primaoca')->nullable();
            $table->string('broj_dozvole_primaoca')->nullable();
            $table->enum('nacin_odredjivanja', ['1', '2', '3'])->default('1');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dnevne_evidencije');
    }
};
