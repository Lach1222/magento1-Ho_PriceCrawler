<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_Message extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8');

        $limit = 50;

        return '<span class="log-message" data-content="' . htmlspecialchars($value) . '"> ' . substr($value, 0, $limit)
            . (strlen($value) > $limit ? '...' : '')
            . '</span>';
    }
}