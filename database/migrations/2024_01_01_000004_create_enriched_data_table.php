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
        Schema::create('enriched_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_title')->nullable();
            $table->text('additional_info')->nullable();
            $table->string('source')->nullable(); // manual, scraper, api
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enriched_data');
    }
};
