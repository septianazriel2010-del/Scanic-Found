<?php

namespace App\Providers;

use App\Models\Claim;
use App\Models\ItemReport;
use App\Policies\ClaimPolicy;
use App\Policies\ItemReportPolicy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Daftarkan service apa pun untuk aplikasi.
     */
    public function register(): void
    {
        //
    }

    /**
     * Jalankan proses bootstrapping aplikasi.
     * Di sini kita daftarkan Policy secara manual (Laravel 11+ tidak lagi
     * memakai AuthServiceProvider bawaan, tapi Policy tetap bisa didaftarkan
     * lewat Gate::policy di AppServiceProvider).
     */
    public function boot(): void
    {
        Gate::policy(ItemReport::class, ItemReportPolicy::class);
        Gate::policy(Claim::class, ClaimPolicy::class);

        // Supaya translatedFormat('d M Y') di semua view Blade otomatis
        // muncul dalam Bahasa Indonesia (Jan -> Jan, Senin, dst), tanpa ini
        // Carbon default-nya tetap pakai locale "en" walau APP_LOCALE=id.
        Carbon::setLocale(config('app.locale'));
    }
}
