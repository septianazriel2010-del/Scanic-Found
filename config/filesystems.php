<?php

return [
    'default' => env('FILESYSTEM_DISK', 'public'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Dipakai saat deploy ke Vercel (filesystem container tidak
        // permanen, jadi foto laporan wajib disimpan di storage eksternal).
        // Supabase Storage kompatibel dengan API S3, jadi cukup pakai
        // driver 's3' bawaan Laravel dengan endpoint milik Supabase.
        // Ambil nilai-nilai env di bawah dari Supabase Dashboard >
        // Project Settings > Storage > S3 Connection.
        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_S3_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_S3_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_S3_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_S3_BUCKET'),
            'endpoint' => env('SUPABASE_S3_ENDPOINT'),
            'url' => env('SUPABASE_S3_PUBLIC_URL'),
            'use_path_style_endpoint' => true,
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
