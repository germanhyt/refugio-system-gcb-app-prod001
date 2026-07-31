<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ActivityTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Actividad últimos 6 meses';

    protected static ?string $description = 'Newsletter, blog y eventos por mes';

    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->startOfMonth()->subMonths($i));

        $labels = $months->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->all();

        $newsletter = $months->map(function (Carbon $month) {
            return NewsletterSubscriber::query()
                ->whereBetween('subscribed_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        $blog = $months->map(function (Carbon $month) {
            return BlogPost::query()
                ->whereNotNull('published_at')
                ->whereBetween('published_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        $events = $months->map(function (Carbon $month) {
            return Event::query()
                ->whereBetween('event_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
                ->count();
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Newsletter',
                    'data' => $newsletter,
                    'backgroundColor' => '#A7623D',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Blog',
                    'data' => $blog,
                    'backgroundColor' => '#8B5E3C',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Eventos',
                    'data' => $events,
                    'backgroundColor' => '#C4895F',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
