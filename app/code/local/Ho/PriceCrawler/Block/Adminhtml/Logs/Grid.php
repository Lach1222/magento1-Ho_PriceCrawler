<?php

class Ho_PriceCrawler_Block_Adminhtml_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $defaultFilter = array('is_item' => 0);

        $jobId = base64_decode($this->getRequest()->getParam('job_id'));
        if ($jobId) {
            $defaultFilter['job_id'] = $jobId;
        }

        $this->setDefaultSort('job_id');
        $this->setDefaultFilter($defaultFilter);
        $this->setId('ho_pricecrawler_logs_grid');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _getCollectionClass()
    {
        return 'ho_pricecrawler/logs_collection';
    }

    protected function _prepareCollection()
    {
        /** @var Ho_PriceCrawler_Model_Resource_Sites_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('ho_pricecrawler');

        $this->addColumn('entity_id', array(
            'header'    => $helper->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));

        $this->addColumn('site_id', array(
            'header'    => $helper->__('Site'),
            'width'     => '100px',
            'index'     => 'site_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('ho_pricecrawler/system_config_source_sites')->toArray(),
        ));

        $this->addColumn('job_id', array(
            'header'    => $helper->__('Job ID'),
            'width'     => '100px',
            'index'     => 'job_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('ho_pricecrawler/system_config_source_jobIds')->toArray(),
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_JobId',
        ));

        $this->addColumn('imported_at', array(
            'header'    => $helper->__('Imported At'),
            'width'     => '100px',
            'index'     => 'imported_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('date', array(
            'header'    => $helper->__('Logged At'),
            'width'     => '100px',
            'index'     => 'date',
            'type'      => 'datetime',
        ));

        $this->addColumn('message', array(
            'header'    => $helper->__('Message'),
            'width'     => '300px',
            'index'     => 'message',
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_Message',
        ));

        $this->addColumn('url', array(
            'header'    => $helper->__('URL'),
            'index'     => 'url',
            'width'     => '500px',
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Logs_Renderer_Url',
        ));

        $this->addColumn('is_item', array(
            'header'    => $helper->__('Has Item'),
            'width'     => '50px',
            'index'     => 'is_item',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // Return nothing, no edit page
    }
}