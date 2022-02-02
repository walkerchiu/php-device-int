<?php

namespace WalkerChiu\Device;

use Illuminate\Support\ServiceProvider;

class DeviceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/device.php' => config_path('wk-device.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_device_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_device_table.php',
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-device');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-device'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-device.command.cleaner')
            ]);
        }

        config('wk-core.class.device.device')::observe(config('wk-core.class.device.deviceObserver'));
        config('wk-core.class.device.deviceLang')::observe(config('wk-core.class.device.deviceLangObserver'));
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-device')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/device.php', 'wk-device'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/device.php', 'device'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
