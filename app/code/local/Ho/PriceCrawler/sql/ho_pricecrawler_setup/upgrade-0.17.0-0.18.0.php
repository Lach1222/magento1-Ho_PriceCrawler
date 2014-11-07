<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("

    -- DROP TABLE IF EXISTS `{$this->getTable('ho_pricecrawler/logs')}`;

    CREATE TABLE `{$this->getTable('ho_pricecrawler/logs')}` (
      `entity_id` int(11) NOT NULL AUTO_INCREMENT,
      `site_id` int(11) DEFAULT 0,
      `job_id` varchar(255) DEFAULT NULL,
      `imported_at` timestamp,
      `date` timestamp,
      `level` int(11) DEFAULT 0,
      `message` text DEFAULT NULL,
      PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='H&O PriceCrawler Spider Logs';

");

$installer->endSetup();