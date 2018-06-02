<?php

namespace CollabCorp\LaravelFeatureToggle;

use Illuminate\Support\ServiceProvider;

class FeatureToggleServiceProvider extends ServiceProvider 
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/features.php' => config_path('features.php'),
        ]);
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Feature::registerBladeIfDirective();
        Feature::registerJavaScriptBladeDirective();
    }
}
