<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Renderer_Elapsed extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $start = new DateTime($row->getStartDate());
        $end = new DateTime($row->getEndDate());
        $interval = $start->diff($end);

        return
            (($interval->d > 0) ? $interval->d .  'd ' : '') .
            (($interval->d > 0 || $interval->h > 0) ? $interval->h . 'h ' : '') .
            (($interval->d > 0 || $interval->h > 0 || $interval->i > 0) ? $interval->i . 'm ' : '') .
            $interval->s . 's';
    }
}