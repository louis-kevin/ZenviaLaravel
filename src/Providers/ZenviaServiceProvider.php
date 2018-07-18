<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 12:19
 */

namespace Louis\Zenvia\Providers;


use Louis\Zenvia\Commands\SendSmsTest;
use Illuminate\Support\ServiceProvider;
use Louis\Zenvia\Services\Zenvia;

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
        $configPath = dirname(__DIR__).'/../config/config.php';
       
        if (function_exists('config_path')) {
            $publishPath = config_path('zenvia.php');
        } else {
            $publishPath = base_path('config/zenvia.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register() {
        $this->app->singleton('zenvia', function($app){
            $account = $app[ 'config' ]->get('zenvia.account')?:'';
            $password = $app[ 'config' ]->get('zenvia.password')?:'';
            return new Zenvia($account, $password);
        });
    }

    public function provides()
    {
        return ['zenvia'];
    }
}
