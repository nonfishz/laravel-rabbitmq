<?php
namespace Nonfishz\RabbitMQ;

use Illuminate\Support\ServiceProvider;

class RabbitMQServiceProvider extends ServiceProvider
{
    public function boot ()
    {

    }

    public function register ()
    {
        $this->app->bind('Nonfishz\RabbitMQ\RabbitMQ', function ($app) {
            $config = $app['config']->get("rabbitmq");
            return new RabbitMQ($config);
        });
    }
}
