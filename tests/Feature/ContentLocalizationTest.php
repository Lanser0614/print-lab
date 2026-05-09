<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use App\Models\ReadyPrint;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_name_uses_current_locale_then_russian_then_legacy_name(): void
    {
        $product = Product::factory()->create([
            'name' => 'Legacy shirt',
            'name_translations' => [
                'ru' => 'Футболка',
                'uz' => 'Futbolka',
            ],
        ]);

        app()->setLocale('uz');
        $this->assertSame('Futbolka', $product->localizedName());

        $product->forceFill(['name_translations' => ['ru' => 'Футболка']]);
        $this->assertSame('Футболка', $product->localizedName());

        $product->forceFill(['name_translations' => []]);
        $this->assertSame('Legacy shirt', $product->localizedName());
    }

    public function test_category_name_uses_current_locale_then_russian_then_legacy_name(): void
    {
        $category = Category::factory()->create([
            'name' => 'Legacy category',
            'name_translations' => [
                'ru' => 'Категория',
                'uz' => 'Kategoriya',
            ],
        ]);

        app()->setLocale('uz');
        $this->assertSame('Kategoriya', $category->localizedName());

        $category->forceFill(['name_translations' => ['ru' => 'Категория']]);
        $this->assertSame('Категория', $category->localizedName());

        $category->forceFill(['name_translations' => []]);
        $this->assertSame('Legacy category', $category->localizedName());
    }

    public function test_ready_print_title_uses_current_locale_then_russian_then_legacy_title(): void
    {
        $print = ReadyPrint::factory()->create([
            'title' => 'Legacy print',
            'title_translations' => [
                'ru' => 'Принт',
                'uz' => 'Print',
            ],
        ]);

        app()->setLocale('uz');
        $this->assertSame('Print', $print->localizedTitle());

        $print->forceFill(['title_translations' => ['ru' => 'Принт']]);
        $this->assertSame('Принт', $print->localizedTitle());

        $print->forceFill(['title_translations' => []]);
        $this->assertSame('Legacy print', $print->localizedTitle());
    }

    public function test_uzbek_homepage_renders_localized_catalog_content(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $productCategory = Category::factory()->create([
            'name' => 'Футболки',
            'name_translations' => [
                'ru' => 'Футболки',
                'uz' => 'Futbolkalar',
            ],
            'slug' => 't-shirts',
            'scope' => Category::SCOPE_PRODUCT,
            'sort_order' => 10,
        ]);

        $printCategory = Category::factory()->create([
            'name' => 'Мемы',
            'name_translations' => [
                'ru' => 'Мемы',
                'uz' => 'Memlar',
            ],
            'slug' => 'memes',
            'scope' => Category::SCOPE_PRINT,
            'sort_order' => 10,
        ]);

        $product = Product::factory()->create([
            'name' => 'Классическая футболка',
            'name_translations' => [
                'ru' => 'Классическая футболка',
                'uz' => 'Klassik futbolka',
            ],
            'is_active' => true,
        ]);
        $product->categories()->attach($productCategory);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => '#ffffff',
            'size' => 'XL',
            'is_active' => true,
        ]);
        ProductPrintArea::factory()->create(['product_variant_id' => $variant->id]);

        ReadyPrint::factory()->create([
            'category_id' => $printCategory->id,
            'title' => 'Космонавт',
            'title_translations' => [
                'ru' => 'Космонавт',
                'uz' => 'Kosmonavt',
            ],
            'is_active' => true,
        ]);

        $this->get('/uz')
            ->assertOk()
            ->assertSee('Futbolkalar', false)
            ->assertSee('Klassik futbolka', false)
            ->assertSee('Memlar', false)
            ->assertDontSee('Классическая футболка', false);
    }

    public function test_catalog_header_removes_cart_and_favorites_and_links_to_login(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $category = Category::factory()->create([
            'slug' => 't-shirts',
            'scope' => Category::SCOPE_PRODUCT,
            'is_active' => true,
        ]);

        $product = Product::factory()->create(['is_active' => true]);
        $product->categories()->attach($category);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'is_active' => true,
        ]);
        ProductPrintArea::factory()->create(['product_variant_id' => $variant->id]);

        foreach (['/ru', '/ru/catalog'] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('/ru/login', false)
                ->assertSee('Войти', false)
                ->assertDontSee('Избранное', false)
                ->assertDontSee('Корзина', false)
                ->assertDontSee('pl-card-fav', false);
        }
    }

    public function test_product_page_renders_hex_colour_swatch_and_size_choice(): void
    {
        $product = Product::factory()->create([
            'name' => 'Классическая футболка',
            'name_translations' => [
                'ru' => 'Классическая футболка',
                'uz' => 'Klassik futbolka',
            ],
            'is_active' => true,
        ]);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => '#ff0000',
            'size' => 'XL',
            'is_active' => true,
        ]);
        ProductPrintArea::factory()->create(['product_variant_id' => $variant->id]);

        $this->get('/uz/products/' . $product->slug)
            ->assertOk()
            ->assertSee('Klassik futbolka', false)
            ->assertSee('background-color:#ff0000', false)
            ->assertSee('data-size="XL"', false);
    }

    public function test_database_seeder_creates_uzbek_content_translations(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            'Klassik futbolka',
            Product::query()->where('slug', 'classic-t-shirt')->firstOrFail()->localizedName('uz'),
        );

        $this->assertSame(
            'Futbolkalar',
            Category::query()->where('slug', 't-shirts')->firstOrFail()->localizedName('uz'),
        );

        $this->assertSame(
            'Tipografika',
            Category::query()->where('slug', 'typography')->firstOrFail()->localizedName('uz'),
        );
    }

    public function test_uzbek_public_pages_do_not_render_core_russian_static_ui(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);
        config()->set('printlab.constructor_v2_enabled', true);

        $product = Product::factory()->create([
            'name' => 'Классическая футболка',
            'name_translations' => [
                'ru' => 'Классическая футболка',
                'uz' => 'Klassik futbolka',
            ],
            'slug' => 'classic-t-shirt',
            'is_active' => true,
        ]);

        $category = Category::factory()->create([
            'name' => 'Футболки',
            'name_translations' => [
                'ru' => 'Футболки',
                'uz' => 'Futbolkalar',
            ],
            'slug' => 't-shirts',
            'scope' => Category::SCOPE_PRODUCT,
            'is_active' => true,
        ]);
        $product->categories()->attach($category);

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => '#ffffff',
            'size' => 'M',
            'is_active' => true,
        ]);
        ProductPrintArea::factory()->create(['product_variant_id' => $variant->id]);

        foreach (['/uz', '/uz/catalog', '/uz/constructor/classic-t-shirt'] as $url) {
            $response = $this->get($url)->assertOk();

            foreach ([
                'Доставка',
                'Оплата',
                'Гарантия',
                'Профиль',
                'Избранное',
                'Корзина',
                'Создай',
                'Каталог товаров',
                'Готовые принты',
                'Нажми + чтобы добавить элемент',
                'Не удалось загрузить изображение товара',
            ] as $russianText) {
                $response->assertDontSee($russianText, false);
            }
        }
    }
}
