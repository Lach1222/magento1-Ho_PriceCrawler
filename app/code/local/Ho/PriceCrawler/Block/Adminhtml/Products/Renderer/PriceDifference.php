<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_PriceDifference extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        $percentage = ($row->getPrice() > 0) ? $value / $row->getPrice() * 100 : 0;
        $noticeLimit = Mage::getStoreConfig(Ho_PriceCrawler_Model_Products::XML_PATH_PRICE_DIFFERENCE_PERCENTAGE);

        if (is_null($value)) {
            return '-';
        }

        if ($value > 0) {
            $leading = '+';
            $color = ($percentage > $noticeLimit) ? 'red' : 'orange';
        }
        else {
            $leading = '-';
            $color = 'green';
            $value = $value * -1;
        }
        $price = Mage::helper('core')->formatPrice($value);
        $value = $price;
        return '<span style="color: ' . $color . ';font-weight:bold;">' . $leading . ' ' . $value . '</span>';
    }
}