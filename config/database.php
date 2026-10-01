<?php

use Illuminate\Support\Str;

return [
    // Default sekarang pgsql karena project ini pakai Supabase (PostgreSQL).
    // Koneksi 'mysql' tetap disediakan di bawah kalau suatu saat mau balik
    // ke MySQL/XAMPP, tinggal ganti DB_CONNECTION di .env.
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'scanic_trace'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => [],
        ],

        // Koneksi ke Supabase. Supabase = PostgreSQL yang di-hosting, jadi
        // dari sisi Laravel cukup pakai driver 'pgsql' bawaan dengan
        // kredensial yang diambil dari Supabase Dashboard > Project Settings
        // > Database > Connection info.
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            // Supabase MEWAJIBKAN koneksi terenkripsi. 'prefer' cukup untuk
            // kebanyakan kasus karena Supabase otomatis upgrade ke SSL,
            // tapi kalau muncul error SSL, ganti ke 'require' di .env.
            'sslmode' => env('DB_SSLMODE', 'prefer'),
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],
];
