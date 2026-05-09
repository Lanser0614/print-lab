<x-layouts.public :title="__('site.success_title') . ' — PrintLab'">
    <main class="mx-auto max-w-2xl px-4 py-16">
        <div class="rounded-lg border border-zinc-200 bg-white p-8">
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('site.success_title') }}</h1>
            <p class="mt-3 text-zinc-600">{{ __('site.success_text') }}</p>
            <a href="{{ route('home') }}"
               class="mt-6 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 transition-colors">
                {{ __('site.success_back') }}
            </a>
        </div>
    </main>
</x-layouts.public>
