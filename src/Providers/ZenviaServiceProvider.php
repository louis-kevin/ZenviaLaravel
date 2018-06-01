<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 12:19
 */

namespace Louis\Zenvia\Providers;


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
        $source = dirname(__DIR__).'/../../config/config.php';
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('zenvia.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('zenvia');
        }
        $this->mergeConfigFrom($source, 'zenvia');
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
