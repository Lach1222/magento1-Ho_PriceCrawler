<?php

class Ho_PriceCrawler_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ho_pricecrawler/ho_pricecrawler_dashboard')
            ->_title($this->__('PriceCrawler'))->_title($this->__('Dashboard'))
            ->_addBreadcrumb($this->__('PriceCrawler'), $this->__('PriceCrawler'))
            ->_addBreadcrumb($this->__('Dashboard'), $this->__('Dashboard'));

        return $this;
    }

    public function stopjobAction()
    {
        $jobId = base64_decode($this->getRequest()->getParam('id'));
        $result = $this->_getJobModel()->stopJob($jobId);

        if ($result) {
            Mage::getModel('adminhtml/session')->addSuccess(
                $this->__('Job %s successfully stopped. Note: It can take a minute until the job is canceled in Scrapinghub.', $jobId)
            );
        }
        else {
            Mage::getModel('adminhtml/session')->addError(
                $this->__('Something went wrong while cancelling job %s. Please try again later.', $jobId)
            );
        }

        $this->_redirect('*/*');
    }

    public function deletejobAction()
    {
        $jobId = base64_decode($this->getRequest()->getParam('id'));
        $result = $this->_getJobModel()->deleteJob($jobId);

        if ($result) {
            Mage::getModel('adminhtml/session')->addSuccess(
                $this->__('Job %s successfully deleted.', $jobId)
            );
        }
        else {
            Mage::getModel('adminhtml/session')->addError(
                $this->__('Something went wrong while deleting job %s. Please try again later.', $jobId)
            );
        }

        $this->_redirect('*/*');
    }

    public function scheduleAction()
    {
        $spider = $this->getRequest()->getParam('spider');
        $result = $this->_getJobModel()->schedule($spider);

        if ($result) {
            Mage::getModel('adminhtml/session')->addSuccess(
                $this->__('Job for %s successfully scheduled.', $spider)
            );
        }
        else {
            Mage::getModel('adminhtml/session')->addError(
                $this->__('Something went wrong while scheduling job for %s. Check if a spider with this name exists in Scrapinghub or try again later.', $spider)
            );
        }

        $this->_redirect('*/*');
    }

    public function importAction()
    {
        $spider = $this->getRequest()->getParam('spider');
        $result = $this->_getItemsModel()->import($spider);

        if ($result) {
            Mage::getModel('adminhtml/session')->addSuccess(
                $this->__('Items for %s successfully imported. Check the Import log on this dashboard for more information.', $spider)
            );
        }
        else {
            Mage::getModel('adminhtml/session')->addError(
                $this->__('Something went wrong while importing items for %s.', $spider)
            );
        }

        $this->_redirect('*/*');
    }

    /**
     * @return Ho_PriceCrawler_Model_Scrapinghub_Jobs
     */
    protected function _getJobModel()
    {
        return Mage::getModel('ho_pricecrawler/scrapinghub_jobs');
    }

    /**
     * @return Ho_PriceCrawler_Model_Scrapinghub_Items
     */
    protected function _getItemsModel()
    {
        return Mage::getModel('ho_pricecrawler/scrapinghub_items');
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ho_pricecrawler/ho_pricecrawler_dashboard');
    }
}