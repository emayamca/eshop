<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('bulkorderform')};
CREATE TABLE {$this->getTable('bulkorderform')} (
  `bulkorderform_id` int(11) unsigned NOT NULL auto_increment,
  `fullname` varchar(255) NOT NULL default '',
  `emailaddress` varchar(255) NOT NULL default '',
  `mobilenumber` bigint(20) unsigned NOT NULL default '0',
  `country` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `productname` varchar(255) NOT NULL default '',
  `quantity` int(255) unsigned NOT NULL default '0',
  `status` smallint(6) NOT NULL default '0',
  `form` smallint(6) default '0',
  `comments` text default NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`bulkorderform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 