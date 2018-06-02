<?php
class Emipro_existingproduct_Block_Adminhtml_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'product_id';
        $this->_controller = 'emipro_existing_catalog_product';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('existingproduct_create');
        $this->_removeButton('back');
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }
    
    public function getHeaderHtml()
    {
        $out = '<div id="order-header"><h3 class="icon-head head-sales-order">Sell Existing Product</h3></div>';
        return $out;
    }
    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }
}
