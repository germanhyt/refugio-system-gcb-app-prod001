<?php

namespace App\Filament\Widgets;

use App\Models\ContactBlock;
use App\Models\EventOffer;
use App\Models\HomeRestaurantFeature;
use App\Models\Restaurant;
use App\Models\ServiceItem;
use Filament\Widgets\ChartWidget;

class ContentKpisChart extends ChartWidget
{
    protected static ?string $heading = 'Contenido activo';

    protected static ?string $description = 'Piezas publicadas en el sitio';

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
        return [
            'datasets' => [
                [
                    'label' => 'Activos',
                    'data' => [
                        Restaurant::query()->active()->count(),
                        HomeRestaurantFeature::query()->active()->count(),
                        EventOffer::query()->active()->count(),
                        ServiceItem::query()->active()->count(),
                        ContactBlock::query()->active()->count(),
                    ],
                    'backgroundColor' => [
                        '#A7623D',
                        '#C4895F',
                        '#236869',
                        '#729F9F',
                        '#3d332c',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Restaurantes',
                'Cinta de logos',
                'Eventos',
                'Servicios',
                'Contacto',
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
