<?php
// app/Providers/YandexServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class YandexServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('yandex', function () use ($socialite) {
            $config = config('services.yandex');
            
            return $socialite->buildProvider(\SocialiteProviders\Yandex\Provider::class, $config);
        });
    }
}