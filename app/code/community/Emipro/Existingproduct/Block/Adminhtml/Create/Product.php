<?php
class Emipro_existingproduct_Block_Adminhtml_Create_Product extends Mage_Adminhtml_Block_Widget
{
   public function __construct()
    {
        parent::__construct();
        $this->setId('existingproduct_create_customer');
    }

    public function getHeaderText()
    {
        return Mage::helper('existingproduct')->__('Please Select Existing Product');
    }
}
