<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zahtev_evidencija', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zahtev_predaje_id')
                ->constrained('zahtevi_predaje')
                ->cascadeOnDelete();
            $table->foreignId('dnevna_evidencija_id')
                ->constrained('dnevne_evidencije')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['zahtev_predaje_id', 'dnevna_evidencija_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zahtev_evidencija');
    }
};
