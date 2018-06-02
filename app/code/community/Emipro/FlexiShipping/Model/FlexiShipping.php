<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_FlexiShipping_Model_FlexiShipping extends Mage_CatalogRule_Model_Rule
{
    public function _construct()
    {
		
        parent::_construct();
        $this->_init('emipro_flexishipping/flexiShipping');
      
    }
   public function getMatchingProductIds1($websiteIds)
    {
		
        if (is_null($this->_productIds)) {
			
            $this->_productIds = array();
            $this->setCollectedAttributes(array());

            if ($websiteIds) {
                /** @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
                $productCollection = Mage::getResourceModel('catalog/product_collection');
              
                $productCollection->addWebsiteFilter($websiteIds);
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
            }
           }
           return $this->_productIds;

	   }
	    protected function _afterSave()
		{
			
		}
  }
