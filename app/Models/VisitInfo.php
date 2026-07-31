<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitInfo extends Model
{
    protected $table = 'visit_info';

    protected $fillable = [
        'address',
        'schedule',
        'phone_reservations',
        'phone_events',
        'email',
        'map_embed_url',
        'pedestrian_access',
        'vehicle_access',
        'amenities',
        'about_content',
    ];

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
            'amenities' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'address' => 'Av. Javier Prado Este 4492 – Santiago de Surco',
                'schedule' => [
                    ['days' => 'Domingo a Miércoles', 'hours' => '8 am a 10 pm'],
                    ['days' => 'Jueves', 'hours' => '8 am a 12 am'],
                    ['days' => 'Viernes y Sábado', 'hours' => '8 am a 1 am'],
                ],
                'phone_reservations' => '991318720',
                'phone_events' => '994848723',
                'email' => 'hola@refugiogastronomico.pe',
                'amenities' => [
                    'Pet Friendly',
                    '3 horas de estacionamiento gratis con S/50 de consumo',
                ],
            ]
        );
    }
}
