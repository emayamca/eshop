<?php
class Apptha_Autosuggestion_IndexController extends Mage_Core_Controller_Front_Action{
	
    public function IndexAction() {
    		
    	$config = Mage::getStoreConfig('autosuggestion/custom_group');
    	$autoSearchBoxSettings = explode(",",$config["autosuggestion_searchbox_settings"]);
    	$query = $this->getRequest()->getParams("query");
    	
    	$collection =  Mage::helper('autosuggestion')->getProductCollectionByQueryString($query);
    	$collectionCat = Mage::helper('autosuggestion')->getCategoryCollectionByQueryString($query);
		$collectionBrand = Mage::helper('autosuggestion')->getBrandCollectionByQueryString($query);
    	$i=0; 
		$j=0;
    	$model = Mage::getModel('catalog/product');
    	$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    	$currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol(); 
		
		//Based on brands and Categories suggestions
		if(count($collection) > 0){
			$pname[$i] = '<h4 class="suggestion-title">Products</h4>';
		} else {
			$pname[$i] = '<h4></h4>';
		}
		
		//Products
		foreach ($collection as $product) //loop for getting products
		{
		    $model->load($product->getId());
		    $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
		    $productName = $model->getName();
		    $data[$i] = $model->getName();
		    $imageUrlOriginal =  $model->getImage();
		    
		    $productUrl = $product->getProductUrl();
		    $imageUrl  = $product->getImageUrl();
		    
		   	
		    if( ($config["autosuggestion_in_outstock"] == "1" && $stock->getQty() >= 1 && $product->isSaleable() == 1)  ){
		   
					
			    	$pname[$i] .= "<div id='auto-search'>";
				    if($config["autosuggestion_by_link"] == "1"){
				    	$pname[$i] .= "<a href='$productUrl'>";
				    }
				    
			    	if(isset($autoSearchBoxSettings[1])  &&  $autoSearchBoxSettings[1] == "1"){
			    		if($imageUrlOriginal == "no_selection")
			    			$pname[$i] .= "<img src='$imageUrl' height='50' width='50' align='left' class='autosearch-img'/>";
			    		else 
			    			$pname[$i] .= "<img src='".Mage::getBaseUrl('media')."catalog/product/".$imageUrlOriginal."' height='50' width='50' align='left' class='autosearch-img'/>";
			    	}
			    	
			    	if(isset($autoSearchBoxSettings[0])  &&  $autoSearchBoxSettings[0] == "4"){
			    		
				    	
							$pname[$i] .= "<font style='white-space: normal;font-weight: bold;'><!---->".$productName."<!---->";
				    	
			    	}
					
					if(isset($autoSearchBoxSettings[3])  &&  $autoSearchBoxSettings[3] == "3")
						$pname[$i] .=  ' - <span class="autosearch-price">'.$currencySymbol.number_format($model->getPrice(),2).'</span></font>';
						
					if(isset($autoSearchBoxSettings[2])  &&  $autoSearchBoxSettings[2] == "2")
						$pname[$i] .= "<br/><font style='white-space: normal;font-weight: normal;'>".$model->getShortDescription().'</font>';
						
						
					$pname[$i] .="";
					
				    if($config["autosuggestion_by_link"] == "1"){   
						$pname[$i] .= "</a>";
				    }
				    $i++;
			   	$pname[$i] .= "</div>";
		    
		    }
                    elseif($config["autosuggestion_in_outstock"] == "2"){

			    	$pname[$i] .= "<div id='auto-search'>";
				    if($config["autosuggestion_by_link"] == "1"){
				    	$pname[$i] .= "<a href='$productUrl'>";
				    }

			    	if(isset($autoSearchBoxSettings[1])  &&  $autoSearchBoxSettings[1] == "1"){
			    		if($imageUrlOriginal == "no_selection")
			    			$pname[$i] .= "<img src='$imageUrl' height='50' width='50' align='left' style='padding: 0 7px 2px 0;'class='autosearch-img'  />";
			    		else
			    			$pname[$i] .= "<img src='".Mage::getBaseUrl('media')."catalog/product/".$imageUrlOriginal."' height='50' width='50' align='left'class='autosearch-img' />";
			    	}

			    	if(isset($autoSearchBoxSettings[0])  &&  $autoSearchBoxSettings[0] == "4"){

				    	
				    		$pname[$i] .= "<font style='white-space: normal;font-weight: bold;'><!---->".$productName."<!---->";
				    	
			    	}

					if(isset($autoSearchBoxSettings[3])  &&  $autoSearchBoxSettings[3] == "3")
						$pname[$i] .=  " - <span class='autosearch-price'>".$currencySymbol.number_format($model->getPrice(),2).'</span></font>';

					if(isset($autoSearchBoxSettings[2])  &&  $autoSearchBoxSettings[2] == "2")
						$pname[$i] .= "<br/><font style='white-space: normal;font-weight: normal;'>".$model->getShortDescription().'</font>';
						

					$pname[$i] .="";

				    if($config["autosuggestion_by_link"] == "1"){
						$pname[$i] .= "</a>";
				    }
				    $i++;
$pname[$i] .= "</div>";
                    }
		}

		//Brands
		if(count($collectionBrand) > 0){
			//Based on Categories suggestion 
			if(count($collection) > 0){
				$pname[$i] .= "<h4 class='suggestion-title'>Brands</h4>";
			} else {
				$pname[$i] = "<h4 class='suggestion-title'>Brands</h4>";
			}
			foreach($collectionBrand as $collectionBrands){ //loop for getting categories
				$pname[$i] .= '<div id="auto-search">';
				$pname[$i] .= '<a href="'.Mage::getBaseUrl().'by-brands.html?brands='.$collectionBrands->getOptionId().'">';
				if(isset($autoSearchBoxSettings[1])  &&  $autoSearchBoxSettings[1] == "1"){
					$pname[$i] .= "<img src='".$collectionBrands->getthumb()."' height='50' width='50' align='left' class='autosearch-img'/>";
				}
				$pname[$i] .= '<font style="white-space: normal;font-weight: bold;"><!---->'.$collectionBrands->getValue().'<!----></a>';
				$pname[$i] .= '</div>';
				$i++;
			}
		}

		//Categories
		if(count($collectionCat) > 0){
			//Category title based on product collection and brand collection
			if((count($collectionCat) > 0 || count($collectionBrand) > 0) && count($collection) > 0){
				$pname[$i] .= '<h4 class="suggestion-title">Categories</h4>';
			} elseif(count($collectionCat) > 0){
				$pname[$i] = '<h4 class="suggestion-title">Categories</h4>';
			} else {
				$pname[$i] = '<h4></h4>';
			}
			foreach($collectionCat as $collectionCats){ //loop for getting categories
				$pname[$i] .= '<div id="auto-search">';
				$pname[$i] .= '<a href="'.$collectionCats->getUrl($collectionCats).'" id="cate'.$collectionCats->getId() .'">';
				if(isset($autoSearchBoxSettings[1])  &&  $autoSearchBoxSettings[1] == "1"){				
					if($collectionCats->getThumbnail()){
						$pname[$i] .= "<img src='".Mage::getBaseUrl('media').'catalog/category/' . $collectionCats->getThumbnail()."' height='50' width='50' align='left' class='autosearch-img'/>";
					}
				}
				$pname[$i] .= '<font style="white-space: normal;font-weight: bold;"><!---->'.$collectionCats->getName().'<!----></a>';
				$pname[$i] .= '</div>';
				$i++;
			}
		}
		
			
		if($i==0 && $j==0){
			$pname[$i] = "No results found";
		}	
		$responseData["query"] = $query["query"];
		$responseData["suggestions"] = $pname;
		$responseData["data"] =  $pname;	
		echo json_encode($responseData);
	  	die;
	  	$this->loadLayout();   
	    $this->renderLayout(); 
    }
    
}
