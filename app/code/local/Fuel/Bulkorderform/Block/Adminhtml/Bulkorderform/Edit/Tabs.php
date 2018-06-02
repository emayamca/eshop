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
 * Bulkorder adminhtml tab informations
 */
class Fuel_Bulkorderform_Block_Adminhtml_Bulkorderform_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bulkorderform_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bulkorderform')->__('Bulkorder Information'));
  }
  
  /*
   * Bulkorderform add/edit page tab text
   *
   * @return HTML
   */
  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bulkorderform')->__('Bulkorder Information'),
          'title'     => Mage::helper('bulkorderform')->__('Bulkorder Information'),
          'content'   => $this->getLayout()->createBlock('bulkorderform/adminhtml_bulkorderform_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}