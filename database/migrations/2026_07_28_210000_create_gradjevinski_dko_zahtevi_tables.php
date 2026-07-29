<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gradjevinski_dko_zahtevi')) {
            Schema::create('gradjevinski_dko_zahtevi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
                $table->foreignId('construction_site_id')->constrained('construction_sites')->onDelete('cascade');
                $table->foreignId('kreirao_korisnik_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('obradio_admin_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('dokument_kretanja_id')->nullable()->constrained('dokumenti_kretanja')->onDelete('set null');

                $table->string('broj_zahteva')->nullable();
                $table->unsignedInteger('redni_broj')->nullable();
                $table->string('status')->default('na_cekanju');
                $table->decimal('masa_ukupno', 12, 3)->default(0);
                $table->text('napomena_klijenta')->nullable();
                $table->text('napomena_admina')->nullable();
                $table->string('razlog_odbijanja')->nullable();
                $table->timestamp('poslato_at')->nullable();
                $table->timestamp('preuzeto_admin_at')->nullable();
                $table->timestamp('zavrseno_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['team_id', 'broj_zahteva']);
                $table->index(['status', 'poslato_at']);
            });
        }

        if (! Schema::hasTable('gradjevinski_dko_zahtev_evidencija')) {
            Schema::create('gradjevinski_dko_zahtev_evidencija', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gradjevinski_dko_zahtev_id');
                $table->unsignedBigInteger('dnevna_evidencija_id');
                $table->timestamps();

                $table->foreign('gradjevinski_dko_zahtev_id', 'gdko_zahtev_fk')
                    ->references('id')->on('gradjevinski_dko_zahtevi')->onDelete('cascade');
                $table->foreign('dnevna_evidencija_id', 'gdko_evidencija_fk')
                    ->references('id')->on('dnevne_evidencije')->onDelete('cascade');
                $table->unique('dnevna_evidencija_id', 'gdko_evidencija_unique');
            });
        }

        if (! Schema::hasColumn('dnevne_evidencije', 'dko_status')) {
            Schema::table('dnevne_evidencije', function (Blueprint $table) {
                $table->string('dko_status')->default('slobodan')->after('construction_site_id');
                $table->unsignedBigInteger('gradjevinski_dko_zahtev_id')->nullable()->after('dko_status');
                $table->foreign('gradjevinski_dko_zahtev_id', 'de_gdko_zahtev_fk')
                    ->references('id')->on('gradjevinski_dko_zahtevi')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dnevne_evidencije', 'dko_status')) {
            Schema::table('dnevne_evidencije', function (Blueprint $table) {
                $table->dropForeign('de_gdko_zahtev_fk');
                $table->dropColumn(['gradjevinski_dko_zahtev_id', 'dko_status']);
            });
        }

        Schema::dropIfExists('gradjevinski_dko_zahtev_evidencija');
        Schema::dropIfExists('gradjevinski_dko_zahtevi');
    }
};
