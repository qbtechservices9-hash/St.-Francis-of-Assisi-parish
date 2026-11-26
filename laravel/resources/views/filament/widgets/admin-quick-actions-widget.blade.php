<x-filament-widgets::widget>
    <x-filament::section class="dashboard-card">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <h3 class="text-lg font-semibold text-slate-900">Quick Actions</h3>
                <p class="text-sm text-slate-500">Jump into key workflows with one click.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.events.create') }}"
                    icon="heroicon-o-calendar-days"
                    color="primary"
                    class="justify-between"
                >
                    Create Event
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.gallery-items.create') }}"
                    icon="heroicon-o-photo"
                    color="info"
                    class="justify-between"
                >
                    Add Gallery
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.ministries.create') }}"
                    icon="heroicon-o-user-group"
                    color="success"
                    class="justify-between"
                >
                    New Ministry
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.contact-messages.index') }}"
                    icon="heroicon-o-inbox"
                    color="warning"
                    class="justify-between"
                >
                    View Messages
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>