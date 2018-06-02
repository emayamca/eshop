<?php

if(!function_exists('geoip_country_code_by_name') && Mage::helper("geoip")->getConfig('general/apache_or_file') == 1){   
    define('GEOIP_LOCAL',1);
    $geoIpInc = Mage::getBaseDir('lib').DS.'Juicy'.DS.'Geoip'.DS.'geoip.inc';
    include($geoIpInc);
}

class Juicy_Geoip_Model_Geoip
{       
    public function runGeoip(){
        
        $countryCode = $this->_getCountryCode();
        if(empty($countryCode)){
            Mage::log("Country code returned empty. Please ensure you have at least one GeoIP method installed/enabled", null, "juicy_geoip.log");
        }
        $pairArr = $this->_getPairArray();
        foreach($pairArr as $searchArr){
            if(in_array($countryCode, $searchArr)){
                $this->_setCurrency($searchArr);
                //This method returns the redirect for store if it needs one  
                //@TODO Make this a bit nicer
                return $this->_setStore($searchArr);
            }
        }
    }
	/*
	 * Custom code to get Country code based on IP Address
	 */
	public function getCountry(){
		return $this->_getCountryCode();
	}
    protected function _setCurrency($searchArr)
    {
        if(Mage::helper('geoip')->canSwitch("currency")){
            Mage::app()->getStore()->setCurrentCurrencyCode(next($searchArr));
        }
    }
    protected function _setStore($searchArr)
    {
        if(Mage::helper('geoip')->canSwitch("store")){
            $storeCode = Mage::app()->getStore($searchArr['store'])->getCode();
            if ($storeCode) {
                $store = Mage::getModel('core/store')->load($storeCode);
                if ($store->getName() != Mage::app()->getStore()->getName()) {
                    //Needs to return store URL for observer to redirect using event
                    return $store->getCurrentUrl(false);
                }
            }
        }
    }
    protected function _getCountryCode()
    {
        if(Mage::helper('geoip')->enableTestMode()){
            $overrideCountry = Mage::helper('geoip')->testOverrideCountry();
            if(!empty($overrideCountry)){
                return $overrideCountry;
            }
        }            
        return $this->_getCountryCodeFromIp($this->_getIp());
    }
    protected function _getPairArray()
    {
        return unserialize(Mage::getStoreConfig('geoip/geoipset/ippair', Mage::app()->getStore()));
    }
    
    protected function _getIp(){
        //Using Mage HTTP helper because Varnish can confuse PHP method
        Mage::helper('geoip')->switchRemoteHeaders();
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    //UNUSED
    protected function _getModGeoIp(){
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $mod_geoip = in_array('mod_geoip', $modules);
        } else {
            $mod_geoip =  getenv('HTTP_MOD_GEOIP')=='On' ? true : false ;
        }
        return $mod_geoip;
    }
    
    protected function _getCountryCodeFromIp($ip){    
        //GeoIP .dat file
        $file = Mage::getBaseDir().DS.'lib'.DS.'Juicy'.DS.'Geoip'.DS.'Data'.DS.Mage::helper("geoip")->getConfig('general/file_location');
        try{
            if(file_exists($file)){
                if(defined('GEOIP_LOCAL')){
                    $gi=geoip_open($file,GEOIP_STANDARD);
                    $location = geoip_country_code_by_addr($gi, $ip);
                    geoip_close($gi);
                    return $location;
                }else{
                    Mage::log(".dat file detected, but you haven't enabled the option to use it ('Use my own GeoIP file' in config)", null, "juicy_geoip.log");
                }
            }else{
                return geoip_country_code_by_name($ip);
            }
        }catch(Exception $e){
            Mage::log("Warning: Could not find GeoIP Country Code. Please check your GeoIP data - Have you included a geoip.dat file?", null, "juicy_geoip.log");
            Mage::log($e->getMessage(), null, "juicy_geoip.log");
            return Mage::getStoreConfig('general/country/default');
        }
    }
}

