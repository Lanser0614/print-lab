<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('scope')->default('print')->after('slug');
            $table->text('icon_svg')->nullable()->after('scope');
            $table->unsignedInteger('sort_order')->default(0)->after('icon_svg');
            $table->boolean('is_active')->default(true)->after('sort_order');

            $table->index(['scope', 'is_active', 'sort_order']);
        });

        Schema::create('category_product', function (Blueprint $table): void {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['scope', 'is_active', 'sort_order']);
            $table->dropColumn(['scope', 'icon_svg', 'sort_order', 'is_active']);
        });
    }
};
