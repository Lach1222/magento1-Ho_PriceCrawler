
<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// Clean products table.
// Since we've changed the way the products are matched during the import,
// we have to delete the entries that have the same 'url' value
$installer->run("

    DELETE FROM `{$this->getTable('ho_pricecrawler/products')}`
    WHERE `product_entity_id` IS NULL

");

$installer->endSetup();