<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\EventOffer;
use App\Models\PageInquiry;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ActivityTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Actividad últimos 6 meses';

    protected static ?string $description = 'Mensajes de contacto, blog y ofertas de eventos publicadas';

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

        $inquiries = $months->map(function (Carbon $month) {
            return PageInquiry::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        $blog = $months->map(function (Carbon $month) {
            return BlogPost::query()
                ->whereNotNull('published_at')
                ->whereBetween('published_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        $eventOffers = $months->map(function (Carbon $month) {
            return EventOffer::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Mensajes',
                    'data' => $inquiries,
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
                    'label' => 'Ofertas eventos',
                    'data' => $eventOffers,
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
