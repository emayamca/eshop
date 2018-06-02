<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Bulkorderform
 */
/**
 * Bulkorder adminhtml Edit
 */
class Fuel_Bulkorderform_Block_Adminhtml_Bulkorderform_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'bulkorderform';
        $this->_controller = 'adminhtml_bulkorderform';
        
        $this->_updateButton('save', 'label', Mage::helper('bulkorderform')->__('Save Bulkorder'));
        $this->_updateButton('delete', 'label', Mage::helper('bulkorderform')->__('Delete Bulkorder'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bulkorderform_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bulkorderform_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bulkorderform_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

  /*
   * Bulkorderform edit page header text
   *
   * @return string
   */
    public function getHeaderText()
    {
        if( Mage::registry('bulkorderform_data') && Mage::registry('bulkorderform_data')->getId() ) {
            return Mage::helper('bulkorderform')->__("Edit Bulkorder '%s'", $this->htmlEscape(Mage::registry('bulkorderform_data')->getFullname()));
        } else {
            return Mage::helper('bulkorderform')->__('Add Bulkorder');
        }
    }
}