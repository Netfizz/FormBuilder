<?php namespace Netfizz\FormBuilder;

use Illuminate\Html\HtmlServiceProvider;
use Netfizz\FormBuilder\FormBuilder;


class FormBuilderServiceProvider_old extends HtmlServiceProvider {
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
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->bindShared('formizz', function($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }

}
