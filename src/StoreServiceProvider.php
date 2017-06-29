<?php

namespace Solunes\Store;

use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot() {
        /* Publicar Elementos */
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], 'config');

        /* Cargar Traducciones */
        $this->loadTranslationsFrom(__DIR__.'/lang', 'store');

        /* Cargar Vistas */
        $this->loadViewsFrom(__DIR__ . '/views', 'store');
    }


    public function register() {
        /* Registrar ServiceProvider Internos */
        //$this->app->register('Collective\Html\HtmlServiceProvider');

        /* Registrar Alias */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        //$loader->alias('HTML', 'Collective\Html\HtmlFacade');

        $loader->alias('Store', '\Solunes\Store\App\Helpers\Store');

        /* Comandos de Consola */
        $this->commands([
            \Solunes\Store\App\Console\AccountCheck::class,
            \Solunes\Store\App\Console\TestSystem::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/config/store.php', 'store'
        );
    }
    
}
