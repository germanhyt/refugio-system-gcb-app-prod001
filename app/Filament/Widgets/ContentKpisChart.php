<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\InstagramPost;
use App\Models\Restaurant;
use Filament\Widgets\ChartWidget;

class ContentKpisChart extends ChartWidget
{
    protected static ?string $heading = 'Contenido activo';

    protected static ?string $description = 'Distribución de piezas visibles en el sitio';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $restaurants = Restaurant::query()->active()->count();
        $events = Event::query()->active()->count();
        $blog = BlogPost::query()->active()->count();
        $instagram = InstagramPost::query()->active()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Activos',
                    'data' => [$restaurants, $events, $blog, $instagram],
                    'backgroundColor' => [
                        '#A7623D',
                        '#C4895F',
                        '#8B5E3C',
                        '#D4A574',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Restaurantes',
                'Eventos',
                'Blog',
                'Instagram',
            ],
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
            'cutout' => '58%',
        ];
    }
}
