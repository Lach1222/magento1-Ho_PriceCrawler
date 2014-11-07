<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
    -- DROP TABLE IF EXISTS `{$this->getTable('ho_pricecrawler/sites')}`;

    CREATE TABLE `{$this->getTable('ho_pricecrawler/sites')}` (
      `site_id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) DEFAULT NULL,
      `description` varchar(255) DEFAULT NULL,
      `active` tinyint(1) NULL DEFAULT '1',
      PRIMARY KEY (`site_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='H&O PriceCrawler Sites';


    -- DROP TABLE IF EXISTS `{$this->getTable('ho_pricecrawler/products')}`;

    CREATE TABLE `{$this->getTable('ho_pricecrawler/products')}` (
      `product_id` int(11) NOT NULL AUTO_INCREMENT,
      `product_identifier` varchar(255) DEFAULT NULL,
      `name` varchar(255) DEFAULT NULL,
      `description` text DEFAULT NULL,
      `price` decimal(12,4) NULL DEFAULT '0',
      `product_entity_id` int(10) unsigned NULL,
      `site_id` int(11) NULL DEFAULT '0',
      PRIMARY KEY (`product_id`),
      FOREIGN KEY (`product_entity_id`) REFERENCES `catalog_product_entity`(`entity_id`),
      FOREIGN KEY (`site_id`) REFERENCES `ho_pricecrawler_sites`(`site_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='H&O PriceCrawler Products';
");

$installer->endSetup();