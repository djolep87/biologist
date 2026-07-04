<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencija_otpada', function (Blueprint $table) {
            $table->id();
            $table->integer('godina');
            $table->unsignedTinyInteger('mesec');
            $table->string('indeksni_broj');
            $table->string('naziv_otpada');
            $table->text('opis_otpada')->nullable();
            $table->string('evidenciju_vodi');
            $table->date('datum');
            $table->decimal('proizvedena_kolicina', 10, 3)->nullable();
            $table->decimal('predata_kolicina', 10, 3)->nullable();
            $table->decimal('stanje_privremeno_skladiste', 10, 3)->nullable();
            $table->boolean('sakupljacu')->default(false);
            $table->boolean('operateru_ponovnog_iskoriscenja')->default(false);
            $table->string('r_oznaka')->nullable();
            $table->boolean('operateru_odlaganja')->default(false);
            $table->string('d_oznaka')->nullable();
            $table->boolean('izvoz')->default(false);
            $table->string('naziv_preduzeca')->nullable();
            $table->string('broj_dozvole')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencija_otpada');
    }
};
