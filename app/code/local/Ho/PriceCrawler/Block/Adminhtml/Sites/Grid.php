<?php

class Ho_PriceCrawler_Block_Adminhtml_Sites_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('ho_pricecrawler_sites_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _getCollectionClass()
    {
        return 'ho_pricecrawler/sites_collection';
    }

    protected function _prepareCollection()
    {
        /** @var Ho_PriceCrawler_Model_Resource_Sites_Collection $collection */
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        $select = $collection->getSelect();
        $select->joinLeft('ho_pricecrawler_products AS pcp', 'main_table.site_id = pcp.site_id', 'COUNT(product_id) AS crawled_products');
        $select->group('main_table.site_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('site_id', array(
            'header'    => $this->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'site_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Site name'),
            'width'     => '350px',
            'index'     => 'name',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Scrapinghub identifier'),
            'width'     => '350px',
            'index'     => 'identifier',
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Description'),
            'index'     => 'description',
        ));

        $this->addColumn('crawled_products', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Crawled products'),
            'width'     => '200px',
            'index'     => 'crawled_products',
            'filter'    => false,
        ));

        $this->addColumn('active', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Active'),
            'width'     => '150px',
            'index'     => 'active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $this->addColumn('show_category', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Show Category'),
            'width'     => '150px',
            'index'     => 'show_category',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
            ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}