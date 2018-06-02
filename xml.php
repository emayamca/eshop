<?php
require_once ('app/Mage.php');
Mage::app(); 

// Define the path to the root of Magento installation.
define('ROOT', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
$collection = Mage::getModel('catalog/product')->getCollection();
$collection->addAttributeToSelect('*');
//$collection->setPageSize(10);
$_helper = Mage::helper('catalog/output');
//add filters if needed.
//$collection->addAttributeToFilter('status', 1); //for example.

$padd = '    '; //4 spaces for identation
$eol = "\n"; //end of line
$xml = '<?xml version="1.0"?>'.$eol;
$xml .= '<products>'.$eol;
$count = 1;
foreach ($collection as $product) {
	$brand = $product->getAttributeText('brands');
 	$images = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages();
    $xml .= $padd.'<product>'.$eol;
    $xml .= str_repeat($padd, 2).'<id>'.$product->getId().'</id>'.$eol;
    $xml .= str_repeat($padd, 2).'<title><![CDATA['.$product->getName().']]></title>'.$eol;
    $xml .= str_repeat($padd, 2).'<availability>In Stock</availability>'.$eol;
    $xml .= str_repeat($padd, 2).'<condition>new</condition>'.$eol;
    $xml .= str_repeat($padd, 2).'<link>'.str_replace('xml.php', '', $product->getProductUrl()).'</link>'.$eol;
    $xml .= str_repeat($padd, 2).'<description><![CDATA['.$_helper->productAttribute($product, nl2br($product->getShortDescription()), 'short_description').']]></description>'.$eol;
    $xml .= str_repeat($padd, 2).'<sku><![CDATA['.$product->getSku().']]></sku>'.$eol;
    foreach ($product->getCategoryIds() as $catIds) {
    	$_cat = Mage::getModel('catalog/category')->load($catIds);
    	//$catName[] = $_cat->getName().' -> ';
    	$xml .= str_repeat($padd, 2).'<subcategory><![CDATA['.$_cat->getName().']]></subcategory>'.$eol;
    	break;
	}
	
    $xml .= str_repeat($padd, 2).'<price><![CDATA['.Mage::helper('core')->currency($product->getFinalPrice(), true, false).']]></price>'.$eol;
    $xml .= str_repeat($padd, 2).'<brand><![CDATA['.$brand[0].']]></brand>'.$eol;
    $xml .= str_repeat($padd, 2).'<is_active>Enabled</is_active>'.$eol;
    foreach ($images as $image){
		$xml .= str_repeat($padd, 2).'<image_link'.$count.'>'.Mage::helper('catalog/image')->init($product, 'image', $image->getFile()).'</image_link >'.$eol;
		$count++;
	}
    
    //add here all the attributes you need to export
    $xml .= $padd.'</product>'.$eol;
}
$xml .= '</products>'.$eol;

print_r($xml); die;
?>