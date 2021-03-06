<?php namespace CompareAsiaGroup\LaravelWpApi;

use CompareAsiaGroup\GuzzleCache\Facades\GuzzleCache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LaravelWpApiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('wp-api.php'),
        ]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('wp-api', function ($app) {

            $endpoint   = Config::get('wp-api.endpoint');
            $prefix     = Config::get('wp-api.prefix', 'wp-api/');
            $options    = [
                'auth'              => Config::get('wp-api.auth', false),
                'debug'             => Config::get('wp-api.debug', false),
                'posts_per_page'    => Config::get('wp-api.posts_per_page', 10)
            ];
            $client     = GuzzleCache::client();

            return new WpApi($endpoint, $prefix, $client, $options);

        });

        $this->app->bind('CompareAsiaGroup\LaravelWpApi\WpApi', function($app)
        {
            return $app['wp-api'];
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['wp-api'];
	}

}
