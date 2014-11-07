<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` CHANGE `price` `price` decimal(12,4) DEFAULT NULL;

");

$installer->endSetup();