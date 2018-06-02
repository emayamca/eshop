<?php
class Fuel_Trackyourorder_IndexController extends Mage_Core_Controller_Front_Action {
    /**
     * Initiates the Fuel product track
     * @return void
     */
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Ajax action for tracking orders
     * @return void
     */
    public function trackordersummaryAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function to get tracking summary
     * @return void
     */
    public function trackSummaryAjaxAction() {
        $block = $this->getLayout ()->createBlock ( 'trackyourorder/trackyourorderajax' )->setTemplate ( 'trackyourorder/trackyourordersummaryajax.phtml' );
        $summaryContent = $block->renderView ();
        $this->getResponse ()->setBody ( $summaryContent );
    }
	/**
     * Brands listing page
	 *
     * @return array
     */
    public function brandsAction() {
		$this->loadLayout ();
        $this->renderLayout ();
    }
	 /**
     * New-Equipment Releated Product Collections
	 *
	 * @post product name
     * @return HTML
     */
    public function compatabilityAction() {
		$productBlock = $this->getLayout()->createBlock('catalog/product_price');
		$ajaxData = $this->getRequest()->getPost('param');
		$_product = Mage::helper('trackyourorder')->getcompatabilitySearchCollections(trim($ajaxData));

		//Check product collection is not empty and create HTML here
		if(count($_product) > 0 && ($_product != '')){
			$count = 1;
			foreach($_product as $_products){
				$name = 'name'.$count;
				$product_url = 'product_url'.$count;
				$image = 'image'.$count;
				$price = 'price'.$count;
				$_productData[$name] = $_products->getName();
				$_productData[$product_url] = $_products->getProductUrl();
				$_productData[$image] = $_products->getImageUrl();
				$_productData[$price] = Mage::helper('core')->currency($_products->getPrice(), true, false);
			
				$count++;
			}
			$_productData['count'] = count($_product);
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($_productData) );
		return;
    }
	
	/**
     * For Rentnsell
	 *
     * @return array
     */
    public function rentnsellAction() {
		$productBlock = $this->getLayout()->createBlock('catalog/product_price');
		$_product = Mage::getBlockSingleton('sociallogin/bestseller')->bestSellerCollection();

		//Best selling products HTML
		$count = 1;
		foreach($_product as $_products){
			$name = 'name'.$count;
			$product_url = 'product_url'.$count;
			$image = 'image'.$count;
			$price = 'price'.$count;
			$_productData[$name] = $_products->getName();
			$_productData[$product_url] = $_products->getProductUrl();
			$_productData[$image] = $_products->getImageUrl();
			$_productData[$price] = Mage::helper('core')->currency($_products->getPrice(), true, false);
        
			$count++;
		}
		$_productData['count'] = count($_product);
		$this->getResponse ()->setBody ( Zend_Json::encode ($_productData) );
		return;
    }
	/**
     * For ESHOP, New-Equipment and Rentnsell
	 * Validate mobile number is already registered in the website
	 *
     * @return HTML
     */
	public function mobileregistrationAction(){
		$mobile = Mage::app()->getRequest()->getPost();
		$validationArray = array();
		$mobileHtml = "";
		$customerCollection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $mobile['mobile']));
		if(count($customerCollection) == 1){
			$validationArray['status'] = 0;
			$mobileHtml .= '<span style="color: red;">Your mobile number already registered</span>';
		} else {
			$validationArray['status'] = 1;
			$mobileHtml .= '<div class="success-mble-section">';
			$mobileHtml .= '<span>Thank You </span>';
			$mobileHtml .= '<a onclick="mobileSignUp()">"'.$this->__('Click here to complete your registration').'"</a>';
			$mobileHtml .= '</div>';
		}
		$validationArray['html'] = $mobileHtml;
		$this->getResponse ()->setBody ( Zend_Json::encode ($validationArray) );
		return;
	}
	
	/*
	 * Ajax category pop-up
	 *
	 * @return HTML
	 */
	public function getPopUpCatAjaxAction(){
		//Get Root Categories
		$_categories = Mage::helper ( 'sociallogin' )->getHeadCatCollection();
		//Flags
		$flag = 1; 
		$accordion = 1;
		$popUpCatAjax = '';
		//Categories loop
		foreach ($_categories as $_category){
			if(Mage::helper ( 'sociallogin' )->_hasProducts($_category->getId())){
				//separate column based on the flag
				if($flag == 1 || $flag == 8 || $flag == 15 || $flag == 21) {
					$popUpCatAjax .= '<div class="col-md-4 pop-up-cat-ajax"><br>';
					$popUpCatAjax .= '<div class="panel-group" id="accordion'.$accordion.'" style="width:100%;">';
				}
				//Display only active categories
				if($_category->getIsActive()){
					$popUpCatAjax .= Mage::helper ( 'sociallogin' )->getChildrenHtml($_category, false, $flag, $accordion);
				}
				$flag++;
				//separate column based on the flag
				if($flag == 1 || $flag == 8 || $flag == 15 || $flag == 21) { 
					$popUpCatAjax .= '</div>';
					$popUpCatAjax .= '</div>';
					$accordion++; 
				}
			}
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($popUpCatAjax) );
		return;
	}
	
	/*
	 * Top Categories section
	 */
	public function getTreeCategoriesAction()
	{
		$this->getResponse ()->setBody ( Zend_Json::encode ($this->getTopCategories($parentId = 2, $isChild = false, $categoryIds = '')) );
		return;
	} 
	
	/*
	 * Top Categories html
	 */
	public function getTopCategories($parentId, $isChild, $categoryIds){
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
			if(Mage::helper ( 'sociallogin' )->_hasProducts($category->getId())){

				if($flag <= 7){
					$html .= '<li><a href="'.$category->getUrl($category).'" id="cate'.$category->getId() .'">'.$category->getName()."<span class='prod-count'>(".Mage::getModel("catalog/category")->load($category->getId())->getProductCount().")</span></a>";
				} else {
					$html .= '<li class="more-item"><a href="'.$category->getUrl($category).'" id="cate'.$category->getId() .'">'.$category->getName()."<span class='prod-count'>(".Mage::getModel("catalog/category")->load($category->getId())->getProductCount().")</span></a>";
				}
				$subcats = $category->getChildren();
					if($subcats != '')
					{
						$html .= '<ul id="cate' . $category->getId() . 'sub_category">';
						$html .= $this->getTopCategories($category->getId(), true, $categoryIds);
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
	 * Ajax Brand pop-up
	 *
	 * @return HTML
	 */
	public function getPopUpBrandAjaxAction(){
		$attributeCode = 'brands';
		$loop = "brands-loop";
		$url = 'by-brands.html?brands';
		$brandHtml = Mage::helper ( 'sociallogin' )->brandAttributePop($attributeCode, $url, $loop);
		$this->getResponse ()->setBody ( Zend_Json::encode ($brandHtml) );
		return;
	}
}