<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin email allowlist
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of email addresses (case-insensitive) that are
    | allowed to access the Filament admin panel in any environment.
    |
    | Set FILAMENT_ADMIN_EMAILS in the server .env, e.g.:
    |   FILAMENT_ADMIN_EMAILS=you@example.com,partner@example.com
    |
    */
    'emails' => array_values(array_filter(array_map(
        fn (string $email): string => strtolower(trim($email)),
        explode(',', (string) env('FILAMENT_ADMIN_EMAILS', '')),
    ))),
];
