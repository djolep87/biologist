<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('katalog_otpada_grupa17', function (Blueprint $table) {
            $table->id();
            $table->string('sifra', 20);
            $table->string('naziv');
            $table->string('kategorija');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sifra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('katalog_otpada_grupa17');
    }
};
