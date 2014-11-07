<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/import_log')}` ADD COLUMN `memory_usage` int(11) DEFAULT NULL;

");

$installer->endSetup();