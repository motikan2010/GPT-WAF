<?php

namespace Motikan2010\GptWaf;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->aliasMiddleware('gpt-waf', 'Motikan2010\GptWaf\Middleware\GptWaf');
    }

}
