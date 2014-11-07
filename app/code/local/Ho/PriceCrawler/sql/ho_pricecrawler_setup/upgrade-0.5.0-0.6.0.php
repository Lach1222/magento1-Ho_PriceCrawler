<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    -- DROP TABLE IF EXISTS `{$this->getTable('ho_pricecrawler/import_log')}`;

    CREATE TABLE `{$this->getTable('ho_pricecrawler/import_log')}` (
        `log_id` int(11) NOT NULL AUTO_INCREMENT,
        `site_id` int(11) NULL DEFAULT '0',
        `start_date` timestamp,
        `end_date` timestamp,
        `imported` int(11) DEFAULT NULL,
        `errors` int(11) DEFAULT NULL,
        PRIMARY KEY (`log_id`),
        FOREIGN KEY (`site_id`) REFERENCES `ho_pricecrawler_sites`(`site_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='H&O PriceCrawler Import Log';

");

$installer->endSetup();