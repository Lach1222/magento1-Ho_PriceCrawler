<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'ho_pricecrawler';
        $this->_controller = 'adminhtml_logs';
        $this->_headerText = $this->__('PriceCrawler Logs');

        parent::__construct();
    }
}