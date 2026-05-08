<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_prints', function (Blueprint $table): void {
            $table->id();
            $table->text('prompt');
            $table->string('reference_image_path')->nullable();
            $table->string('generated_image_path')->nullable();
            $table->string('model');
            $table->string('status')->default('completed')->index();
            $table->text('error_message')->nullable();
            $table->string('guest_fingerprint')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_prints');
    }
};
