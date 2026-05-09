<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function show(string $locale, Product $product): View
    {
        return view('products.show', [
            'product' => $product->load(['variants.printAreas']),
        ]);
    }
}
