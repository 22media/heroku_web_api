## Heroku Web API

This package could be used to scale dynos and get information on current dyno sizes. If there are more needs to use the heroku API create a pull request and will be implemented soon. 

### Install the package

The package could be installed by using composer like this and be used like any other composer package afterwards (see https://getcomposer.org/doc/01-basic-usage.md for basic usage).

```
composer require mcgo/heroku-web-api
```

### Basic usage information

To use the package you need to know your heroku key and the machine readble app name. Both could be fetched from your heroku account page under security. The package uses two methods to setup the credentials for communicating with your heroku app. 

#### Credentials via environment variables
 
Make sure you have created a environment variable for the app and the key with the Names `HEROKU_KEY` and `HEROKU_APP`. These will be used automatically. If they are not found, an Exception will be thrown.

#### Set credentials manually

If you do not want to use environment variables are want to communicate with more than one app, you could directly set the correct values by using the appropriate methods `setApp()` and `setKey()`. Check the example below.
 
### Usage as Laravel ServiceProvider

You could use this package from inside your Laravel application as a service provider. Just add a line to your app's config provider array in `config/app.php` like this:
 
 ```
   'providers' => [   
     // ...   
     // Heroku Scaling
     McGo\HerokuWebAPIServiceProvider::class,
     // ...
  ],
 ```
 
 and to your aliases: 
 
 ```
  'aliases' => [
    // ...
    'Heroku'   => McGo\HerokuWebAPIFacade::class,
    // ...
 ```
 Afterwards you could use it everywhere in your app like `Heroku::scaleDyno('worker', 1)` or to show the current size of your dynos in a blade template `Heroku::getDynoSize('web')`
 
### Example
 
```PHP
try {
  // Instantiate a new HerokuWebAPI object
  $heroku = new HerokuWebAPI();

  // Get the current dyno size of web dynos
  $web_size = $heroku->getDynoSize('web');

  // Get the current siez of worker dynos
  $worker_size = $heroku->getDynoSize('worker');

  // Scale worker to 100
  $heroku->scaleDyno('worker', 100);
  
  // Control another app with the same key. The $heroku methods setApp, setKey and scaleDyno support method chaining, so you could do something like this.
  $heroku->setApp('my-other-app')->scaleDyno('web', 1)->scaleDyno('worker', 0);
  
  // Please note, that if you use credentials only by settings them with the methods, it is important to do it directly
  // after newing up the Object. Otherwise it would result in a HerokuCredentialsNotSetException. This is no correct
  // usage and will only work if environment variables are set.
  $wrongusage = new HerokuWebAPI();
  $wrongusage->scaleDyno('web', 0)->setApp('my-app');

} catch (HerokuCredentialsNotSetException $e) {
  // The credentials are not set. Either do so by using the environment
  // variables HEROKU_KEY and HEROKU_APP or storing them by using the
  // appropriate methods setApp() and setKey();
  
} catch (HerokuRequestFailedException $e) {
  // The request to Heroku was not completed with a 200 status.
    
}
```