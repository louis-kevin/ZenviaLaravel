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
        $configPath = $this->getConfigDir();
        $publishPath = base_path('config/zenvia.php');
       
        $this->publishes([$configPath => $publishPath], 'config');

        if(config('zenvia.log',  true)){
            $channels = \Config::get('logging.channels');

            $channels[config('zenvia.channel',  'zenvia')] = [
                'driver' => 'single',
                'path' => storage_path('logs/zenvia.log'),
                'level' => 'debug',
            ];

            \Config::set('logging.channels', $channels);

            file_exists('storage/logs/zenvia.log')?:fopen('storage/logs/zenvia.log', 'x+');
        }
    }

    public function register() {
        $this->app->singleton('zenvia', function($app){
            $account = config('zenvia.account', env('ZENVIA_ACCOUNT'));
            $password = config('zenvia.password', env('ZENVIA_PASSWORD'));
            return new Zenvia($account, $password);
        });
    }

    public function provides()
    {
        return ['zenvia'];
    }

    private function getConfigDir(){
        return dirname(__DIR__).'/../config/config.php';
    }
}
