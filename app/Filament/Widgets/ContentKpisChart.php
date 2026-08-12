<?php

namespace App\Filament\Widgets;

use App\Models\EventOffer;
use App\Models\Restaurant;
use App\Models\ServiceItem;
use App\Models\ContactBlock;
use Filament\Widgets\ChartWidget;

class ContentKpisChart extends ChartWidget
{
    protected static ?string $heading = 'Contenido activo';

    protected static ?string $description = 'Piezas visibles en el sitio restructurado';

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
        $eventOffers = EventOffer::query()->active()->count();
        $services = ServiceItem::query()->active()->count();
        $contactBlocks = ContactBlock::query()->active()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Activos',
                    'data' => [$restaurants, $eventOffers, $services, $contactBlocks],
                    'backgroundColor' => [
                        '#A7623D',
                        '#C4895F',
                        '#236869',
                        '#729F9F',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Restaurantes',
                'Ofertas eventos',
                'Servicios',
                'Contacto home',
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
