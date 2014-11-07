<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_useSpecialPrices;

    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setDefaultFilter(array('matched' => 1));
        $this->setId('ho_pricecrawler_products_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('special_price');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        $select = $collection->getSelect();

        $useSpecialPrices = $this->_useSpecialPrices();

        // Join product price so we can calculate price difference
        $productPrice = Mage::getSingleton('eav/config')->getAttribute('catalog_product','price');
        $select->join($productPrice->getBackendTable() . ' AS product_attribute',
            'e.entity_id = product_attribute.entity_id AND product_attribute.attribute_id = ' . $productPrice->getId(),
            array('value AS product_price')
        );

        if ($useSpecialPrices) {
            // Join special price for price difference calculation
            $productSpecialPrice = Mage::getSingleton('eav/config')->getAttribute('catalog_product','special_price');
            $select->joinLeft($productSpecialPrice->getBackendTable() . ' AS product_attribute_special',
                'e.entity_id = product_attribute_special.entity_id AND product_attribute_special.attribute_id = ' . $productSpecialPrice->getId(),
                array('value AS product_special_price)')
            );
        }

        // Join average price and price difference between own and lowest price
        $select->joinLeft('ho_pricecrawler_products AS pcp', 'e.entity_id = pcp.product_entity_id', null);
        $select->columns(array(
            'COUNT(DISTINCT pcp.site_id) AS match_count',
            'AVG(pcp.price) AS average_price',
            ($useSpecialPrices
                ? 'IF (product_attribute_special.value > 0, product_attribute_special.value, product_attribute.value)'
                : 'product_attribute.value') .
            ' - MIN(pcp.price) AS price_difference',
        ));

        // Join lowest price with matching site ID
        $select->joinLeft(
            'ho_pricecrawler_products AS prices',
            'e.entity_id = prices.product_entity_id
            AND prices.price = (SELECT min(price) FROM ho_pricecrawler_products WHERE product_entity_id = e.entity_id)',
            array('prices.site_id AS lowest_site_id', 'prices.price AS lowest_price', 'prices.date_price_updated AS lowest_price_updated', 'prices.product_entity_id')
        );
        // Join site name of lowest site
        $select->joinLeft(
            'ho_pricecrawler_sites AS sites',
            'prices.site_id = sites.site_id',
            array('name AS lowest_site_name', 'site_id AS lowest_site_id')
        );

        $select->group('e.entity_id');

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();

        $this->addColumn('id', array(
            'header'=> $this->__('ID'),
            'align' =>'right',
            'width' => '50px',
            'index' => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'=> Mage::helper('ho_pricecrawler')->__('Productname'),
            'index' => 'name'
        ));

        $this->addColumn('sku', array(
            'header'=> Mage::helper('ho_pricecrawler')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
        ));

        $this->addColumn('matched', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Matched'),
            'width'     => '50px',
            'index'     => 'product_entity_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_Matched',
            'filter_condition_callback' => array($this, '_filterMatchedColumn'),
            'order_callback'            => array($this, '_orderMatchedColumn'),
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Own Price'),
            'index'     => 'price',
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
        ));

        $this->addColumn('special_price', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Own Special Price'),
            'index'     => 'special_price',
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'sortable'  => false,
        ));

        $this->addColumn('lowest_price', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Lowest price'),
            'width'     => '80px',
            'index'     => 'lowest_price',
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('lowest_price_site', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Lowest price site'),
            'width'     => '80px',
            'index'     => 'lowest_site_name',
            'type'      => 'options',
            'options'   => Mage::helper('ho_pricecrawler/sites')->getOptionsArray(),
            'filter_condition_callback' => array($this, '_filterLowestSiteName'),
            'sortable'  => false,
        ));

        $this->addColumn('lowest_price_updated', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Lowest price last updated'),
            'width'     => '140px',
            'index'     => 'lowest_price_updated',
            'type'      => 'datetime',
            'order_callback' => array($this, '_defaultColumnOrderMethod'),
            'filter_condition_callback' => array($this, '_filterLowestPriceUpdated'),
        ));

        $this->addColumn('match_count', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Matches'),
            'width'     => '20px',
            'index'     => 'match_count',
            'filter'    => false,
            'order_callback' => array($this, '_defaultColumnOrderMethod'),
        ));

        $this->addColumn('average_price', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Average price'),
            'index'     => 'average_price',
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('price_difference', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Price difference'),
            'width'     => '50px',
            'index'     => 'price_difference',
            'type'      => 'price',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_PriceDifference',
            'align'     => 'right',
            'filter'    => false,
            'order_callback' => array($this, '_defaultColumnOrderMethod'),
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('ho_pricecrawler')->__('Change price'),
            'width'     => '130px',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('ho_pricecrawler')->__('Set lowest price'),
                    'url'     => array(
                        'base'=>'*/*/setLowestPrice',
                    ),
                    'field'   => 'id',
                    'confirm' => Mage::helper('ho_pricecrawler')->__('Are you sure you want to set the lowest price for this product?'),
                ),
                array(
                    'caption' => Mage::helper('ho_pricecrawler')->__('Set average price'),
                    'url'     => array(
                        'base'=> '*/*/setAveragePrice',
                    ),
                    'field'   => 'id',
                    'confirm' => Mage::helper('ho_pricecrawler')->__('Are you sure you want to set the average price for this product?'),
                )
            ),
            'renderer'  => 'Ho_PriceCrawler_Block_Adminhtml_Products_Renderer_Actions',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('edit_product', array(
            'width'     => '100px',
            'type'      => 'action',
            'getter'    => 'getEntityId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('ho_pricecrawler')->__('Edit product'),
                    'url'       => array(
                        'base'  => 'adminhtml/catalog_product/edit',
                    ),
                    'field'     => 'id',
                )
            ),
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('match_product', array(
            'width'     => '100px',
            'type'      => 'action',
            'getter'    => 'getEntityId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('ho_pricecrawler')->__('Match product'),
                    'url'       => array(
                        'base'  => 'ho_pricecrawler/adminhtml_products/edit',
                    ),
                    'field'     => 'id',
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

    /**
     * Sort column by column index name
     *
     * @param $collection
     * @param $column
     */
    protected function _defaultColumnOrderMethod($collection, $column)
    {
        $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }

    /**
     * Filter 'matched' (yes/no) column
     *
     * @param $collection
     * @param $column
     */
    protected function _filterMatchedColumn($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        $condition = $value ? 'IS NOT NULL' : 'IS NULL';

        $collection->getSelect()->where('prices.product_entity_id ' . $condition);
    }

    /**
     * Sort 'matched' (yes/no) column
     *
     * @param $collection
     * @param $column
     */
    protected function _orderMatchedColumn($collection, $column)
    {
        $collection->getSelect()->order('prices.product_entity_id ' . strtoupper($column->getDir()));
    }

    /**
     * Filter 'lowest_price_updated' column
     *
     * @param $collection
     * @param $column
     * @return bool
     */
    protected function _filterLowestPriceUpdated($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (!is_array($value)) return false;

        $from = array_key_exists('orig_from', $value) ? $value['orig_from'] : false;
        $to = array_key_exists('orig_to', $value) ? $value['orig_to'] : false;

        if ($from) {
            $from = date('Y-m-d', strtotime($from)) . ' 00:00:00';
            $collection->getSelect()->where('prices.date_price_updated > ?', $from);
        }
        if ($to) {
            $to = date('Y-m-d', strtotime($to)) . ' 23:59:59';
            $collection->getSelect()->where('prices.date_price_updated < ?', $to);
        }
    }

    /**
     * Filter 'lowest_site_name' column
     *
     * @param $collection
     * @param $column
     */
    protected function _filterLowestSiteName($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->getSelect()->where('sites.site_id = ?', $value);
    }

    /**
     * Set sorting order of custom sorting columns
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this|Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        if ($column->getOrderCallback()) {
            call_user_func($column->getOrderCallback(), $this->getCollection(), $column);

            return $this;
        }

        return parent::_setCollectionOrder($column);
    }

    /**
     * Check if special prices must be used to calculate price differences
     *
     * @return bool
     */
    protected function _useSpecialPrices()
    {
        if (!$this->_useSpecialPrices) {
            $useSpecialPrice = Mage::getStoreConfig(Ho_PriceCrawler_Model_Products::XML_PATH_USE_SPECIAL_PRICES);
            $this->_useSpecialPrices = $useSpecialPrice;
        }

        return $this->_useSpecialPrices;
    }
}