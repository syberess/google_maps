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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('google_place_id')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('review_count')->default(0);
            $table->string('category')->nullable();
            $table->text('types')->nullable(); // JSON array
            $table->foreignId('status_id')->default(1)->constrained('company_statuses')->onDelete('cascade');
            $table->string('source')->default('google_maps'); // google_maps, manual
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status_id', 'created_at']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
