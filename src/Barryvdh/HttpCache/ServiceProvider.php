<?php namespace Barryvdh\HttpCache;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\HttpKernel\HttpCache\Esi;

class ServiceProvider extends BaseServiceProvider {

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
        $app['config']->package('barryvdh/laravel-httpcache', $this->guessPackagePath() . '/config');

        $app['http_cache.options'] = array_replace(
            array(
                'debug' => $app['config']->get('app.debug'),
            ), $app['config']->get('laravel-httpcache::config.options')
        );

        $app['http_cache.cache_dir'] = $app['config']->get('laravel-httpcache::config.cache_dir');

        $app['http_cache.store'] = $app->share(function ($app) {
            return new Store($app['http_cache.cache_dir']);
        });

        $app['http_cache.esi'] = $app->share(function ($app) {
            if( $app['config']->get('laravel-httpcache::config.esi') ){
                return new Esi();
            }
        });

        if( $app['config']->get('laravel-httpcache::config.enabled') ){

            $app->middleware('Symfony\Component\HttpKernel\HttpCache\HttpCache', array($app['http_cache.store'], $app['http_cache.esi'], $app['http_cache.options']));

            if( $app['config']->get('laravel-httpcache::config.esi') ){
                $app->middleware('Barryvdh\HttpCache\EsiMiddleware', array($app['http_cache.esi']));
            }
        }

        $this->app['command.httpcache.clear'] = $this->app->share(function($app)
        {
            return new Console\ClearCommand($app['files']);
        });
        $this->commands('command.httpcache.clear');

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
