<?php

namespace WallaceMaxters\Navalha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class NavalhaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/navalha'),
        ], 'navalha-assets');

        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'navalha');

        Blade::directive('navalhaScripts', function () {
            return <<<HTML
                <script src="//unpkg.com/alpinejs" defer></script>
                <script src="<?php echo asset('vendor/navalha/app.js') ?>"></script>
            HTML;
        });
    }
}
