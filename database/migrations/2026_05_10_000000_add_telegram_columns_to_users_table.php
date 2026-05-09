<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('telegram_id')->nullable()->unique()->after('id');
            $table->string('telegram_username')->nullable()->after('telegram_id');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->index()->after('email');
            $table->string('photo_url')->nullable()->after('phone');
            $table->string('locale', 5)->nullable()->after('photo_url');
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_id',
                'telegram_username',
                'first_name',
                'last_name',
                'phone',
                'photo_url',
                'locale',
            ]);
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
