<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/sites')}` ADD COLUMN `identifier` varchar(255) DEFAULT NULL AFTER `name`;
    ALTER TABLE `{$this->getTable('ho_pricecrawler/sites')}` ADD COLUMN `fields` text DEFAULT NULL;

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `image` varchar(255) DEFAULT NULL;
    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `stock` text DEFAULT NULL;

");

$installer->endSetup();