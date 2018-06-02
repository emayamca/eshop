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
class  Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
   public function __construct()
    {
        parent::__construct();
        $this->setId('form');
        $this->setTitle(Mage::helper('emipro_flexishipping')->__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
