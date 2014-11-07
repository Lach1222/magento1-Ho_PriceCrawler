<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        if ($row->getProductEntityId()) {
            return parent::render($row);
        }
        else {
            return '-';
        }
    }
}