<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `date_price_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';
    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD COLUMN `date_product_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

");

$installer->endSetup();