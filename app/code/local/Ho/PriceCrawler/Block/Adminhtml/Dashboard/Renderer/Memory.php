<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Renderer_Memory extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        if (!$value) return '0B';

        $unit=array('B','KB','MB','GB','TB','PB');

        return @round($value/pow(1024,($i=floor(log($value,1024)))),2).$unit[$i];
    }
}
