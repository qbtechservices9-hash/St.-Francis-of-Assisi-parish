<x-filament-widgets::widget>
    <x-filament::section class="dashboard-card">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <x-filament::icon icon="heroicon-o-shield-check" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-amber-600/80">Admin Dashboard</p>
                    <h2 class="text-xl font-semibold text-slate-900">Welcome back, {{ $userName }}</h2>
                    <p class="text-sm text-slate-500">Manage the St. Francis Of Assisi Church website.</p>
                </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="rounded-lg bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                    {{ now()->format('l, F j, Y') }}
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-filament::button
                        tag="a"
                        href="{{ url('/') }}"
                        icon="heroicon-o-arrow-top-right-on-square"
                    >
                        View Site
                    </x-filament::button>

                    <form action="{{ filament()->getLogoutUrl() }}" method="post">
                        @csrf

                        <x-filament::button
                            color="gray"
                            icon="heroicon-o-arrow-left-end-on-rectangle"
                            type="submit"
                            outlined
                        >
                            Sign out
                        </x-filament::button>
                    </form>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>