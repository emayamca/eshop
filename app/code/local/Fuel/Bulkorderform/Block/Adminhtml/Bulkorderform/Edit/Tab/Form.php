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
 * Bulkorder adminhtml form
 */
class Fuel_Bulkorderform_Block_Adminhtml_Bulkorderform_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bulkorderform_form', array('legend'=>Mage::helper('bulkorderform')->__('Bulkorder information')));
     
      $fieldset->addField('fullname', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Customer Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'fullname',
      ));
	  
	  $fieldset->addField('emailaddress', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Email Address'),
          'class'     => 'validate-email required-entry',
          'required'  => true,
          'name'      => 'emailaddress',
      ));
	  
	  $fieldset->addField('mobilenumber', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Mobile Number'),
          'class'     => 'validate-number required-entry validate-length maximum-length-10 minimum-length-10 validate-digits',
          'required'  => true,
          'name'      => 'mobilenumber',
      ));
	  $fieldset->addField('country', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Country'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'country',
      ));
	  
	  $fieldset->addField('city', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('City'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'city',
      ));
	  
	  $fieldset->addField('productname', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Product Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'productname',
      ));

	  $fieldset->addField('quantity', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Qty'),
          'class'     => 'validate-number required-entry',
          'required'  => true,
          'name'      => 'quantity',
      ));
	  
	  $fieldset->addField('comments', 'text', array(
          'label'     => Mage::helper('bulkorderform')->__('Comments'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'comments',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bulkorderform')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bulkorderform')->__('Acitvate'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bulkorderform')->__('Deacitvate'),
              ),
          ),
      ));
	  
	  $fieldset->addField('form', 'select', array(
          'label'     => Mage::helper('bulkorderform')->__('Select Form Name'),
          'name'      => 'form',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('bulkorderform')->__('Bulkorder'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bulkorderform')->__('Enquiryform'),
              ),
          ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getContactformData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getContactformData());
          Mage::getSingleton('adminhtml/session')->setContactformData(null);
      } elseif ( Mage::registry('bulkorderform_data') ) {
          $form->setValues(Mage::registry('bulkorderform_data')->getData());
      }
      return parent::_prepareForm();
  }
}