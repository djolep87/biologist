<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('opstina')->nullable()->after('grad');
            $table->string('email')->nullable()->after('kontakt_telefon');
            $table->string('faks')->nullable()->after('email');
            $table->string('kontakt_osoba')->nullable()->after('faks');
            $table->string('dozvola_broj')->nullable()->after('kontakt_osoba');
            $table->date('dozvola_datum_izdavanja')->nullable()->after('dozvola_broj');
            $table->date('dozvola_vazi_do')->nullable()->after('dozvola_datum_izdavanja');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'opstina',
                'email',
                'faks',
                'kontakt_osoba',
                'dozvola_broj',
                'dozvola_datum_izdavanja',
                'dozvola_vazi_do',
            ]);
        });
    }
};
