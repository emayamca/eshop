<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Sociallogin
 */
/**
 * This Helper file helps to includes the License key option for social login extension
 */
class Fuel_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Is sand box mode ?
     *
     * @return boolean
     */
    public function isSandBox() {
        return Mage::getStoreConfigFlag ( 'sociallogin/paypal/sandbox_mode' );
    }
    /**
     * Get sandbox or not paypal Client Id from system config.
     *
     * @return string
     */
    public function getPaypalClientId() {
        if ($this->isSandBox ()) {
            return Mage::getStoreConfig ( 'sociallogin/paypal/sandbox_client_id' );
        } else {
            return Mage::getStoreConfig ( 'sociallogin/paypal/client_id' );
        }
    }
    /**
     * Get sandbox or not paypal Secret Key from system config.
     *
     * @return string
     */
    public function getPaypalSecretKey() {
        if ($this->isSandBox ()) {
            return Mage::getStoreConfig ( 'sociallogin/paypal/sandbox_secret' );
        } else {
            return Mage::getStoreConfig ( 'sociallogin/paypal/secret' );
        }
    }
    /**
     * Get sandbox or not paypal endpoint from system config.
     *
     * @return string
     */
    public function getPaypalEndpoint() {
        if ($this->isSandBox ()) {
            return Mage::getStoreConfig ( 'sociallogin/paypal/sandbox_endpoint' );
        } else {
            return Mage::getStoreConfig ( 'sociallogin/paypal/endpoint' );
        }
    }
	
	/*
	 * Product counts for Header menus
	 */
	public function getAttributeProductCount($attributeCode, $value){
		return Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect("*")
					->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
					->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
					->addAttributeToFilter("$attributeCode", array('eq' => $value));
	}
	
	/*
	 * Header menu
	 *
	 * The header menu's are drop down attributes machine type, brands and category_type
	 */
	public 	function headerAttributeMenu($attributeCode, $url, $loop){
		$mhtml = "";
		$attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
		$attributeOptions = $attribute->getSource()->getAllOptions();
		$flag = 1;
			foreach($attributeOptions as $each){
				if($each['value']){
					$mhtml .= "<li><a href='".Mage::getBaseUrl().''.$url.'='.$each['value']."'>".$each['label']."</a></li>";	
				}
				if($attributeCode == 'brands' && $flag >= 40) { break; }
				$flag++;
			}
		return $mhtml;
	}
	
	/*
	 * Header menu without Limit - Brands
	 *
	 * The header menu's are drop down attributes machine type, brands and category_type
	 */
	public 	function brandAttributePop($attributeCode, $url, $loop){
		$mhtml = "";
		$attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
		$attributeOptions = $attribute->getSource()->getAllOptions();
		$mhtml .= "<ul class='brand-loop-alive ".$loop."'>";
			foreach($attributeOptions as $each){
				if($each['value']){
					$mhtml .= "<li><a href='".Mage::getBaseUrl().''.$url.'='.$each['value']."'>".$each['label']."</a></li>";	
				}	
			}
		$mhtml .= "</ul>";
		return $mhtml;
	}
	
	/*
	 * Top Categories section
	 */
	public function getTreeCategories($parentId, $isChild, $categoryIds)
	{
		$html = "";
		$allCats = Mage::getModel('catalog/category')->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToFilter('is_active','1')
					->addAttributeToFilter('include_in_menu','1')
					->addAttributeToFilter('parent_id',array('eq' => $parentId))
					->addAttributeToSort('position', 'asc');

		$class = ($isChild) ? "sub-cat-list" : "cat-list";
		$html .= '<ul class="'.$class.'">'; 
		$flag = 1;
		foreach($allCats as $category)
		{                 
			if($this->_hasProducts($category->getId())){
				$categoryCount = Mage::getModel('catalog/category')->getCollection()
									->addAttributeToFilter('is_active','1')
									->addAttributeToFilter('parent_id',array('eq' => $category->getId()));
				if($flag <= 7){
					$html .= '<li><a href="'.$category->getUrl($category).'" id="cate'.$category->getId() .'">'.$category->getName()."<span class='prod-count'>(".Mage::getModel("catalog/category")->load($category->getId())->getProductCount().")</span></a>";
				} else {
					$html .= '<li class="more-item"><a href="'.$category->getUrl($category).'" id="cate'.$category->getId() .'">'.$category->getName()."<span class='prod-count'>(".Mage::getModel("catalog/category")->load($category->getId())->getProductCount().")</span></a>";
				}
				$subcats = $category->getChildren();
					if($subcats != '')
					{
						$html .= '<ul id="cate' . $category->getId() . 'sub_category">';
						$html .= $this->getTreeCategories($category->getId(), true, $categoryIds);
						$html .= '</ul>';                               
					}
				$html .= '</li>';
				$flag++;
			}
		}
		$html .= '</ul>';
	return $html;
	}  
	
	/*
	 * Get Status for category products
	 */
	public function _hasProducts($category_id) {
		$products = Mage::getModel('catalog/category')->load($category_id)
			->getProductCollection()
			->addAttributeToSelect('entity_id')
			->addAttributeToFilter('status', 1)
			->addAttributeToFilter('visibility', 4);
		return ( $products->count() > 0 )  ? true : false;
	}
	/*
	 * Get Sub Categories
	 */
	public function getSubCategories($childrenCat){
		$model_category = Mage::getModel('catalog/category');
		$sub_categories = $model_category->getCollection();
		$sub_categories->addAttributeToSelect('entity_id')
							->addAttributeToFilter('is_active',1)
							->addAttributeToFilter('include_in_menu','1')
							->addIdFilter($childrenCat)
							->setOrder('position', 'ASC')
							->load();
		return $sub_categories;
	}
	
	/*
	 * Get Sub Categories
	 */
	public function getHeadCatCollection(){
		$allCatsCount = Mage::getModel('catalog/category')->getCollection()
						->addAttributeToSelect('*')
						->addAttributeToFilter('is_active','1')
						->addAttributeToFilter('include_in_menu','1')
						->addAttributeToFilter('parent_id',array('eq' => 2))
						->addAttributeToSort('position', 'asc');
		return $allCatsCount;
	}

	/*
	 * Child category HTML
	 */
    public function getChildrenHtml($_category, $isChild, $flag, $accordion){
		if($this->_hasProducts($_category->getId())){
			$children = explode( ",", $_category->getChildren() );
			$sub_categories = $this->getSubCategories($_category->getChildren());

			$content = '';
			if($isChild){
				$content .= '<li class="';
				$content .= 'panel panel-default';

				$content .= '">';
			} else {
				$content .= '<div class="';
				$content .= 'panel panel-default';

				$content .= '">';
			}
			$class = '';
			if(!$isChild){
				$class = "mainCat";
				$content .= '<div class="panel-heading">';
				$content .= '<h6 class="panel-title">';
			}
			$content .= '<a href="'.$_category->getUrl($_category).'" class='.$class.'';
			$content .= '>';
			//Displayed category icons only for parent categories
			if($_category->getLevel() == 2){
				$content .= '<img src="'.Mage::getBaseUrl('media').'catalog/category/icons/' . $_category->getUrlKey().'.png" height="25" width="25" alt="'.$_category->getName().'" class="autosearch-img"/>';
			}
			$content .= $_category->getName().'<span class="prod-count">('.Mage::getModel("catalog/category")->load($_category->getId())->getProductCount().')</span></a>';
			if($children[0]){
				$content .= '<a data-toggle="collapse" data-parent="#accordion'.$accordion.'" href="#collapse'.$flag.'" class="toggleSwitch"><i class="fa fa-bars"></i></a>';
			}
			else{
				$content .= '<a data-toggle="collapse" data-parent="#accordion'.$accordion.'" href="#collapse'.$flag.'" class="toggleSwitch">&nbsp;</a>';
			}
			if(!$isChild){
				$content .= '</h6>';
				$content .= '</div>';
			}
			if($children[0]){
				$content .='<div id="collapse'.$flag.'" class="panel-collapse collapse">';
				$content .='<div class="panel-body">';
				$content .= '<ul>';
				foreach($sub_categories->getData() as $child){
					$_subcat = Mage::getModel( 'catalog/category' )->load( $child['entity_id'] );
					$content .= $this->getChildrenHtml($_subcat, true, $flag, $accordion);
				}
				$content .= '</ul>';
				$content .= '</div>';
				$content .= '</div>';
			}
			if(!$isChild){
			$content .= '</div>';
			} else {
				$content .= '</li>';
			}
		}
        return $content;
    }
	/*
	 * Child category HTML for SEO
	 */
    public function getSeoChildrenHtml($_category, $isChild, $flag, $accordion){
		if($this->_hasProducts($_category->getId())){
			$children = explode( ",", $_category->getChildren() );
			$sub_categories = $this->getSubCategories($_category->getChildren());

			$content = '';
			if($isChild){
				$content .= '<li class="';
				$content .= 'panel panel-default';

				$content .= '">';
			} else {
				$content .= '<div class="';
				$content .= 'panel panel-default';

				$content .= '">';
			}
			$class = '';
			if(!$isChild){
				$class = "mainCat";
				$content .= '<div class="panel-heading">';
				$content .= '<h6 class="panel-title">';
			}
			$content .= '<a href="'.$_category->getUrl().'" class='.$class.'';
			$content .= '>'.$_category->getName().'<span class="prod-count">('.Mage::getModel("catalog/category")->load($_category->getId())->getProductCount().')</span></a>';
			if($children[0]){
				$content .= '<a data-toggle="collapse" data-parent="#accordionn'.$accordion.'" href="#collapsee'.$flag.'" class="toggleSwitch"><i class="fa fa-bars"></i></a>';
			}
			else{
				$content .= '<a data-toggle="collapse" data-parent="#accordionn'.$accordion.'" href="#collapsee'.$flag.'" class="toggleSwitch">&nbsp;</a>';
			}
			if(!$isChild){
				$content .= '</h6>';
				$content .= '</div>';
			}
			if($children[0]){
				$content .='<div id="collapsee'.$flag.'" class="panel-collapse collapse">';
				$content .='<div class="panel-body">';
				$content .= '<ul>';
				foreach($sub_categories->getData() as $child){
					$_subcat = Mage::getModel( 'catalog/category' )->load( $child['entity_id'] );
					$content .= $this->getChildrenHtml($_subcat, true, $flag, $accordion);
				}
				$content .= '</ul>';
				$content .= '</div>';
				$content .= '</div>';
			}
			if(!$isChild){
			$content .= '</div>';
			} else {
				$content .= '</li>';
			}
		}
        return $content;
    }
	/*
	 * Generate OTP
	 * 
	 * @param $length integer
	 * @param $chars varchar
	 *
	 * @return varchar
	 */
	public function generateOTP($length = 4, $chars = '0123456789'){
        $chars_length = (strlen($chars) - 1);
        $string = $chars{rand(0, $chars_length)};
        for ($i = 1; $i < $length; $i = strlen($string)){
            $r = $chars{rand(0, $chars_length)};
            if ($r != $string{$i - 1}) $string .=  $r;
        }
        return $string;
    }
	/*
	 * Sent Message to New Registered User
	 *
	 * @param $mobileno integer
	 * @param $textmessage varchar
	 *
	 * @return integer
	 */
	public function CURLsendsms($mobileno, $form_id){
		//OTP Generate
		$OTP = $this->generateOTP();
		if($form_id == "register"){
			Mage::getSingleton('core/session')->setOTP($OTP);
		} else if($form_id == "dashboard"){
			Mage::getSingleton('core/session')->setCOTP($OTP);
		} else {
			Mage::getSingleton('core/session')->setCHOTP($OTP);
		}
		//Mage::log($OTP, null, 'mylogfile.log');
		$textmessage = urlencode("Your OTP for metacrust.com is ".$OTP);
		// SMS API
		$url = "http://login.smsadda.com/API/pushsms.aspx?loginID=businessfuel&password=business123&mobile=$mobileno&text=$textmessage&senderid=BIZFUL&route_id=1&Unicode=1";

		/*CURL Starts Here*/
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$ee       = curl_getinfo($ch);
		curl_close($ch);
        /*CURL Ends Here*/

		return $status;
    }
	/*
	 * Send verification code
	 *
	 * @param $email varchar
	 * @param $textmessage varchar
	 *
	 * @return integer
	 */
	public function CURLsendemail($post){
		//VCODE Generate
		$VCODE = $this->generateOTP();
		$translate = Mage::getSingleton('core/translate');
		/* @var $translate Mage_Core_Model_Translate */
		$translate->setTranslateInline(false);
		$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( Mage::getStoreConfig('sociallogin/emailverification/template') );
		$emailTemplate->setSenderName ( Mage::getStoreConfig('trans_email/ident_general/name') );
		$emailTemplate->setSenderEmail ( Mage::getStoreConfig('trans_email/ident_general/email') );
		$emailTemplate->setDesignConfig ( array (
			'area' => 'frontend' 
		) );
		$emailTemplate->getProcessedTemplate ( $post );
		$toEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$toName = Mage::getStoreConfig('trans_email/ident_general/name');
		$post['verificationcode'] = $VCODE;
		try {
			$emailTemplate->send ( $toEmail, $toName, $post );
			Mage::getSingleton('core/session')->setVCODE($VCODE);
			$return = true;
		} catch (Exception $e) {
			$translate->setTranslateInline(true);
			$return =  false;
		}

		return $return;
    }
	
	public function getHostDomain(){
		if(Mage::getStoreConfig('sociallogin/hostsetting/configurations') == 1){
			$domain = 'www.localhost.com';
		} elseif (Mage::getStoreConfig('sociallogin/hostsetting/configurations') == 2){
			$domain = '.metacrust.com';
		} else {
			$domain = '.metacrust.com';
		}
	return $domain;
	}
}