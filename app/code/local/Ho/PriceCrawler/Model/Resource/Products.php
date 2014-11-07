<?php

class Ho_PriceCrawler_Model_Resource_Products extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ho_pricecrawler/products', 'product_id');
    }
}