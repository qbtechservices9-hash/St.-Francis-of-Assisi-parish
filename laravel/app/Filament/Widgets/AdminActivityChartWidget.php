<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Event;
use Illuminate\Support\Collection;
use Filament\Widgets\ChartWidget;

class AdminActivityChartWidget extends ChartWidget
{
    protected ?string $heading = 'Weekly Engagement';
    protected ?string $description = 'Messages and events created over the last 7 days.';

    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }

    protected function getData(): array
    {
        $labels = [];
        $messages = [];
        $events = [];

        $start = now()->subDays(6)->startOfDay();
        $messageCounts = $this->groupCountsByDate(
            ContactMessage::query()
                ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
                ->where('created_at', '>=', $start)
                ->groupByRaw('DATE(created_at)')
                ->pluck('aggregate', 'day')
        );
        $eventCounts = $this->groupCountsByDate(
            Event::query()
                ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
                ->where('created_at', '>=', $start)
                ->groupByRaw('DATE(created_at)')
                ->pluck('aggregate', 'day')
        );

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $dayKey = $day->toDateString();

            $labels[] = $day->format('D');
            $messages[] = $messageCounts[$dayKey] ?? 0;
            $events[] = $eventCounts[$dayKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => $messages,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'Events',
                    'data' => $events,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function groupCountsByDate(Collection $counts): array
    {
        return $counts
            ->mapWithKeys(fn ($count, $day) => [(string) $day => (int) $count])
            ->all();
    }
}
