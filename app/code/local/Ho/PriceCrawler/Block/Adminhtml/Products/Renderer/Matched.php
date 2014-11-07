<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_Matched extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        if (is_numeric($value)) {
            return Mage::helper('catalog')->__('Yes');
        }
        else {
            return Mage::helper('catalog')->__('No');
        }
    }
}