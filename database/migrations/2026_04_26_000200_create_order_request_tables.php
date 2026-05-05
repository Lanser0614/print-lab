<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id();
            $table->string('status')->index();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_comment')->nullable();
            $table->string('customer_city')->nullable();
            $table->text('customer_address')->nullable();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('total_amount')->nullable();
            $table->string('currency')->default('UZS');
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamps();
        });

        Schema::create('order_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->string('product_name_snapshot');
            $table->string('product_type_snapshot');
            $table->string('color_snapshot');
            $table->string('size_snapshot')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price')->nullable();
            $table->unsignedBigInteger('total_price')->nullable();
            $table->timestamps();
        });

        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_request_item_id')->constrained()->cascadeOnDelete();
            $table->string('side')->default('front');
            $table->json('canvas_json');
            $table->string('preview_image_path')->nullable();
            $table->string('print_image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('design_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('original_file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('design_text_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->string('layer_id');
            $table->text('text');
            $table->string('font_family')->nullable();
            $table->unsignedInteger('font_size')->nullable();
            $table->string('color')->nullable();
            $table->decimal('x', 10, 2)->nullable();
            $table->decimal('y', 10, 2)->nullable();
            $table->decimal('scale', 8, 4)->nullable();
            $table->decimal('rotation', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_text_layers');
        Schema::dropIfExists('design_assets');
        Schema::dropIfExists('designs');
        Schema::dropIfExists('order_request_items');
        Schema::dropIfExists('order_requests');
    }
};
