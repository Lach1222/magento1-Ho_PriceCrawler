<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $url = $row->getData($this->getColumn()->getIndex());

        $limit = 80;

        return '<a href="' . $url . '" class="log-url tooltip tooltip-nowrap" target="_blank" data-tooltip="' . $url . '">'
            . substr($url, 0, $limit)
            . (strlen($url) > $limit ? '...' : '')
            . '</a>';
    }
}