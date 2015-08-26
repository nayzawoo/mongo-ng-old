<?php

namespace App\Libs\Html;

use App\Libs\Html\FormBuilder;
use Illuminate\Html\HtmlServiceProvider as IlluminateHtmlServiceProvider;

class HtmlServiceProvider extends IlluminateHtmlServiceProvider
{
    /**
     * Register the form builder instance.
     */
    protected function registerFormBuilder()
    {
        $this->app->bindShared('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }
}
