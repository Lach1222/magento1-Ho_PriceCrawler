<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    -- Add `show_category` column to sites table
    ALTER TABLE `{$this->getTable('ho_pricecrawler/sites')}` ADD `show_category` TINYINT(1) DEFAULT 0;

");

$installer->endSetup();