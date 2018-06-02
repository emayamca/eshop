<?php
/**
 * Store the current object in installer variable
 */
$installer = $this;
$installer->startSetup();
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->addAttribute('customer', 'login_provider', array('label'=> 'Provider',
    'type'      => 'varchar',
    'input'     => 'text',
    'visible'   => true,
    'required'  => false
)); 
$eavConfig = Mage::getSingleton('eav/config');
$attribute = $eavConfig->getAttribute('customer', 'login_provider');
$attribute->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit'));
$attribute->save();
$installer->endSetup(); 