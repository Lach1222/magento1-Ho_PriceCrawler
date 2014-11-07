<?php

class Ho_PriceCrawler_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'ho_pricecrawler';
        $this->_controller = 'adminhtml_products';
        $this->_headerText = $this->__('PriceCrawler Products');

        parent::__construct();

        $this->_removeButton('add');
    }
}