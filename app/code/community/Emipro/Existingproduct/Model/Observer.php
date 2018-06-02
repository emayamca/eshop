<?php
class Emipro_Existingproduct_Model_Observer
{
   public function productsavebefore($observer)
   {
      $actionname = Mage::app()->getRequest()->getActionName();
      if($actionname!='duplicate')
      {
         $product = $observer->getEvent()->getProduct();
         $skuprefix = Mage::helper('existingproduct')->getSkuPrefix($product);
         if(isset($skuprefix))
         {
            $length = strlen($skuprefix);
            $sku = substr($skuprefix,0,$length);
         }
         else
         {
            $sku ='';
         }
         $product->setSku($sku.($product->getSku()));
      }
   }
   public function adminhtml_block_html_before($observer) {
      $block = $observer->getBlock();
      if(!isset($block))
         return $this;

      if(!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
         if($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu) {
            $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();
            $menu = $config->getNode('menu');
            if($menu->catalog->children->existingproduct){
               unset($menu->catalog->children->existingproduct);
            }
         }
      }
   }

   public function duplicateproduct($observer){
      $_product = $observer->getCurrentProduct();
      $_newproduct = $observer->getNewProduct();
      if($_product && $_newproduct){
         if(trim($_product->getEsin())==''){
            $esinnumber = $this->getEsinNumber(3,true).$this->getEsinNumber(7);
               $productcheck = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('esin',$esinnumber);
               if($productcheck->count()>0){
                  $esinnumber = $this->getEsinNumber(3,true).$this->getEsinNumber(7);
               }
            $_product->setEsin($esinnumber);
            try{
               $_product->save();
            }catch(Exception $e){
               Mage::log($e->getMessage());
            }
            $_newproduct->setEsin($esinnumber);
         }
      }
   }

   public function getEsinNumber($esin_length, $alpha = false){
      if($alpha)
      {
         $allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';   
      }else{
         $allowed_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
      }
      $allowed_count = strlen($allowed_chars);
      $esin = null;
      $esin = '';
      for($i = 0; $i < $esin_length; ++$i) {
         $esin .= $allowed_chars{mt_rand(0, $allowed_count - 1)};
      }
      return $esin;
   }

   public function addcolumnproductgrid($observer){
      $block = $observer->getBlock();
      if(!isset($block))
         return $this;

      if($block instanceof Emipro_Marketplace_Block_Adminhtml_Catalog_Product_Grid) {
         $block->addColumnAfter(
             'esin',
             array(
                 'header' => Mage::helper('existingproduct')->__('ESIN'),
                 'index'  => 'esin'
             ),
            'status'
         );
      }
   }

   public function updateproductcollection($observer){
       $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }
        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
            $collection->addAttributeToSelect('esin');
        }
   }
}