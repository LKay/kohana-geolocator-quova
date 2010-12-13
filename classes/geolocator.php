<?php defined('SYSPATH') or die('No direct script access.');

class Geolocator {
	
	const QUOVA_API_URL = 'http://api.quova.com/v1/ipinfo/';
	
	public $ip;
	
	private $_api_key;
	private $_api_secret;
	
	private $_cache = TRUE;
	private $_cookie;
	private $_cookie_expire;
	
	public static function factory($ip) {
		return new Geolocator($ip);
	}
	
	public function __construct($ip = NULL) {
		if (isset($ip) && !Validate::ip($ip)) {
			throw new Geolocator_Exception('Error creating object [ status :code ] Incorrect IP address',array(':code'=>999));
		}
		$this->ip = $ip;
		$this->_api_key = Kohana::config('geolocator')->get('api_key');
		$this->_api_secret = Kohana::config('geolocator')->get('api_secret');
		$this->_cache = Kohana::config('geolocator')->get('cache');
		$this->_cookie = 'geo_'.md5($ip);
		$this->_cookie_expire = Kohana::config('geolocator')->get('cookie_expire');
	}
	
	public static function clear_cache() {
		return setcookie($this->_cookie, NULL, time() - 3600);
	}
	
	public function execute() {
		if (!$this->ip) {
			throw new Geolocator_Exception('Error executing request [ status :code ] Incorrect IP address',array(':code'=>999));
		}
		
		$query = http_build_query(array(
			'apikey' => $this->_api_key,
			'sig'    => $this->_get_sig(),
		));
		$options = array(
			CURLOPT_HTTPAUTH       => CURLAUTH_ANY,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_RETURNTRANSFER => TRUE,
		);
		
		try {
			if ($this->_cache && $cookie = Arr::get($_COOKIE,$this->_cookie)) {
				$response = base64_decode($cookie);
			} else {
				$response = Remote::get(self::QUOVA_API_URL.$this->ip.'?'.$query, $options);
			}
			
			$xml = new SimpleXMLElement($response);
			
			if ($xml->http_status) {
				throw new Geolocator_Exception('Error fetching data [ status :code ] :error',array(
					':code'  => $xml->http_status,
					':error' => $xml->message
				));
			} 
			
			if ($this->_cache) {
				setcookie($this->_cookie, base64_encode($response), time() + $this->_cookie_expire, '/');
			}
			
			return new Geolocation($xml);
			
		} catch (Kohana_Exception $e) {
			throw new Geolocator_Exception('Error fetching data [ status :code ] :error',array(
				':code'  => 404,
				':error' => 'Not Found',
			));
		}
		
	}
	
	private function _get_sig() {
		return md5($this->_api_key.$this->_api_secret.gmdate('U'));
	}
	
	public function __ToString() {
		return $this->ip;
	}
}