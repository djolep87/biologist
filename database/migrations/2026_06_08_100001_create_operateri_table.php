<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operateri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('naziv');
            $table->string('pib', 9)->unique();
            $table->string('maticni_broj', 8)->unique();
            $table->string('kratki_naziv')->nullable();

            $table->string('opstina')->nullable();
            $table->string('mesto')->nullable();
            $table->string('postanski_broj', 5)->nullable();
            $table->string('ulica')->nullable();

            $table->string('telefon')->nullable();
            $table->string('faks')->nullable();
            $table->string('email')->nullable();
            $table->string('kontakt_osoba')->nullable();

            $table->string('dozvola_broj')->nullable();
            $table->date('dozvola_datum_izdavanja')->nullable();
            $table->date('dozvola_vazi_do')->nullable();

            $table->json('prihvata_indeksne_brojeve')->nullable();
            $table->json('tip')->nullable();

            $table->string('r_oznaka', 5)->nullable();
            $table->string('d_oznaka', 5)->nullable();

            $table->boolean('aktivan')->default(true);
            $table->text('napomena')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operateri');
    }
};
