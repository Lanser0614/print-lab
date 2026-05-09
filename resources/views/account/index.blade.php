@php
    $locale = app()->getLocale();
@endphp
<x-layouts.public :title="__('auth.my_account') . ' — PrintLab'">
    <main class="mx-auto max-w-3xl px-4 py-8">
        <div class="flex items-center gap-4">
            @if ($user->photo_url)
                <img src="{{ $user->photo_url }}" alt="" class="h-12 w-12 rounded-full">
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-200 text-lg font-semibold text-zinc-600">
                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-xl font-semibold">{{ $user->first_name }} {{ $user->last_name }}</h1>
                <p class="text-sm text-zinc-500">@if ($user->telegram_username) @{{ $user->telegram_username }} @endif</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-lg font-semibold">{{ __('auth.my_orders') }}</h2>

            @if ($orderRequests->isEmpty())
                <div class="mt-4 rounded-lg border border-dashed border-zinc-300 p-8 text-center">
                    <p class="text-zinc-500">{{ __('auth.no_orders') }}</p>
                    <a href="{{ route('catalog.index') }}" class="mt-3 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-zinc-800">
                        {{ __('auth.to_catalog') }}
                    </a>
                </div>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($orderRequests as $orderRequest)
                        <a href="{{ route('account.order-requests.show', $orderRequest) }}" class="block rounded-lg border border-zinc-200 p-4 transition-colors hover:border-zinc-400">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">#{{ $orderRequest->id }}</span>
                                <span class="text-xs text-zinc-400">{{ $orderRequest->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="text-sm text-zinc-600">{{ $orderRequest->customer_name }}</span>
                                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium">{{ $orderRequest->status->label() }}</span>
                            </div>
                            @if ($orderRequest->items->isNotEmpty())
                                <p class="mt-1 text-xs text-zinc-400">
                                    {{ $orderRequest->items->pluck('product_name_snapshot')->implode(', ') }}
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-8 border-t pt-6">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-zinc-500 underline transition-colors hover:text-zinc-800">
                    {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    </main>
</x-layouts.public>
