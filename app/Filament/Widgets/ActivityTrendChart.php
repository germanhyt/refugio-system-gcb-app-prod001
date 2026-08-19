<?php

namespace App\Filament\Widgets;

use App\Models\ComplaintBookEntry;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ActivityTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Actividad últimos 6 meses';

    protected static ?string $description = 'Reclamaciones y suscriptores de newsletter';

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

        $complaints = $months->map(function (Carbon $month) {
            return ComplaintBookEntry::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        $newsletter = $months->map(function (Carbon $month) {
            return NewsletterSubscriber::query()
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->count();
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Reclamaciones',
                    'data' => $complaints,
                    'backgroundColor' => '#A7623D',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Newsletter',
                    'data' => $newsletter,
                    'backgroundColor' => '#236869',
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
