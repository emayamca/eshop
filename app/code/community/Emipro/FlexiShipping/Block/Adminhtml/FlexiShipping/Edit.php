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
class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit extends
                   Mage_Adminhtml_Block_Widget_Form_Container
 {
   public function __construct()
   {
     
         $this->_objectId = 'id';
        $this->_controller = 'adminhtml_flexiShipping';
		 $this->_blockGroup = 'emipro_flexishipping';
        parent::__construct();

      
        $this->_addButton('save_apply', array(
			'value'=>'save_apply',
			'name'=>'save_apply',
            'class'   => 'save',
            'label'   => Mage::helper('emipro_flexishipping')->__('Save and Apply'),
            'onclick' => "$('rule_auto_apply').value=1; editForm.submit()",
				
        ) );
	}
   public function getHeaderText()
    {
         $rule = Mage::registry('flexishipping');
        if ($rule->getShipId()) {
            return Mage::helper('emipro_flexishipping')->__("Edit Rule '%s'", $this->escapeHtml($rule->getName()));
        }
        else {
            return Mage::helper('emipro_flexishipping')->__('New Rule');
        }
    }
}
