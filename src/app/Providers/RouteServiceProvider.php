<?php

namespace Solunes\Store\App\Providers;

use Illuminate\Routing\Router;
use Solunes\Master\App\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $storeNamespace = 'Solunes\Store\App\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        //$this->mapWebRoutes($router);
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapAdminRoutes(Router $router)
    {
        $router->group(['namespace' => $this->storeNamespace, 'middleware' => 'admin'], function ($router) {
            require __DIR__ . '/../Routes/admin.php';
        });
        parent::mapAdminRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group(['namespace' => $this->storeNamespace, 'middleware' => 'web'], function ($router) {
            require __DIR__ . '/../Routes/routes.php';
        });
        parent::mapWebRoutes($router);
    }

}
