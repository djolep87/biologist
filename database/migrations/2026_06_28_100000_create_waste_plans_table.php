<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_plans', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->json('form_data');
            $table->longText('plan_content');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index('generated_at');
            $table->index('company_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_plans');
    }
};
