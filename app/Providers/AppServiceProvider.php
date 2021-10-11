<?php

namespace App\Providers;

use App\Option;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $cacheValidity = Carbon::now()->addHours(1);

        $options = Cache::remember('options', $cacheValidity, function() {
            $opts = Option::pluck('value', 'key');
            unset($opts['site.gdrive']);
            return  $opts;
        });


        Cache::remember('theme', $cacheValidity, function() use ($options) {
            $theme = $options['site.theme'];
            if (strpos($theme, 'default') !== false) {
                $tab = explode('.', $theme);
                $theme = $tab[0];
            }
            return $theme;
        });

        Cache::remember('variation', $cacheValidity, function() use ($options) {
            $theme = $options['site.theme'];
            $variation = "";
            if (strpos($theme, 'default') !== false) {
                $tab = explode('.', $theme);
                $variation = $tab[1];
            }
            return $variation;
        });

        $subscription = json_decode($options['site.subscription']);

        // set language
        App::setLocale($options['site.lang'], 'en');

        // set orientation
        Config::set('orientation', $options['site.orientation']);

        // allow subscribe
        Config::set('subscribe', ($subscription->subscribe === 'true'));

        // default role
        Config::set('default_role', $subscription->default_role);

        // dd(Cache::get('theme'));
    }
}
