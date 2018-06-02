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
class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'emipro_flexishipping';
        $this->_controller = 'adminhtml_flexiShipping';
        $this->_headerText = Mage::helper('emipro_flexishipping')->__('Flexi Shipping Method');
	    parent::__construct();
       
    }
}
