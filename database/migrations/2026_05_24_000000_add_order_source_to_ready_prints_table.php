<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ready_prints', function (Blueprint $table): void {
            $table->foreignId('order_request_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('source_design_id')
                ->nullable()
                ->after('order_request_id')
                ->constrained('designs')
                ->nullOnDelete()
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('ready_prints', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('source_design_id');
            $table->dropConstrainedForeignId('order_request_id');
        });
    }
};
