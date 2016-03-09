<?php
namespace McGo\HerokuWebAPI;

use Illuminate\Support\Facades\Facade;

class HerokuWebAPIFacade extends Facade {
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() {
    return 'heroku';
  }
}