<?php   

$installer = $this;  
$installer->startSetup();

$installer->run("ALTER TABLE  `".$this->getTable('catalogrule/rule')."` ADD `seller_admin_id` int(11)  NULL");

$installer->endSetup();
