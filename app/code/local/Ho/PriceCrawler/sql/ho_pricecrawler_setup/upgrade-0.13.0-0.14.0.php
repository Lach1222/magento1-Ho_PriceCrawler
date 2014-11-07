<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}`
      ADD INDEX `IDX_HO_PRICECRAWLER_PRODUCTS_SITE_ID_PRODUCT_IDENTIFIER` (`site_id`, `product_identifier`);

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}`
      ADD INDEX `IDX_HO_PRICECRAWLER_PRODUCTS_SITE_ID_URL` (`site_id`, `url`);

");

$installer->endSetup();