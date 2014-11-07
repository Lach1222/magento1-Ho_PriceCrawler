<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/logs')}` ADD `url` varchar(255) DEFAULT NULL;
    ALTER TABLE `{$this->getTable('ho_pricecrawler/logs')}` ADD `is_item` tinyint(1) DEFAULT 0;

");

$installer->endSetup();