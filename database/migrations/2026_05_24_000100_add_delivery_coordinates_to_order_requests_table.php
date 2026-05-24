<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->decimal('delivery_lat', 10, 7)->nullable()->after('customer_address');
            $table->decimal('delivery_lng', 10, 7)->nullable()->after('delivery_lat');
        });
    }

    public function down(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->dropColumn(['delivery_lat', 'delivery_lng']);
        });
    }
};
