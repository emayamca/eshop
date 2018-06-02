<?php
 
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
 
-- DROP TABLE IF EXISTS {$this->getTable('emipro_sellerproduct')};
CREATE TABLE {$this->getTable('emipro_sellerproduct')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 
");
 
$installer->endSetup();