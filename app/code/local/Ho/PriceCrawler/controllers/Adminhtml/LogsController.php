<?php

class Ho_PriceCrawler_Adminhtml_LogsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ho_pricecrawler/ho_pricecrawler_logs')
            ->_title($this->__('PriceCrawler'))->_title($this->__('Logs'))
            ->_addBreadcrumb($this->__('PriceCrawler'), $this->__('PriceCrawler'))
            ->_addBreadcrumb($this->__('Logs'), $this->__('Logs'));

        return $this;
    }

    /**
     * Show logs grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ho_pricecrawler/ho_pricecrawler_logs');
    }
}