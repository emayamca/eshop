<?php
class Fuel_Trackyourorder_Helper_Data extends Mage_Core_Helper_Abstract{
    /**
     * Function to get sales track
     * @param int $increment_id
     * @return object
     * 
     */
    public function salesTrack($increment_id) {
        return Mage::getModel ( 'sales/order' )-load($increment_id,'increment_id');
    }
    /**
     * Function to redirect url
     * @return string
     */
    public function redirectingTo() {
        $historyUrl = Mage::getBaseUrl () . "sales/order/history";
        return Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $historyUrl );
    }
    
    /**
     * Function to get product collection
     * @return object
     */
    public function _getProductCollection($productId) {
        return Mage::getModel ( 'catalog/product' )->load ( $productId );
    }
    
   /**
    * Function to get track order key
    * @return void|string
    * 
    */
    public function TrackOrderKey() {
        $code = $this->genenrateTrackUrOrderdomain ();
        $domainKey = substr ( $code, 0, 25 ) . "CONTUS";
        $apikey = Mage::getStoreConfig ( 'trackyourorder/general/trackorder_license' );
        if ($domainKey != $apikey) {
            return base64_decode ( 'PHNwYW4+UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vd3d3LmFwcHRoYS5jb20vY2hlY2tvdXQvY2FydC9hZGQvcHJvZHVjdC8xNjAvcXR5LzEvIiB0YXJnZXQ9Il9ibGFuayIgPlRyYWNrIFlvdXIgT3JkZXI8L2E+PC9zcGFuPg==' );
        } else {
            return;
        }
    }
    /**
     * Function to get domain key
     * @param string $tkey
     * @return void
     *
     */
    public function domainKey($tkey) {
        $message = "EM-TYOMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $uKey = strlen ( $tkey );
        for($i = 0; $i < $uKey; $i ++) {
            $key_array [] = $tkey [$i];
        }
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $lenCharSet = strlen ( $chars_str );
        for($i = 0; $i < $lenCharSet; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $lenMessage = strlen ( $message );
        $cKeyArr = count ( $key_array );
        for($i = 0; $i < $lenMessage; $i ++) {
            $char = substr ( $message, $i, 1 );
            $offset = $this->getOffset ( $key_array [$kPos], $char );
            $enc_message .= $chars_array [$offset];
            $kPos ++;
            if ($kPos >= $cKeyArr) {
                $kPos = 0;
            }
        }
        return $enc_message;
     }
    /**
     * Function to get offset
     * @param $start,$end
     * @return unknown
     * 
     */
    public function getOffset($start, $end) {
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen ( $chars_str );
        for($i = 0; $i < $strLen; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        for($i = count ( $chars_array ) - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $chars_array [$i] )] = $i;
        }
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
            $offset = count ( $chars_array ) + ($offset);
        }
        return $offset;
    }
    
    /**
     * Function to get track your order domain
     * @return void
     * 
     */
    public function genenrateTrackUrOrderdomain() {
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        if (isset ( $matches ['domain'] )) {
            $customerurl = $matches ['domain'];
        } else {
            $customerurl = "";
        }
        $customerurl = str_replace ( "www.", "", $customerurl );
        $customerurl = str_replace ( ".", "D", $customerurl );
        $customerurl = strtoupper ( $customerurl );
        if (isset ( $matches ['domain'] )) {
            $response = $this->domainKey ( $customerurl );
        } else {
            $response = "";
        }
        return $response;
    }
	
	/*
	 * Product Collections
	 *
	 * @return array
	 */
	 public function geteshopCollections(){
		$storeId = Mage::app ()->getStore ()->getId ();
		$visibility = array (
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG 
            );
        $_product = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
						'name',
						'price',
						'compatibility',
						'small_image' 
					) )
					->setStoreId ( $storeId )->addStoreFilter ( $storeId )
					->addAttributeToFilter ( 'visibility', $visibility )
					->addAttributeToFilter ( 'status', array ('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));

		//Remove Out of stock products on the product collections
		$configValueStockStatus = Mage::getStoreConfig ( 'cataloginventory/options/show_out_of_stock', $storeId );
        if ($configValueStockStatus == 0) {
            Mage::getSingleton ( 'cataloginventory/stock' )->addInStockFilterToCollection ( $_product );
        }
		
		return $_product;
	 }

	/*
	 * Get productIds based on the parameter strings
	 *
	 * @return array
	 */	
	public function getcompatabilitySearchCollections($arrayPostDatas){
		$explode = explode(" ", $arrayPostDatas);
		$model = Mage::getModel('catalog/product');
		$store_id = Mage::app()->getStore()->getId();
		$collection = "";
		$count = 0;
		if(count($explode) > 1){
			$likeQuery = Mage::helper('autosuggestion')->getLikeQuery($explode);
			$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect ( '*' )->addFieldToFilter('name',array($likeQuery))
				->addAttributeToSort('relevance');
			$collection->setPageSize(10);
		} elseif($arrayPostDatas) {
			$collection = $this->geteshopCollections()->addAttributeToFilter(array(array('attribute'=>'name', 'like'=>'%'.$arrayPostDatas.'%')))
				->addAttributeToSort('relevance');
			$collection->setPageSize(10);
		}
		return $collection;
	}

	/*
	 * Get product collections based on the product Ids
	 *
	 * @return array
	 */	
	public function getcompatabilityCollectionsByIds($productId){
		$_product = Mage::helper('trackyourorder')->geteshopCollections();
		$_product->addAttributeToFilter('entity_id', array('in' => $productId));
		$_product->setOrder ( 'name', 'asc' );
		$_product->setPageSize ( 10 );
		return $_product;
	}
	
	/**
     * Calculate average ratings
     * 
     * @param integer $productId
     * @return number
     */
    public function averageRatings($productId) {      
        // REVIEW COLLECTION
        $reviews = Mage::getModel ('review/review')->getResourceCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addEntityFilter ( 'product', $productId )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->setDateOrder ()->addRateVotes ();
        // RATINGS
        $ratings = array ();
        $avg = 0;
        if (count ( $reviews ) > 0) {
            foreach ( $reviews->getItems () as $review ) {
                foreach ( $review->getRatingVotes () as $vote ) {
                    $ratings [] = $vote->getPercent ();
                }
            }
            $count = count ( $ratings );
            $avg = array_sum ( $ratings ) / $count;
        }
        return round ( $avg, 1 );
    }
    
    /**
     * Function to get the Total Ratings of an Product
     * @param integer $productId
     * @return array
     */
    public function totalRatings($productId) {
        $reviews = Mage::getModel ('review/review')->getResourceCollection ()->addStoreFilter ( Mage::app ()->getStore ()->getId () )->addEntityFilter ( 'product', $productId )->addStatusFilter ( Mage_Review_Model_Review::STATUS_APPROVED )->setDateOrder ()->addRateVotes ();
        // RATINGS
        $ratings = array ();
        if (count ( $reviews ) > 0) {
            foreach ( $reviews->getItems () as $review ) {
                foreach ( $review->getRatingVotes () as $vote ) {
                    $ratings [] = $vote->getPercent ();
                }
            }
        }
        return $ratings;
    }
	
	/**
     * Function to get Rating star title
     * @param integer $value
     * @return string
     */
	public function rateTitle($value){
		switch($value){
			case 1:
				$title = "I hate it";
				break;
			case 2:
				$title = "I don't like it";
				break;
			case 3:
				$title = "It's okay";
				break;
			case 4:
				$title = "I like it";
				break;
			case 5:
				$title = "I love it";
				break;
			default:
				$title = "Rate this $value star out of 5";
				break;
		}
	return $title;
	}
	/**
     * Function to Check whether a loggedin or newly registered user verified his Mobile number
	 *
     * @return boolean
     */
	public function checkMobileNumber(){
		if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $customerMobile = $customerData->getMobile ();
            $customerEmail = $customerData->getEmail ();
			if($customerMobile == '' || $customerEmail == ''){
				return false;
			}
        }
		return true;
	}
}