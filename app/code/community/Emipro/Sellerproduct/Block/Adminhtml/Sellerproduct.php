<?php
class Emipro_Sellerproduct_Block_Adminhtml_Sellerproduct extends Emipro_Marketplace_Block_Adminhtml_Sellerlist_Edit_Tabs
{
	private $parent;
	protected function _prepareLayout()
	{
        $request = Mage::app()->getRequest()->getParams('id');
        $this->parent = parent::_prepareLayout();
        if(isset($request))
        {
            $this->addTab('sellerproduct_tab', array(
                        'label'     => Mage::helper('sellerproduct')->__('Display Product on Seller Profile'),
                        'url'       => $this->getUrl('adminhtml/sellerproduct/products', array('_current' => true)),
					   	'class'     => 'ajax',
                        'after'=> 'bank_details',
            ));
            return $this->parent;
        }
	}
}