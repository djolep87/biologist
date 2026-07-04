<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('godisnji_izvestaji');

        Schema::create('godisnji_izvestaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->integer('godina');
            $table->string('odgovorno_lice_ime')->nullable();
            $table->string('odgovorno_lice_funkcija')->nullable();
            $table->string('odgovorno_lice_telefon')->nullable();
            $table->string('lice_otpad_ime')->nullable();
            $table->string('lice_otpad_funkcija')->nullable();
            $table->string('lice_otpad_telefon')->nullable();
            $table->string('lice_otpad_email')->nullable();
            $table->enum('status', ['generisan', 'finalizovan'])->default('generisan');
            $table->timestamp('generisan_at')->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'godina']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('godisnji_izvestaji');
    }
};
