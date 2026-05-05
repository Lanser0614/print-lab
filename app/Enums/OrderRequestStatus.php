<?php

namespace App\Enums;

enum OrderRequestStatus: string
{
    case New              = 'new';
    case Processing       = 'processing';
    case CallbackRequired = 'callback_required';
    case WaitingPayment   = 'waiting_payment';
    case Paid             = 'paid';
    case InProduction     = 'in_production';
    case Ready            = 'ready';
    case Completed        = 'completed';
    case Cancelled        = 'cancelled';

    public function label(): string
    {
        $key = 'site.status_' . $this->value;
        $translated = __($key);

        // Fallback to Russian if translation key not found
        if ($translated === $key) {
            return $this->labelRu();
        }

        return $translated;
    }

    public function labelRu(): string
    {
        return match ($this) {
            self::New              => 'Новая',
            self::Processing       => 'В обработке',
            self::CallbackRequired => 'Нужен звонок',
            self::WaitingPayment   => 'Ожидает оплаты',
            self::Paid             => 'Оплачена',
            self::InProduction     => 'В производстве',
            self::Ready            => 'Готова',
            self::Completed        => 'Завершена',
            self::Cancelled        => 'Отменена',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
