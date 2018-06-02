<?php
class Apptha_Autosuggestion_Helper_Data extends Mage_Core_Helper_Abstract{
    
	public function getSuggestUrl()
    {
        return $this->_getUrl('autosuggestion', array(
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }
    
	/*
	 * Get search box default text
	 *
	 * @return String
	 */
    public function getSearchBoxDefaultText(){
    	$config = Mage::getStoreConfig('autosuggestion/custom_group');
    	return $config["autosuggestion_default_text"];
    }

	/*
	 * Get Category collection
	 *
	 * @param query string $query
	 * @return array
	 */
	public function getCategoryCollectionByQueryString($query){
		$explode = explode(" ", $query["query"]);
		$likeQuery = $this->getLikeQuery($explode);
		/*$filter_a = array('like'=>$explode[0].'%');
        $filter_b = array('like'=>$explode[1].'%');
		echo(
        (string)Mage::getModel('catalog/category')->getCollection()
						->addFieldToFilter('name',array($filter_a,$filter_b))
						->getSelect()
        );
		die;*/
        $allCats = Mage::getResourceModel('catalog/category_collection')
						->addAttributeToSelect('*')
						->addAttributeToFilter('is_active','1')
						->addAttributeToFilter('include_in_menu','1')
						->addFieldToFilter('name',array($likeQuery))
						->addAttributeToSort('relevance');

	  	return $allCats;
	}

	/*
	 *Get brand collection
	 *
	 * @param query string $query
	 * @return array
	 */
	public function getBrandCollectionByQueryString($query){
		$explode = explode(" ", $query["query"]);
		$likeQuery = $this->getLikeQuery($explode);
		//Option value Table
		$tbl_faq_item = Mage::getSingleton('core/resource')->getTableName('eav_attribute_option_value');

		//Getting brands attribute code and entity Value from configuration
		$config    = Mage::getModel('eav/config');
		$attribute = $config->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'brands');

		//Attribute collection
		$filteredAudienceCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
		->setPositionOrder('asc')
		->setAttributeFilter($attribute->getId());
		
		//Join Attribute collection with eav_attribute_option_value table to get Attribute options values
		$filteredAudienceCollection->getSelect()->join(array('t2' => $tbl_faq_item),'main_table.option_id = t2.option_id','t2.value');
		//$filteredAudienceCollection->addFieldToFilter('value', array('like' => '%'.$query["query"].'%'));
		$filteredAudienceCollection->addFieldToFilter('value',array($likeQuery));
		$filteredAudienceCollection->setOrder('value', 'asc');
	return $filteredAudienceCollection;	
	}

	/*Store like filter into an array variable
	 *
	 *@param category_name array
	 *@return array
	 */
    public function getLikeQuery($explodedQuery){
		for($i=0; $i<=count($explodedQuery) - 1; $i++){
			$filter_loop_array[$i] = array('like'=>$explodedQuery[$i].'%');
		}
	return $filter_loop_array;
	}
	
	/*
	 *Get product collection
	 *
	 * @param query string $query
	 * @return array
	 */
    public function getProductCollectionByQueryString2($query){
		$explode = explode(" ", $query["query"]);
		$likeQuery = $this->getLikeQuery($explode);
		$model = Mage::getModel('catalog/product');
	  	$store_id = $storeId = Mage::app()->getStore()->getId();
	  	$collection = "";
	  	$count = 0;
		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
			->addFieldToFilter('name',array($likeQuery))
			->addAttributeToSort('relevance');
		$collection->setPageSize(30);
	  	return $collection;
	}
	
	/*
	 *Get product collection
	 *
	 * @param query string $query
	 * @return array
	 */
    public function getProductCollectionByQueryString($query){
    	$query = strtolower($query["query"]);
        $config = Mage::getStoreConfig('autosuggestion/custom_group');
        
        $autosuggestion_options = explode(",",$config["autosuggestion_options"]);
        
       //Connection to database
	  	$model = Mage::getModel('catalog/product');
	  	
	  	$store_id = $storeId = Mage::app()->getStore()->getId();
	  	
	  	$collection = "";
	  	
	  	$count = 0;
	  	  	
    	if( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] =="1" ) && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] =="2"  )   && ( isset($autosuggestion_options[2]) && $autosuggestion_options[2] =="3") && ( isset($autosuggestion_options[3]) && $autosuggestion_options[3] =="4") ) {
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'name','like'=>'%'.$query.'%'  ),
	                   array('attribute'=>'price','like' =>$query.'%'  ),
	                   array('attribute'=>'description','like'=>'%'.$query.'%'  ),
	                   array('attribute'=>'meta_keyword','like'=>'%'.$query.'%' )
	                )
	    	);
	    	
	  	}    
	  	
    	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '1' )  && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '2' ) && (isset($autosuggestion_options[2]) && $autosuggestion_options[2] == '3' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'name', 'like'=>'%'.$query.'%'),
	                   array('attribute'=>'price','like' =>$query.'%'  ),
	                   array('attribute'=>'description', 'like'=>'%'.$query.'%')
	                )
	    	);
			
	  	}
	  	
   	    elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '2' )  && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '3' ) && (isset($autosuggestion_options[2]) && $autosuggestion_options[2] == '4' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'price','like' =>$query.'%'  ),
	                   array('attribute'=>'description', 'like'=>'%'.$query.'%'),
	                   array('attribute'=>'meta_keyword','like'=>'%'.$query.'%' )
	                )
	    	);
	  	}
	  	
     	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '1' )  && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '3' ) && (isset($autosuggestion_options[2]) && $autosuggestion_options[2] == '4' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'name', 'like'=>'%'.$query.'%'),
	                   array('attribute'=>'description', 'like'=>'%'.$query.'%'),
	                   array('attribute'=>'meta_keyword','like'=>'%'.$query.'%' )
	                )
	    	);
	    	
	  	}
	  	
	  	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '1' )  && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '2' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'name', 'like'=>'%'.$query.'%'),
	                   array('attribute'=>'description', 'like'=>'%'.$query.'%')
	                )
	    	);
	  	}
	  	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '2') && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '3' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	                   array('attribute'=>'price', 'like'=>$query.'%'),
	                   array('attribute'=>'description', 'like'=>'%'.$query.'%')
	                )
	    	);

	  	}
	  	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '1') && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '3' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'name', 'like'=>'%'.$query.'%'),
	                    array('attribute'=>'price', 'like'=>$query.'%')
	                )
	    	);    	
	  	}
    	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '1') && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '4' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'name', 'like'=>'%'.$query.'%'),
	                    array('attribute'=>'meta_keyword', 'like'=>'%'.$query.'%')
	                )
	    	);    	
	  	}
   		elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '2') && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '4' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'price', 'like'=>'%'.$query.'%'),
	                    array('attribute'=>'meta_keyword', 'like'=>'%'.$query.'%')
	                )
	    	);    	
	  	}
    	elseif( (isset($autosuggestion_options[0]) && $autosuggestion_options[0] == '3') && (isset($autosuggestion_options[1]) && $autosuggestion_options[1] == '4' ) ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'description', 'like'=>'%'.$query.'%'),
	                    array('attribute'=>'meta_keyword', 'like'=>'%'.$query.'%')
	                )
	    	);    	
	  	}
    	elseif(isset($autosuggestion_options[0])  && $autosuggestion_options[0] =="1"   ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'name', 'like'=>'%'.$query.'%')
	                )
	    	);
	  	}
    	elseif(isset($autosuggestion_options[0]) && $autosuggestion_options[0] =="2"   ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'description', 'like'=>'%'.$query.'%')
	                )
	    	);
	  	}
    	elseif(isset($autosuggestion_options[0]) && $autosuggestion_options[0] =="3" ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				 array('attribute'=>'price', 'like'=>$query.'%')
	                )
	    	);
	  	}
   		elseif(isset($autosuggestion_options[0]) && $autosuggestion_options[0] =="4" ){
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				 array('attribute'=>'meta_keyword', 'like'=>'%'.$query.'%')
	                )
	    	);
	  	}
	  	else{
	  		$collection = $model->getCollection()
	  		->setStoreId( $store_id )
	    	->addAttributeToFilter(
	    		array(
	    				array('attribute'=>'name', 'like'=>'%'.$query.'%')
	                )
	    	);
	  	}
	  	$collection->setOrder('name', 'asc');
		$collection->setPageSize(5);
	  	return $collection;
    }
}

?>
