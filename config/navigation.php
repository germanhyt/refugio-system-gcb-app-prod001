<?php

return [
    'primary' => [
        ['label' => 'Nosotros', 'route' => 'about'],
        ['label' => 'Restaurantes', 'route' => 'restaurants.index'],
        ['label' => 'Eventos', 'route' => 'events.index'],
    ],

    'footer_secondary' => [
        ['label' => 'Blog', 'route' => 'blog.index'],
        ['label' => 'Descuentos U. Lima', 'route' => 'static.ulima'],
        ['label' => 'Preguntas Frecuentes', 'route' => 'static.faq'],
        ['label' => 'Reglamento de Pet Friendly', 'route' => 'static.pet-friendly'],
        ['label' => 'Política de estacionamiento', 'route' => 'static.parking'],
    ],

    'footer_legal' => [
        ['label' => 'Términos y condiciones', 'route' => 'legal.terms'],
        ['label' => 'Políticas de privacidad', 'route' => 'legal.privacy'],
        ['label' => 'Libro de reclamaciones', 'route' => 'legal.complaints'],
    ],
];
