<?php

namespace App\Providers;

use App\User;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot() {
        $this->app['auth']->viaRequest('api', function ($request){return $this->getApiTokenUser($request);} );
        $this->app['auth']->viaRequest('topsecret', function ($request){return $this->getApiTokenUser($request);} );
    }

    // Verifico que el api_token venga en request o header
    private function getApiTokenUser($request) {
        $apiToken = $request->input('api_token');

        if ($apiToken === null) {
            $apiToken = $request->header('api_token');
        }

        $return = null;
        if ($apiToken && $apiToken == env('API_TOKEN')) {
            $return = new GenericUser(['id' => 1, 'name' => 'User']);
        }

        return $return;
    }
}
