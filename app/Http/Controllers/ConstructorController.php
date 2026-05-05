<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ConstructorController extends Controller
{
    public function show(Request $request, Product $product): View
    {
        $variant = $this->resolveVariant($request, $product);
        $side = $request->string('side', 'front')->toString();

        if (! in_array($side, ['front', 'back'], true)) {
            $side = 'front';
        }

        $printArea = ProductPrintArea::query()
            ->where('product_variant_id', $variant->id)
            ->where('side', $side)
            ->first()
            ?? ProductPrintArea::query()->where('product_variant_id', $variant->id)->firstOrFail();

        return view('constructor.show', [
            'product' => $product->load('variants'),
            'variant' => $variant,
            'printArea' => $printArea,
            'side' => $side,
        ]);
    }

    private function resolveVariant(Request $request, Product $product): ProductVariant
    {
        $variantId = $request->integer('variant');

        if ($variantId > 0) {
            return ProductVariant::query()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->findOrFail($variantId);
        }

        return $product->variants()
            ->where('is_active', true)
            ->orderBy('id')
            ->firstOrFail();
    }
}
