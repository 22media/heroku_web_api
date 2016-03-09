<?php
namespace McGo\HerokuWebAPI\Facade;

use Illuminate\Support\Facades\Facade;

class Heroku extends Facade {
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() {
    return 'heroku';
  }
}