<?php defined('SYSPATH') or die('No direct script access.');

class Geolocation {
	
	private $_xml;
	
	private $_data = array();
	
	public function __construct(SimpleXMLElement $xml) {
		$this->_xml = $xml;
		$this->_data['ip'] = (string) $xml->ip_address;
		$this->_data['ip_type'] = (string) $xml->ip_type;
		$this->_data['network'] = $this->_get_network();		
		$this->_data['location'] = $this->_get_location();		
	}
	
	private function _get_network() {
		$network = new Geolocation_Network;
		$network->organization = (string) $this->_xml->Network->organization;
		$network->carrier = (string) $this->_xml->Network->carrier;
		$network->asn = (string) $this->_xml->Network->asn;
		$network->connection_type = (string) $this->_xml->Network->connection_type;
		$network->line_speed = (string) $this->_xml->Network->line_speed;
		$network->ip_routing_type = (string) $this->_xml->Network->ip_routing_type;
		$network->domain_tld = (string) $this->_xml->Network->Domain->tld;
		$network->domain_sld = (string) $this->_xml->Network->Domain->sld;
		
		return $network;
	}

	private function _get_location() {
		$location = new Geolocation_Location;
		$location->continent = (string) $this->_xml->Location->continent;
		$location->country = (string) $this->_xml->Location->CountryData->country;
		$location->country_code = (string) $this->_xml->Location->CountryData->country_code;
		$location->country_cf = (string) $this->_xml->Location->CountryData->country_cf;
		$location->region = (string) $this->_xml->Location->region;
		$location->state = (string) $this->_xml->Location->StateData->state;
		$location->state_code = (string) $this->_xml->Location->StateData->state_code;
		$location->state_cf = (string) $this->_xml->Location->StateData->state_cf;
		$location->dma = (string) $this->_xml->Location->dma;
		$location->msa = (string) $this->_xml->Location->msa;
		$location->city = (string) $this->_xml->Location->CityData->city;
		$location->city_cf = (string) $this->_xml->Location->CityData->city_cf;
		$location->postal_code = (string) $this->_xml->Location->CityData->postal_code;
		$timezone = (string) $this->_xml->Location->CityData->time_zone;
		$location->time_zone = 'GMT' . ($timezone > 0 ? '+' . $timezone : ($timezone < 0 ? '-' . $timezone : NULL)) ;
		$location->area_code = (string) $this->_xml->Location->CityData->area_code;
		$location->logitude = (string) $this->_xml->Location->longitude;
		$location->latitude = (string) $this->_xml->Location->latitude;
		
		return $location;
	}
	
	public function __get($name) {
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		}
		
		throw new Geolocation_Exception('Property :class::$:property does not exist',array(
			':property' => $name,
			':class'    => __CLASS__,
		)); 
	}
	
	public function __ToString() {
		return json_encode($this->_data);
	}
}