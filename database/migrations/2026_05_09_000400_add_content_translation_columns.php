<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('name_translations')->nullable()->after('name');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->json('name_translations')->nullable()->after('name');
        });

        Schema::table('ready_prints', function (Blueprint $table): void {
            $table->json('title_translations')->nullable()->after('title');
        });

        DB::table('products')
            ->whereNull('name_translations')
            ->orderBy('id')
            ->eachById(function (object $product): void {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['name_translations' => json_encode(['ru' => $product->name], JSON_UNESCAPED_UNICODE)]);
            });

        DB::table('categories')
            ->whereNull('name_translations')
            ->orderBy('id')
            ->eachById(function (object $category): void {
                DB::table('categories')
                    ->where('id', $category->id)
                    ->update(['name_translations' => json_encode(['ru' => $category->name], JSON_UNESCAPED_UNICODE)]);
            });

        DB::table('ready_prints')
            ->whereNull('title_translations')
            ->orderBy('id')
            ->eachById(function (object $print): void {
                DB::table('ready_prints')
                    ->where('id', $print->id)
                    ->update(['title_translations' => json_encode(['ru' => $print->title], JSON_UNESCAPED_UNICODE)]);
            });
    }

    public function down(): void
    {
        Schema::table('ready_prints', function (Blueprint $table): void {
            $table->dropColumn('title_translations');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('name_translations');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('name_translations');
        });
    }
};
