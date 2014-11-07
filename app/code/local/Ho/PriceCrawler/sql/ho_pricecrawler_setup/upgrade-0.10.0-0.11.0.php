<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `original_price` varchar(255) DEFAULT NULL AFTER `description`;
    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `original_special_price` varchar(255) DEFAULT NULL AFTER `original_price`;

");

$installer->endSetup();