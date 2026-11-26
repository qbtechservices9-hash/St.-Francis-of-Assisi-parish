<x-filament-panels::page>
    <x-filament::section>
        <div class="mb-4">
            <h2 class="text-lg font-semibold">Role Permissions Matrix</h2>
            <p class="text-sm text-gray-500">Quick reference for feature access by role.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="text-left text-sm font-semibold">
                        <th class="px-3 py-2">Feature</th>
                        <th class="px-3 py-2">Super Admin</th>
                        <th class="px-3 py-2">Content Admin</th>
                        <th class="px-3 py-2">Ministry Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    @foreach ($this->matrix as $row)
                        <tr>
                            <td class="px-3 py-2 font-medium">{{ $row['feature'] }}</td>
                            <td class="px-3 py-2">
                                @if ($row['super_admin'])
                                    <span class="text-success-600">Allowed</span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if ($row['content_admin'])
                                    <span class="text-success-600">Allowed</span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if ($row['ministry_admin'])
                                    <span class="text-success-600">Allowed</span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
