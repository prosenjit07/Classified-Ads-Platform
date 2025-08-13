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
        Schema::create('category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // text, number, textarea, select, checkbox, radio, etc.
            $table->text('options')->nullable(); // For select, checkbox, radio options (comma-separated)
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->json('validation_rules')->nullable(); // Additional validation rules
            $table->string('default_value')->nullable();
            $table->text('help_text')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['category_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_fields');
    }
};
