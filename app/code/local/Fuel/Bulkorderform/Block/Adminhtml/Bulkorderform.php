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
 * Bulkorder backend block
 */
class Fuel_Bulkorderform_Block_Adminhtml_Bulkorderform extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_bulkorderform';
    $this->_blockGroup = 'bulkorderform';
    $this->_headerText = Mage::helper('bulkorderform')->__('Bulkorder Manager');
    $this->_addButtonLabel = Mage::helper('bulkorderform')->__('Add Bulkorder');
    parent::__construct();
	$this->_removeButton('add');
  }
}