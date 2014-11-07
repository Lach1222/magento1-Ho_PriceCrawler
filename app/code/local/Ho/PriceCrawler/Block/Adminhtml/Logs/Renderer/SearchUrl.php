<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_SearchUrl extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('ho_pricecrawler');

        $jobId = $row->getData('job_id');
        $url = $row->getData('url');

        $url = Mage::helper('ho_pricecrawler/scrapinghub')->getUrlSearchUrl($jobId, $url);

        return '<a href="' . $url . '" class="external-url tooltip tooltip-nowrap"'
            . 'data-tooltip="' . $helper->__('Search for this URL in the requests of this job in Scrapinghub') .'"'
            . 'target="_blank">'
            . $helper->__('Open Request') . '</a>';
    }
}