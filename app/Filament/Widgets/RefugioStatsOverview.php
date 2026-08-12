<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\EventOffer;
use App\Models\PageInquiry;
use App\Models\Restaurant;
use App\Models\ServiceItem;
use App\Models\SiteSetting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RefugioStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $settings = SiteSetting::current();

        return [
            Stat::make('Restaurantes activos', Restaurant::query()->active()->count())
                ->description('Visibles en /restaurantes')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),
            Stat::make('Ofertas de eventos', EventOffer::query()->active()->count())
                ->description('Tarjetas en /eventos')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
            Stat::make('Servicios activos', ServiceItem::query()->active()->count())
                ->description(ServiceItem::query()->active()->showOnHome()->count().' en preview home')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),
            Stat::make(
                'Blog',
                BlogPost::query()->active()->count()
            )
                ->description(
                    ($settings->show_blog_section ? 'Rutas /blog ON' : 'Rutas /blog OFF')
                    .' · solo footer (no home)'
                )
                ->descriptionIcon('heroicon-m-newspaper')
                ->color($settings->show_blog_section ? 'gray' : 'gray'),
            Stat::make('Mensajes de contacto', PageInquiry::query()->count())
                ->description('Formulario de /contacto y convocatorias')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
        ];
    }
}
