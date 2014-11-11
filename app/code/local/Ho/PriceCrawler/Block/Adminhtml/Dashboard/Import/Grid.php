<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Import_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('start_date');
        $this->setDefaultDir('desc');
        $this->setDefaultLimit(Mage::helper('ho_pricecrawler')->getImportLogLimit());
        $this->setId('importLogGrid');
    }

    protected function _prepareCollection()
    {
        /** @var Ho_PriceCrawler_Model_Resource_ImportLog_Collection $collection */
        $collection = Mage::getModel('ho_pricecrawler/importLog')
            ->getCollection()
            ->setOrder('start_date', 'DESC');

        $select = $collection->getSelect();
        $select->joinLeft(
            'ho_pricecrawler_sites AS s',
            'main_table.site_id = s.site_id',
            array('name AS site_name', 'identifier AS site_identifier')
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id', array(
            'header'    => $this->__('Log ID'),
            'index'     => 'log_id',
            'width'     => '60px',
        ));

        $this->addColumn('site_name', array(
            'header'    => $this->__('Spider'),
            'index'     => 'site_name'
        ));

        $this->addColumn('start_date', array(
            'header'    => $this->__('Start time'),
            'index'     => 'start_date',
            'type'      => 'datetime',
            'width'     => '160px',
        ));
        $this->addColumn('end_date', array(
            'header'    => $this->__('End time'),
            'index'     => 'end_date',
            'type'      => 'datetime',
            'width'     => '160px',
        ));
        $this->addColumn('elapsed', array(
            'header'    => $this->__('Running time'),
            'align'     => 'right',
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Dashboard_Renderer_Elapsed',
            'width'     => '50px',
        ));

        $this->addColumn('imported', array(
            'header'    => $this->__('Imported'),
            'align'     => 'right',
            'index'     => 'imported',
            'width'     => '50px',
        ));
        $this->addColumn('errors', array(
            'header'    => $this->__('Errors'),
            'align'     => 'right',
            'index'     => 'errors',
            'width'     => '50px',
        ));

        $this->addColumn('memory_usage', array(
            'header'    => $this->__('Memory usage'),
            'align'     => 'right',
            'index'     => 'memory_usage',
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Dashboard_Renderer_Memory',
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }
}
