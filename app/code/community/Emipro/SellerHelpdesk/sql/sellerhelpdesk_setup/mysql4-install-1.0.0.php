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


$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('seller_helpdesk_system')};
CREATE TABLE {$this->getTable('seller_helpdesk_system')} (
`id` int(11) unsigned NOT NULL auto_increment,
`admin_user_id` int(11) NOT NULL ,
`seller_id` int(11) NOT NULL,
`subject` text NOT NULL,
`status_id` int(11) NOT NULL,
`date` datetime NOT NULL,
PRIMARY KEY  (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('seller_helpdesk_conversation')};
CREATE TABLE {$this->getTable('seller_helpdesk_conversation')} (
`conversation_id` int(11) unsigned NOT NULL auto_increment,
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`message` text NOT NULL,
`status_id` int(11) NOT NULL,
`date` datetime NOT NULL,
PRIMARY KEY  (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('seller_helpdesk_attachment')};
CREATE TABLE {$this->getTable('seller_helpdesk_attachment')} (
`attachment_id` int(11) unsigned NOT NULL auto_increment,
`conversation_id` int(11) NOT NULL,
`file` varchar(255) NOT NULL,
`current_file_name` varchar(255) NOT NULL,
PRIMARY KEY  (`attachment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->run("ALTER TABLE {$this->getTable('seller_helpdesk_conversation')} ADD CONSTRAINT {$this->getTable('seller_helpdesk_conversation')}_FK_1
FOREIGN KEY (`id`) REFERENCES {$this->getTable('seller_helpdesk_system')} (`id`)
ON DELETE CASCADE ON UPDATE CASCADE
");

$installer->run("ALTER TABLE {$this->getTable('seller_helpdesk_attachment')} ADD CONSTRAINT {$this->getTable('seller_helpdesk_attachment')}_FK_1
FOREIGN KEY (`conversation_id`) REFERENCES {$this->getTable('seller_helpdesk_conversation')} (`conversation_id`)
ON DELETE CASCADE ON UPDATE CASCADE
");

$installer->run("ALTER TABLE {$this->getTable('seller_helpdesk_system')} AUTO_INCREMENT=1000");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('helpdesk_status')};
CREATE TABLE {$this->getTable('helpdesk_status')} (
`status_id` int(11) unsigned NOT NULL auto_increment,
`status` varchar(255) NOT NULL,
PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$helpdesk_status = array("New", "Closed", "Reopen", "Waiting For Admin", "Waiting For Seller");
foreach ($helpdesk_status as $status) {
    $installer->run("INSERT INTO {$this->getTable('helpdesk_status')} (status) VALUES ('" . $status . "')");
}

$installer->endSetup();
?>
