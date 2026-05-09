<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ReadyPrint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_v2_shows_product_and_print_categories(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $productCategory = Category::factory()->create([
            'name' => 'Футболки',
            'slug' => 't-shirts',
            'scope' => Category::SCOPE_PRODUCT,
            'sort_order' => 10,
        ]);

        Category::factory()->create([
            'name' => 'Скрытая категория',
            'slug' => 'hidden-category',
            'scope' => Category::SCOPE_PRODUCT,
            'is_active' => false,
        ]);

        $product = Product::factory()->create();
        $product->categories()->attach($productCategory);

        $printCategory = Category::factory()->create([
            'name' => 'Мемы',
            'slug' => 'memes',
            'scope' => Category::SCOPE_PRINT,
            'sort_order' => 10,
        ]);

        ReadyPrint::factory()->create([
            'category_id' => $printCategory->id,
        ]);

        $response = $this->get('/ru');

        $response->assertOk();
        $response->assertSee('Футболки', false);
        $response->assertSee('1 товаров', false);
        $response->assertSee('Мемы', false);
        $response->assertDontSee('NYET', false);
        $response->assertDontSee('Космонавт', false);
        $response->assertDontSee('Скрытая категория', false);
    }

    public function test_catalog_filters_products_by_category_and_shows_empty_state(): void
    {
        $filledCategory = Category::factory()->create([
            'name' => 'Футболки',
            'slug' => 't-shirts',
            'scope' => Category::SCOPE_PRODUCT,
        ]);

        $emptyCategory = Category::factory()->create([
            'name' => 'Худи',
            'slug' => 'hoodies',
            'scope' => Category::SCOPE_PRODUCT,
        ]);

        $product = Product::factory()->create([
            'name' => 'Классическая футболка',
            'is_active' => true,
        ]);
        $product->categories()->attach($filledCategory);

        $response = $this->get('/ru/catalog?category=' . $emptyCategory->slug);

        $response->assertOk();
        $response->assertSee('Товары пока не добавлены.', false);
        $response->assertDontSee('Классическая футболка', false);
    }

    public function test_inactive_category_is_not_visible_or_filterable_on_site(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $inactiveCategory = Category::factory()->create([
            'name' => 'Скрытые товары',
            'slug' => 'hidden-products',
            'scope' => Category::SCOPE_PRODUCT,
            'is_active' => false,
        ]);

        $product = Product::factory()->create([
            'name' => 'Товар из скрытой категории',
            'is_active' => true,
        ]);
        $product->categories()->attach($inactiveCategory);

        $this->get('/ru')
            ->assertOk()
            ->assertDontSee('Скрытые товары', false);

        $this->get('/ru/catalog?category=' . $inactiveCategory->slug)
            ->assertOk()
            ->assertSee('Товары пока не добавлены.', false)
            ->assertDontSee('Товар из скрытой категории', false);
    }
}
