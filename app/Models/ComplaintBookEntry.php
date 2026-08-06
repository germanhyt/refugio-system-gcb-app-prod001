<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintBookEntry extends Model
{
    protected $fillable = [
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'department',
        'address',
        'phone',
        'email',
        'parent_name',
        'claimed_amount',
        'product_description',
        'claim_type',
        'claim_detail',
        'consumer_request',
        'ip_address',
        'user_agent',
    ];

    /** @return list<string> */
    public static function departments(): array
    {
        return [
            'Amazonas',
            'Áncash',
            'Apurímac',
            'Arequipa',
            'Ayacucho',
            'Cajamarca',
            'Callao',
            'Cusco',
            'Huancavelica',
            'Huánuco',
            'Ica',
            'Junín',
            'La Libertad',
            'Lambayeque',
            'Lima',
            'Loreto',
            'Madre de Dios',
            'Moquegua',
            'Pasco',
            'Piura',
            'Puno',
            'San Martín',
            'Tacna',
            'Tumbes',
            'Ucayali',
        ];
    }
}
