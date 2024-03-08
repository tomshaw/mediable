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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->nullable();
            $table->string('file_original_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->default('0')->nullable();
            $table->string('file_dir')->nullable();
            $table->string('file_url')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default('0')->nullable();
            $table->string('styles')->nullable();
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
