<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_BulkProductImport_Block_Adminhtml_Bulkproductimport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    protected function _prepareLayout() {
        $this->_formScripts[] = "
			function getAttr(val)
			{
				document.getElementById('register').href=document.getElementById('register').href + 'set/' + val + '/';
				document.getElementById('options').href=document.getElementById('options').href + 'set/' + val + '/';
			}
			function mysubmit() {
				$('edit_form').setAttribute('target','_blank');
				editForm.submit();
			}
			";
        return parent::_prepareLayout();
    }

    public function __construct() {
        parent::__construct();
        $this->_objectId = "entity_id";
        $this->_blockGroup = "emipro_bulkproductimport";
        $this->_controller = "adminhtml_bulkproductimport";

        $this->removeButton("delete");
        $this->removeButton("back");
        $this->removeButton("save");
        $this->removeButton("reset");
    }

    public function getHeaderText() {
        return Mage::helper("marketplace")->__("Bulk Product Import");
    }

}
