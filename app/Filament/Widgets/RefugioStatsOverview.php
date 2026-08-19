<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ComplaintBookEntryResource;
use App\Filament\Resources\EventOfferResource;
use App\Filament\Resources\HomeRestaurantFeatureResource;
use App\Filament\Resources\RestaurantResource;
use App\Filament\Resources\ServiceItemResource;
use App\Models\ComplaintBookEntry;
use App\Models\ContactBlock;
use App\Models\EventOffer;
use App\Models\HomeRestaurantFeature;
use App\Models\Restaurant;
use App\Models\ServiceItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RefugioStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $complaintsTotal = ComplaintBookEntry::query()->count();
        $complaintsMonth = ComplaintBookEntry::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $complaintsWeek = ComplaintBookEntry::query()
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        return [
            Stat::make('Restaurantes activos', Restaurant::query()->active()->count())
                ->description('Visibles en /restaurantes')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success')
                ->url(RestaurantResource::getUrl('index')),
            Stat::make('Cinta de logos', HomeRestaurantFeature::query()->active()->count())
                ->description('Logos del home')
                ->descriptionIcon('heroicon-m-photo')
                ->color('success')
                ->url(HomeRestaurantFeatureResource::getUrl('index')),
            Stat::make('Ofertas de eventos', EventOffer::query()->active()->count())
                ->description('Tarjetas en /eventos')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning')
                ->url(EventOfferResource::getUrl('index')),
            Stat::make('Servicios activos', ServiceItem::query()->active()->count())
                ->description(ServiceItem::query()->active()->showOnHome()->count().' en preview home')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary')
                ->url(ServiceItemResource::getUrl('index')),
            Stat::make('Reclamaciones', $complaintsTotal)
                ->description($complaintsMonth.' este mes · '.$complaintsWeek.' esta semana')
                ->descriptionIcon('heroicon-m-book-open')
                ->color($complaintsMonth > 0 ? 'danger' : 'gray')
                ->url(ComplaintBookEntryResource::getUrl('index')),
            Stat::make('Canales de contacto', ContactBlock::query()->active()->count())
                ->description('Sección «¿Dudas? ¡Contáctanos!»')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
        ];
    }
}
