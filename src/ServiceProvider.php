<?php

namespace Barryvdh\HttpCache;

use Barryvdh\HttpCache\Middleware\CacheRequests;
use Barryvdh\StackMiddleware\StackMiddleware;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\Esi;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;

class ServiceProvider extends BaseServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $app = $this->app;

        $configPath = __DIR__ . '/../config/httpcache.php';
        $this->mergeConfigFrom($configPath, 'httpcache');

        if (function_exists('config_path')) {
            $this->publishes([$configPath => config_path('httpcache.php')], 'config');
        }

        $app['http_cache.options'] = array_replace(
            array(
                'debug' => $app['config']->get('app.debug'),
            ), $app['config']->get('httpcache.options')
        );

        $app['http_cache.cache_dir'] = $app['config']->get('httpcache.cache_dir');

        $app->singleton(Store::class, function ($app) {
            return new Store($app['http_cache.cache_dir']);
        });
        $app->alias(Store::class, StoreInterface::class);

        $app->singleton( Esi::class, function ($app) {
            if( $app['config']->get('httpcache.esi') ){
                return new Esi();
            }
        });
        $app->alias(Esi::class, SurrogateInterface::class);

        $this->app->bind('command.httpcache.clear', function($app)
        {
            return new Console\ClearCommand($app['files']);
        });
        $this->commands('command.httpcache.clear');
	}

    public function boot()
    {
        $this->app->make(StackMiddleware::class)->bind(CacheRequests::class,
            function($app) {
              if(! $this->app['config']->get('httpcache.enabled')) {
                return $app;
              }

              return new HttpCache(
                  $app,
                  $this->app->make(StoreInterface::class),
                  $this->app->make(Esi::class),
                  $this->app['http_cache.options']
              );
            }
        );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(Esi::class, Store::class, StoreInterface::class, 'http_cache.cache_dir', 'http_cache.options', 'command.httpcache.clear');
	}
}
