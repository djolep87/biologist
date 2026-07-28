<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('dko_format_broja', 20)->default('osnovni')->after('dozvola_vazi_do');
            $table->unsignedTinyInteger('dko_broj_cifara')->default(3)->after('dko_format_broja');
            $table->string('dko_prefix', 20)->nullable()->after('dko_broj_cifara');
            $table->json('dko_lokacije')->nullable()->after('dko_prefix');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['dko_format_broja', 'dko_broj_cifara', 'dko_prefix', 'dko_lokacije']);
        });
    }
};
