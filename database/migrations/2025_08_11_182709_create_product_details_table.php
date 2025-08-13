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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('attribute_name');
            $table->text('attribute_value');
            $table->string('attribute_type')->default('text'); // text, number, boolean, date, url, etc.
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['product_id', 'attribute_name']);
            $table->unique(['product_id', 'attribute_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
