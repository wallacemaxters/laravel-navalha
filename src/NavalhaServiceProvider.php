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
            __DIR__.'/../public' => $this->app->publicPath('vendor/navalha'),
        ], 'navalha-assets');

        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'navalha');


        Blade::directive('navalhaScripts', function () {
            return <<<HTML
                <style>[x-cloak] { display: none !important; }</style>
                <script src="//unpkg.com/alpinejs" defer></script>
                <script src="<?php echo asset('vendor/navalha/app.js') ?>"></script>
            HTML;
        });
    }
}
