<?php

return [
    'source_url' => env('REFUGIO_SOURCE_URL', 'https://refugiogastronomico.pe'),
    'scraper_delay_ms' => (int) env('REFUGIO_SCRAPER_DELAY_MS', 500),
    'events_year' => (int) env('REFUGIO_EVENTS_YEAR', date('Y')),
    'events_month' => (int) env('REFUGIO_EVENTS_MONTH', 7),
    'complaint_book_recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env(
            'REFUGIO_COMPLAINT_BOOK_RECIPIENTS',
            'leilah@gcb.pe,mario@refugiogastronomico.pe,nataly@gcb.pe'
        ))
    ))),
];
