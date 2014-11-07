<?php

class Ho_PriceCrawler_Model_System_Config_Source_Sorting
{
    const RELEVANCE     = 0;
    const ALPHABETICAL  = 1;

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::RELEVANCE,
                'label' => Mage::helper('ho_pricecrawler')->__('Relevance')
            ),
            array(
                'value' => self::ALPHABETICAL,
                'label' => Mage::helper('ho_pricecrawler')->__('Alphabetical')
            ),
        );
    }
}