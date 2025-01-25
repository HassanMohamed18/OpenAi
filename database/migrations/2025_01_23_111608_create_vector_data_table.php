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
        Schema::create('vector_data', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('source_table'); // Reference to the original table row
            //$table->string('column_name'); // Name of the column being processed
            $table->text('text'); // Text content
            $table->json('embedding'); // JSON-encoded embeddings
            $table->timestamps(); // created_at and updated_at

            // Optional: You can add indexes for faster queries if needed
            //$table->index(['source_table', 'column_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vector_data');
    }
};
