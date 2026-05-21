<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpWord\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        Settings::setTempDir($tempDir);

        if (! class_exists(\ZipArchive::class)) {
            $pclZip = base_path('vendor/phpoffice/phpword/src/PhpWord/Shared/PCLZip/pclzip.lib.php');
            if (is_file($pclZip)) {
                require_once $pclZip;
            }

            Settings::setZipClass(Settings::PCLZIP);
        }
    }
}
