## HttpCache for Laravel 4.1

Laravel 4.1 can easily use [HttpKernelInterface Middlewares](http://stackphp.com/middlewares/), so also [HttpCache](http://symfony.com/doc/current/book/http_cache.html).
This package provides a simple ServiceProvider to get you started with HttpCache.

First, require this package in composer.json and run `composer update`

    "barryvdh/laravel-httpcache": "*"

After updating, add the ServiceProvider to the array of providers in app/config/app.php

    'Barryvdh\HttpCache\ServiceProvider',

Caching is now enabled, for public responses. Just set the Ttl or MaxSharedAge

    Route::get('my-page', function(){
       return Response::make('Hello!')->setTtl(60); // Cache 1 minute
    });

You can also define a filter.

    Route::filter('cache', function($route, $request, $response, $age=60){
        $response->setTtl($age);
    });
    Route::get('cached', array('after' => 'cache:30', function(){
        return 'I am cached 30 seconds!';
    }));

Publish the config to change some options (cache dir, default ttl, etc) or enable ESI.

    $ php artisan config:publish barryvdh/laravel-httpcache

### ESI

Enable ESI in your config file. You can now define ESI includes in your layouts.

    <esi:include src="<?= url('partial/page') ?>"/>

This will render partial/page, with it's own TTL. The rest of the page will remain cached (using it's own TTL)

### Purging/flushing the cache

You can purge a single url or just delete the entire cache directory:

    App::make('http_cache.store')->purge($url);
    \File::cleanDirectory(app('http_cache.cache_dir'));

Or use the Artisan `httpcache:clear` command

    $ php artisan httpcache:clear

### More information
For more information, read the [Docs on Symfony HttpCache](http://symfony.com/doc/current/book/http_cache.html#symfony2-reverse-proxy)
