<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ho/pricecrawler/index.phtml');
    }

    protected function _prepareLayout()
    {
        /** @var Ho_PriceCrawler_Helper_Data $helper */
        $helper = Mage::helper('ho_pricecrawler');

        $this->setChild('pending_jobs',
            $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_dashboard_jobs')
                ->setName('pending_jobs')
                ->setJobs($this->_getJobs('pending'))
        );

        $this->setChild('running_jobs',
            $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_dashboard_jobs')
                ->setName('running_jobs')
                ->setJobs($this->_getJobs('running'))
        );

        $this->setChild('finished_jobs',
            $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_dashboard_jobs')
                ->setName('finished_jobs')
                ->setJobs($this->_getJobs('finished', $helper->getFinishedJobLimit()))
        );

        $this->setChild('import_log',
            $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_dashboard_import_grid')
        );

        parent::_prepareLayout();
    }

    protected function _getJobs($state, $limit = false)
    {
        $jobs = Mage::getModel('ho_pricecrawler/scrapinghub_jobs')
            ->listJobs(array(
                'state' => $state,
            ));

        if ($limit) {
            $jobs = array_slice($jobs, 0, $limit);
        }

        return $jobs;
    }
}