<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Prospekt, Müzakere, Müşteri, Kayıp
            $table->string('slug')->unique();
            $table->string('color')->default('#6B7280'); // Tailwind gray-500
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_statuses');
    }
};
