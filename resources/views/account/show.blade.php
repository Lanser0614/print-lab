@php
    $locale = app()->getLocale();
@endphp
<x-layouts.public :title="__('auth.order_detail') . ' — PrintLab'">
    <main class="mx-auto max-w-3xl px-4 py-8">
        <a href="{{ route('account.index') }}" class="text-sm text-zinc-500 underline transition-colors hover:text-zinc-800">
            &larr; {{ __('auth.back_to_orders') }}
        </a>

        <div class="mt-4 rounded-lg border border-zinc-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">{{ __('auth.order') }} #{{ $orderRequest->id }}</h1>
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-sm font-medium">{{ $orderRequest->status->label() }}</span>
            </div>

            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-zinc-500">{{ __('auth.customer_name') }}</dt>
                    <dd>{{ $orderRequest->customer_name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-zinc-500">{{ __('auth.customer_phone') }}</dt>
                    <dd>{{ $orderRequest->customer_phone }}</dd>
                </div>
                @if ($orderRequest->customer_comment)
                    <div class="flex justify-between">
                        <dt class="text-zinc-500">{{ __('auth.customer_comment') }}</dt>
                        <dd class="text-right">{{ $orderRequest->customer_comment }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-zinc-500">{{ __('auth.created_at') }}</dt>
                    <dd>{{ $orderRequest->created_at->format('d.m.Y H:i') }}</dd>
                </div>
            </dl>

            @if ($orderRequest->items->isNotEmpty())
                <h2 class="mt-6 text-sm font-semibold">{{ __('auth.order_items') }}</h2>
                <ul class="mt-2 space-y-2">
                    @foreach ($orderRequest->items as $item)
                        <li class="rounded-md bg-zinc-50 p-3 text-sm">
                            <span class="font-medium">{{ $item->product_name_snapshot }}</span>
                            @if ($item->color_snapshot)
                                <span class="text-zinc-500">— {{ $item->color_snapshot }}</span>
                            @endif
                            <span class="text-zinc-500">× {{ $item->quantity }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </main>
</x-layouts.public>
