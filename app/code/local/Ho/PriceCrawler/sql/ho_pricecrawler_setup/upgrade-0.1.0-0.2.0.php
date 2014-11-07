<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('ho_pricecrawler/products')}` ADD FULLTEXT(name);

");

$installer->endSetup();