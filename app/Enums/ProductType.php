<?php

namespace App\Enums;

enum ProductType: string
{
    case TShirt = 't-shirt';
    case Mug = 'mug';
    case Hoodie = 'hoodie';
    case Other = 'other';

    public function label(): string
    {
        $key = 'site.product_type_'.str_replace('-', '_', $this->value);
        $translated = __($key);

        // Fallback to Russian if translation key missing
        return $translated !== $key ? $translated : $this->labelRu();
    }

    public function labelRu(): string
    {
        return match ($this) {
            self::TShirt => 'Футболка',
            self::Mug => 'Кружка',
            self::Hoodie => 'Худи',
            self::Other => 'Другое',
        };
    }
}
