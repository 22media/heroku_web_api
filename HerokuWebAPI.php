<?php

namespace McGo\HerokuWebAPI;

use Exception;
use GuzzleHttp\Client;

class HerokuWebAPI {
  private $heroku_key = false;
  private $heroku_app = false;
  private $client = null;

  /**
   * HerokuWebAPI constructor. Loads credentials from the configuration
   */
  public function __construct() {
    // Get the Heroku credentials from environment variables.
    $this->heroku_key = getenv('HEROKU_KEY');
    $this->heroku_app = getenv('HEROKU_APP');

    // Initialize the Guzzle Client.
    $this->client = new Client();
  }

  /**
   * Return the current app name.
   *
   * @return bool|string
   */
  public function getApp() {
    return $this->heroku_app;
  }

  /**
   * Per default this is meant to work with one app, defined in the environment
   * variable.
   *
   * @param $value
   * @return $this
   */
  public function setApp($value) {
    $this->heroku_app = $value;
    return $this;
  }

  /**
   * If one wants to use the class with several apps / keys they could be set
   * directly and not rely on the environment variables.
   *
   * @param $value
   * @return $this
   */
  public function setKey($value) {
    $this->heroku_key = $value;
    return $this;
  }

  /**
   * Scale a dyno of the given type to the desired size
   *
   * @param $type
   * @param $size
   * @return $this
   */
  public function scaleDyno($type, $size) {
    $url = $this->getBaseURL() ."/formation/". $type;
    $payload = ['quantity' => $size];
    $this->client->request('PATCH', $url, ['headers' => $this->getHeader(), 'json' => $payload]);
    return $this;
  }

  /**
   * Return the current amount of used dynos of the given type.
   *
   * @param $type
   * @return int
   * @throws HerokuCredentialsNotSetException
   * @throws HerokuRequestFailedException
   */
  public function getDynoSize($type) {
    $url = $this->getBaseURL().'/formation';
    $response = $this->client->get($url, ['headers' => $this->getHeader()]);
    if ($response->getStatusCode() == 200) {
      $dynos = json_decode((string)$response->getBody());

      foreach ($dynos as $dyno) {
        if ($dyno->type === $type) return $dyno->quantity;
      }
    } else {
      throw new HerokuRequestFailedException();
    }
    return 0;
  }

  /**
   * Returns the base URL for the current app.
   *
   * @return string
   * @throws HerokuCredentialsNotSetException
   */
  private function getBaseURL() {
    if (!$this->heroku_app) throw new HerokuCredentialsNotSetException();
    return "https://api.heroku.com/apps/" .$this->heroku_app;
  }

  /**
   * Builds the correct header for a request to heroku
   * @return array
   * @throws HerokuCredentialsNotSetException
   */
  private function getHeader() {
    if (!$this->heroku_key) throw new HerokuCredentialsNotSetException();
    $basekey = base64_encode(":". $this->heroku_key);
    return [
      "Accept" => "application/vnd.heroku+json; version=3",
      "Authorization" => $basekey,
    ];
  }

}

class HerokuCredentialsNotSetException extends Exception {}
class HerokuRequestFailedException extends Exception {}