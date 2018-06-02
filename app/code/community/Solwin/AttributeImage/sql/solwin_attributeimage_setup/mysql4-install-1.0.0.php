<?php

$installer = $this;

$tableOption = $this->getTable('eav_attribute_option');
$installer->startSetup();

$installer->run("
ALTER TABLE `{$tableOption}`
    ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
");

$installer->run("
ALTER TABLE `{$tableOption}`
    ADD `thumb` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
");

$installer->endSetup();
