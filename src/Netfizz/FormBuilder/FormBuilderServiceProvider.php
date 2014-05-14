<?php namespace Netfizz\FormBuilder;

use Illuminate\Support\ServiceProvider;

class FormBuilderServiceProvider extends ServiceProvider {

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
        $this->package('netfizz/form-builder');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bindShared('formizz.builder', function($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());
            return $form->setSessionStore($app['session.store']);
        });

        /*
        $this->app->bindShared('formizz', function($app)
        {
            //return new Formizz($app['formizz.builder']);
            return new Component();
        });
        */
    }

    /**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('formizz.builder');
	}

}
