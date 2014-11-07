<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD CONSTRAINT product_identifier_site_id UNIQUE (`product_identifier`,`site_id`)

");

$installer->endSetup();