<?php
class Smartwave_Porto_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
		$this->loadLayout();
        $this->renderLayout();
    }
    
    public function showcategoryproductsAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $response = array();
            $category_id = $this->getRequest()->getParam("category_id");
            $product_count = $this->getRequest()->getParam("product_count");
            $aspect_ratio = $this->getRequest()->getParam("aspect_ratio");
            $ratio_width = $this->getRequest()->getParam("image_width");
            $ratio_height = $this->getRequest()->getParam("image_height");
            $columns = $this->getRequest()->getParam("columns");
            
            $html = "";
            
            $products = $this->getLayout()->createBlock("filterproducts/latest_home_list")->setTemplate("smartwave/ajaxproducts/grid.phtml")->setData("category_id",$category_id)->setData("product_count",$product_count)->setData("aspect_ratio",$aspect_ratio)->setData("image_width",$ratio_width)->setData("image_height",$ratio_height)->setData("columns",$columns)->toHtml();
            
            $html .= $products;
            
            $response['category_products'] = $html;
            $response['status'] = 'SUCCESS';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            
            return;
        }
    }
}