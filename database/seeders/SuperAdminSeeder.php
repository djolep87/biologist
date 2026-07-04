<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Zamenite email pre pokretanja: php artisan db:seed --class=SuperAdminSeeder
        $email = env('SUPER_ADMIN_EMAIL', 'TVOJ_EMAIL@OVDE.COM');

        User::where('email', $email)->update(['is_super_admin' => true]);
    }
}
