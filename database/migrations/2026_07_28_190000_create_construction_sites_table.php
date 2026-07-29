<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('naziv_gradilista');
            $table->string('broj_gradevinske_dozvole');
            $table->string('adresa_gradilista');
            $table->string('mesto');
            $table->string('opstina');
            $table->string('katastarska_parcela')->nullable();
            $table->string('investitor_naziv')->nullable();
            $table->string('izvodjac_naziv')->nullable();
            $table->string('status')->default('aktivno');
            $table->date('datum_pocetka')->nullable();
            $table->date('planirani_zavrsetak')->nullable();
            $table->date('datum_zavrsetka')->nullable();
            $table->decimal('kvadratura_objekta', 10, 2)->nullable();
            $table->string('tip_radova')->nullable();
            $table->decimal('procijenjena_kolicina_otpada', 10, 3)->nullable();
            $table->string('operater_naziv')->nullable();
            $table->string('operater_pib')->nullable();
            $table->string('operater_adresa')->nullable();
            $table->string('operater_dozvola_broj')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_sites');
    }
};
