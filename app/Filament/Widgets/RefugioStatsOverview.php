<?php

namespace App\Filament\Widgets;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\InstagramPost;
use App\Models\NewsletterSubscriber;
use App\Models\Restaurant;
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
            Stat::make('Eventos activos', Event::query()->active()->count())
                ->description('Visibles en /eventos')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
            Stat::make(
                'Blog',
                BlogPost::query()->active()->count()
            )
                ->description(
                    ($settings->show_blog_section ? 'Sección ON' : 'Sección OFF')
                    .' · '.BlogPost::query()->active()->featured()->count().' en home'
                )
                ->descriptionIcon('heroicon-m-newspaper')
                ->color($settings->show_blog_section ? 'primary' : 'gray'),
            Stat::make('Instagram activos', InstagramPost::query()->active()->count())
                ->description('Feed home (máx. 12)')
                ->descriptionIcon('heroicon-m-camera')
                ->color('info'),
            Stat::make('Newsletter', NewsletterSubscriber::query()->count())
                ->description('Suscriptores registrados')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
        ];
    }
}
