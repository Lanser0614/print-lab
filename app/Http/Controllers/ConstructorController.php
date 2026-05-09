<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Contracts\View\View;
use Database\Seeders\DatabaseSeeder;

class ConstructorController extends Controller
{
    public function show(Request $request, string $locale, Product $product): View
    {
        return $this->renderConstructor(
            $request,
            $product,
            config('printlab.constructor_v2_enabled') ? 'constructor.v2' : 'constructor.show',
        );
    }

    public function v2(Request $request, string $locale, Product $product): View
    {
        return $this->renderConstructor($request, $product, 'constructor.v2');
    }

    public function aiStudio(Request $request, string $locale, Product $product): View
    {
        return $this->renderConstructor($request, $product, 'constructor.ai-studio');
    }

    private function renderConstructor(Request $request, Product $product, string $view): View
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

        return view($view, [
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

    public function localFallback(Request $request): mixed
    {
        abort_unless(app()->environment('local'), 404);

        if (! Product::query()->where('slug', 'classic-t-shirt')->exists()) {
            app(DatabaseSeeder::class)->run();
        }

        return redirect()->route('constructor.show', ['product' => 'classic-t-shirt']);
    }
}
