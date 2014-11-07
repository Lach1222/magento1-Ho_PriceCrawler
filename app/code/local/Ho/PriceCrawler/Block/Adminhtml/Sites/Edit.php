<?php

class Ho_PriceCrawler_Block_Adminhtml_Sites_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'ho_pricecrawler';
        $this->_controller = 'adminhtml_sites';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Site'));
        $this->_updateButton('delete', 'label', $this->__('Delete Site'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('ho_pricecrawler')->getId()) {
            return $this->__('Edit Site');
        }
        else {
            return $this->__('New Site');
        }
    }
}