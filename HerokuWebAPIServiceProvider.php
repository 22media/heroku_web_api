<?php

namespace McGo\HerokuWebAPI;

use Illuminate\Support\ServiceProvider;

class HerokuWebAPIServiceProvider extends ServiceProvider {
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->singleton('heroku', function () {
      return new HerokuWebAPI();
    });
  }
}
