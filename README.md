## Usage

First of all you should set up your API key and secret in config file **config/geolocator.php**. You can disable caching so Geolocator wil always check API for given IP, otherwise it will check once for every IP address and store retrieved data in encoded cookie (In free account you have limit of 2 requests per second or 1000 a day, so it's useful. :) ). :

		return array
		(
			'api_key'       => 'Put your API key here',    // Quova api key
			'api_secret'    => 'Put your secret here',     // Quova secret
			'cache'         => TRUE,                       // Enables cache
			'cookie_expire' => 86400,                      // Cache cookie validity time
		);

Create an geolocator and send request

		$geolocator = Geolocator::factory('127.0.0.1');
		$geolocation = $geolocator->execute();
		echo 'You live in ' . $geolocation->location->country . ', in the city of ' . $geolocation->location->country . '<br />' ; 
		echo 'Your ISP is: ' . $geolocation->network->carrier . '<br />';

Of course the methods are chainable so to obtain ie. code of the country you can simply do:

		echo Geolocator::factory('127.0.0.1')->execute()->location->country_code;

If you want to erase all stored IPs just call:

		Geolocator::clear_cache();

I hope this is simple enough to maintain for now. I will try to improve it in the future for checking multiple IPs or operate other APIs not only Quova.
