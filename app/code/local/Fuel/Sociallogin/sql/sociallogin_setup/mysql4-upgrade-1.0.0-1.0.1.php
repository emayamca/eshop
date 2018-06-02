<?php
$installer = $this;
$installer->startSetup();
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->addAttribute("customer", "phonenumbers",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Phone Numbers",
    "input"    => "textarea",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

$attribute   = Mage::getSingleton("eav/config")->getAttribute("customer", "phonenumbers");

$used_in_forms=array();
$used_in_forms[]="adminhtml_customer";
$used_in_forms[]="customer_account_create";
$used_in_forms[]="customer_account_edit";
$attribute->setData("used_in_forms", $used_in_forms)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 101);
$attribute->save();

$installer->addAttribute("customer", "emailaddresses",  array(
    "type"     => "text",
    "backend"  => "",
    "label"    => "Email Addresses",
    "input"    => "textarea",
    "source"   => "",
    "visible"  => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique"     => false,
    "note"       => ""

	));

$attributeemail   = Mage::getSingleton("eav/config")->getAttribute("customer", "emailaddresses");

$used_in_forms_email=array();
$used_in_forms_email[]="adminhtml_customer";
$used_in_forms_email[]="customer_account_create";
$used_in_forms_email[]="customer_account_edit";
$attributeemail->setData("used_in_forms", $used_in_forms_email)
		->setData("is_used_for_customer_segment", true)
		->setData("is_system", 0)
		->setData("is_user_defined", 1)
		->setData("is_visible", 1)
		->setData("sort_order", 102);
$attributeemail->save();


$installer->endSetup();