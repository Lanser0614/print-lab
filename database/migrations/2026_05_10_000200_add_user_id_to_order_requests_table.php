<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
