<?php

namespace WallaceMaxters\Navalha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Wallacemaxters\Navalha\Commands\MakeComponent;

class NavalhaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            MakeComponent::class,
        ]);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../public' => $this->app->publicPath('vendor/navalha'),
        ], 'navalha-assets');

        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'navalha');

        Blade::directive('navalha', function ($expression) {
            return <<<PHP
                <?php
                    (function (string \$name, array \$args = []) {

                        \$instance = app('\App\Navalha\\\' . \$name, \$args);
                        \$instance->setUp();
                        \$params = [
                            'component' => \$name,
                            'data'      => \$instance->jsonSerialize(),
                            'csrf'      => csrf_token()
                        ];

                        printf('<div x-cloak x-data="__navalha_component__(%s)">%s</div>', Js::from(\$params), \$instance->render());

                    })({$expression});
                ?>
                PHP;
        });

        Blade::directive('navalhaStyles', function () {
            return <<<HTML
                <style>[x-cloak] { display: none !important; }</style>
            HTML;
        });
        
        Blade::directive('navalhaScripts', function ($expression) {

            $script = $expression === 'false' ? '' : sprintf('<script src=%s defer></script>', $expression ?: '//unpkg.com/alpinejs');
            return <<<HTML
                {$script}
                <script src="<?php echo asset('vendor/navalha/app.js') ?>"></script>
            HTML;
        });
    }
}
