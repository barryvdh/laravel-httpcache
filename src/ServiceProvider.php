<?php namespace Barryvdh\HttpCache;

use Barryvdh\StackMiddleware\StackMiddleware;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\Esi;

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
        $this->publishes([$configPath => config_path('httpcache.php')], 'config');

        $app['http_cache.options'] = array_replace(
            array(
                'debug' => $app['config']->get('app.debug'),
            ), $app['config']->get('httpcache.options')
        );

        $app['http_cache.cache_dir'] = $app['config']->get('httpcache.cache_dir');

        $app['http_cache.store'] = $app->share(function ($app) {
            return new Store($app['http_cache.cache_dir']);
        });

        $app['http_cache.esi'] = $app->share(function ($app) {
            if( $app['config']->get('httpcache.esi') ){
                return new Esi();
            }
        });

        $app->alias('http_cache.esi', 'Symfony\Component\HttpKernel\HttpCache\Esi');

        $this->app['command.httpcache.clear'] = $this->app->share(function($app)
        {
            return new Console\ClearCommand($app['files']);
        });
        $this->commands('command.httpcache.clear');
	}

    public function boot(StackMiddleware $stack)
    {
        $stack->bind(
          'Barryvdh\HttpCache\Middleware\CacheRequests',
          'Symfony\Component\HttpKernel\HttpCache\HttpCache',
          [
            $this->app['http_cache.store'],
            $this->app['http_cache.esi'],
            $this->app['http_cache.options']
          ]
        );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('http_cache.store', 'http_cache.esi', 'http_cache.cache_dir', 'http_cache.options', 'command.httpcache.clear');
	}
}
