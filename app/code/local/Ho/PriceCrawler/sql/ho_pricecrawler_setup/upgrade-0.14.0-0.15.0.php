<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    -- Add 'category' column
    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `category` varchar(255) DEFAULT NULL AFTER `description`;

");

$installer->endSetup();