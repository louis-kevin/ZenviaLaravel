<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 12:19
 */

namespace Zenvia\Providers;


use Zenvia\Commands\SendSmsTest;
use Illuminate\Support\ServiceProvider;
use Zenvia\Services\Zenvia;

class ZenviaServiceProvider extends ServiceProvider
{

    protected $defer = true;
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SendSmsTest::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('zenvia.php'),
        ]);
    }

    public function register() {
        $this->app->singleton('zenvia', function($app){
            return new Zenvia($app[ 'config' ]->get('zenvia.account'), $app[ 'config' ]->get('zenvia.password'));
        });
    }

    public function provides()
    {
        return ['zenvia'];
    }
}
