<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run: php artisan migrate
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('pib', 9)->nullable()->after('name');
            $table->string('maticni_broj', 8)->nullable()->after('pib');
            $table->string('adresa')->nullable()->after('maticni_broj');
            $table->string('grad')->nullable()->after('adresa');
            $table->string('postanski_broj', 5)->nullable()->after('grad');
            $table->enum('tip_subjekta', ['DEO1', 'DEO2', 'DEO3', 'DEO4', 'DEO5', 'DEO6'])->default('DEO1')->after('postanski_broj');
            $table->string('delatnost', 4)->nullable()->after('tip_subjekta');
            $table->string('kontakt_telefon')->nullable()->after('delatnost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'pib',
                'maticni_broj',
                'adresa',
                'grad',
                'postanski_broj',
                'tip_subjekta',
                'delatnost',
                'kontakt_telefon',
            ]);
        });
    }
};
