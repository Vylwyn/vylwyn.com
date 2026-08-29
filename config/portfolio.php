<?php

declare(strict_types=1);

/**
 * Site-wide details that appear in views, meta tags and structured data.
 *
 * Read these with config('portfolio.contact.email'), never env() directly.
 * Once `php artisan config:cache` runs in production, env() returns null
 * everywhere outside config files — a classic and confusing deployment bug.
 */
return [
    'name' => 'Vylwyn D’Souza',
    'full_name' => 'Vylwyn Anthony D’Souza',
    'role' => 'IT Team Lead & Full-Stack Developer',
    'specialisms' => 'Laravel · Flutter',
    'location' => 'Kuwait',
    'relocating_to' => 'India',

    'available' => env('PORTFOLIO_AVAILABLE', true),

    'contact' => [
        'email' => env('PORTFOLIO_EMAIL'),

        /** Digits only, international format, no + or spaces. E.g. 96512345678 */
        'whatsapp' => env('PORTFOLIO_WHATSAPP'),

        'linkedin' => env('PORTFOLIO_LINKEDIN', 'https://www.linkedin.com/in/vad90/'),
        'github' => env('PORTFOLIO_GITHUB', 'https://github.com/Vylwyn'),
    ],

    'seo' => [
        'title' => 'Vylwyn D’Souza — IT Team Lead & Full-Stack Developer',
        'description' => 'I lead a 22-person IT support and procurement team at Alghanim International in Kuwait, and build production Laravel and Flutter applications. MCA in Computer Science.',
    ],
];
