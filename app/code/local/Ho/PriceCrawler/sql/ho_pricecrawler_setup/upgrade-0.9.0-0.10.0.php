<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` DROP INDEX `product_entity_id`;

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}`
      ADD INDEX `IDX_HO_PRICECRAWLER_PRODUCTS_PRODUCT_ENTITY_ID_PRICE_SITE_ID` (`product_entity_id`, `price`, `site_id`)

");

$installer->endSetup();