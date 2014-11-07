<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_JobId extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        return $value;
    }
}