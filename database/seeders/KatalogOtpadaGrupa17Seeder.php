<?php

namespace Database\Seeders;

use App\Models\KatalogOtpadaGrupa17;
use Illuminate\Database\Seeder;

class KatalogOtpadaGrupa17Seeder extends Seeder
{
    public function run(): void
    {
        $stavke = [
            ['sifra' => '17 01 01', 'naziv' => 'Beton', 'kategorija' => 'inertni'],
            ['sifra' => '17 01 02', 'naziv' => 'Cigle', 'kategorija' => 'inertni'],
            ['sifra' => '17 01 03', 'naziv' => 'Crepovi i keramički materijali', 'kategorija' => 'inertni'],
            ['sifra' => '17 01 06', 'naziv' => 'Mešavine ili frakcije betona, cigle, crepa – opasne', 'kategorija' => 'opasni'],
            ['sifra' => '17 01 07', 'naziv' => 'Mešavina betona, cigle, crepa i keramike', 'kategorija' => 'inertni'],
            ['sifra' => '17 02 01', 'naziv' => 'Drvo', 'kategorija' => 'neopasni'],
            ['sifra' => '17 02 02', 'naziv' => 'Staklo', 'kategorija' => 'neopasni'],
            ['sifra' => '17 02 03', 'naziv' => 'Plastika', 'kategorija' => 'neopasni'],
            ['sifra' => '17 03 02', 'naziv' => 'Asfaltni mešavine bez katrana', 'kategorija' => 'neopasni'],
            ['sifra' => '17 04 01', 'naziv' => 'Bakar, bronza, mesing', 'kategorija' => 'neopasni'],
            ['sifra' => '17 04 02', 'naziv' => 'Aluminijum', 'kategorija' => 'neopasni'],
            ['sifra' => '17 04 05', 'naziv' => 'Gvožđe i čelik', 'kategorija' => 'neopasni'],
            ['sifra' => '17 04 07', 'naziv' => 'Mešani metali', 'kategorija' => 'neopasni'],
            ['sifra' => '17 05 04', 'naziv' => 'Zemlja i kamen od iskopa', 'kategorija' => 'inertni'],
            ['sifra' => '17 05 06', 'naziv' => 'Mulj od iskopavanja', 'kategorija' => 'neopasni'],
            ['sifra' => '17 06 04', 'naziv' => 'Izolacioni materijali bez azbesta', 'kategorija' => 'neopasni'],
            ['sifra' => '17 08 02', 'naziv' => 'Građevinski materijali na bazi gipsa', 'kategorija' => 'neopasni'],
            ['sifra' => '17 09 04', 'naziv' => 'Mešani građevinski otpad', 'kategorija' => 'neopasni'],
        ];

        foreach ($stavke as $stavka) {
            KatalogOtpadaGrupa17::updateOrCreate(
                ['sifra' => $stavka['sifra']],
                [
                    'naziv' => $stavka['naziv'],
                    'kategorija' => $stavka['kategorija'],
                    'is_active' => true,
                ]
            );
        }
    }
}
