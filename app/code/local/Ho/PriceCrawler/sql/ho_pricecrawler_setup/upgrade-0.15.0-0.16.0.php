<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    -- Move 'site_id' column
    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` CHANGE COLUMN `site_id` `site_id` INT(11) AFTER `product_id`;

");

$installer->endSetup();