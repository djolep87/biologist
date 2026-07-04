<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zahtevi_predaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['na_cekanju', 'u_obradi', 'zavrseno', 'odbijeno'])->default('na_cekanju');
            $table->foreignId('dokument_kretanja_id')
                ->nullable()
                ->constrained('dokumenti_kretanja')
                ->nullOnDelete();
            $table->string('indeksni_broj', 20);
            $table->string('naziv_otpada');
            $table->decimal('masa_ukupno', 10, 3);
            $table->text('napomena_klijenta')->nullable();
            $table->text('napomena_admina')->nullable();
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('admin_odgovorio_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zahtevi_predaje');
    }
};
