<?php

class Ho_PriceCrawler_Model_Scrapinghub_Items extends Ho_PriceCrawler_Model_Scrapinghub_Abstract
{
    const PRICE_UPDATED_COLUMN = 'date_price_updated';
    const PRODUCT_UPDATED_COLUMN = 'date_product_updated';

    protected $_headers = array(
        'url',
        'name',
        'product_identifier',
        'description',
        'category',
        'price',
        'special_price',
        'stock',
        'image',
    );
    protected $_updateHeaders = array(
        'name',
        'description',
        'category',
        'original_price',
        'original_special_price',
        'price',
        'stock',
        'image',
    );

    protected $_matchColumns = array(
        'url',
        'product_identifier',
    );

    protected $_spider = null;

    /**
     * Import products from the last finished Scrapinghub job of the given identifier into the database.
     *
     * @param string $spiderIdentifier
     * @return mixed
     */
    public function import($spiderIdentifier)
    {
        $startMemoryUsage = memory_get_usage(true);

        /** @var Ho_PriceCrawler_Model_ImportLog $log */
        $log = Mage::getModel('ho_pricecrawler/importLog');
        $log->setSiteId($this->_getSpiderId($spiderIdentifier));
        $log->setStartDate(date('Y-m-d H:i:s'));

        $result = $this->get('items',
            array(
                'spider' => $spiderIdentifier,
                'include_headers' => true,
                'fields' => implode(',', $this->_headers),
            ),
            'csv'
        );

        $path = Mage::getBaseDir('var') . DS . 'pricecrawler';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $fileName = $path . DS . $spiderIdentifier . '.csv';

        file_put_contents($fileName, $result);

        $updateDate = date('Y-m-d H:i:s');

        $imported = $errors = 0;
        if (($handle = fopen($fileName, 'r')) !== false) {
            $i = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $i++;
                if ($i == 1) continue; // Skip header

                $item = $this->_getFormattedItem($data, $spiderIdentifier);

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

                $productRow = false;
                $additionalUpdateColumns = $this->_matchColumns;
                foreach ($this->_matchColumns as $key => $matchColumn) {
                    // Check on site_id and $matchColumn
                    $productRow = $this->_fetchProduct($connection, array('site_id', $matchColumn), $item);

                    if ($productRow) {
                        unset($additionalUpdateColumns[$key]);
                        break;
                    }
                }

                try {
                    if ($productRow) {
                        // Product row exists, update the data
                        $updateValues = array();
                        $updateHeaders = $this->_updateHeaders;
                        // Also update the match columns (except the one that is currently matched on)
                        $updateHeaders = array_merge($updateHeaders, $additionalUpdateColumns);
                        foreach ($updateHeaders as $header) {
                            $updateValues[$header] = $item[$header];
                        }

                        // Update date
                        $updateValues[self::PRODUCT_UPDATED_COLUMN] = $updateDate;

                        $oldPrice = $productRow['price'];
                        // Only update price update date column when price differs
                        if ($item['price'] != $oldPrice) {
                            $updateValues[self::PRICE_UPDATED_COLUMN] = $updateDate;
                        }

                        $where = array();
                        $where['site_id = ?'] = $item['site_id'];
                        $where[$matchColumn . ' = ?'] = $item[$matchColumn];

                        $connection->update('ho_pricecrawler_products', $updateValues, $where);
                        $imported++;
                    }
                    else {
                        // No product row found, insert new row
                        $item[self::PRODUCT_UPDATED_COLUMN] = $updateDate;
                        $item[self::PRICE_UPDATED_COLUMN] = $updateDate;

                        $connection->insert('ho_pricecrawler_products', $item);
                        $imported++;
                    }
                }
                catch (Exception $e) {
                    Mage::logException($e);
                    $errors++;
                }
            }
            fclose($handle);
        }

        $memoryUsage = memory_get_usage(true) - $startMemoryUsage;
        $log->setEndDate(date('Y-m-d H:i:s'));
        $log->setImported($imported);
        $log->setErrors($errors);
        $log->setMemoryUsage($memoryUsage);
        $log->save();

        return Mage::helper('ho_pricecrawler')->__('Imported/updated products from %s: %s (%s errors)', $spiderIdentifier, $imported, $errors);
    }

    /**
     * Retrieve a product by the given fields
     *
     * @param Varien_Db_Adapter_Interface $connection
     * @param array $fields 'where'-fields
     * @param array $item All item data
     * @return array
     */
    protected function _fetchProduct($connection, $fields, $item)
    {
        $select = $connection->select();
        $select->from('ho_pricecrawler_products');

        foreach ($fields as $field) {
            $select->where($field . ' = ?', $item[$field]);
        }

        $result = $connection->fetchRow($select);

        return $result;
    }

    /**
     * Formats items array
     * - Lowest value of special_price and price becomes price
     * - site_id is added
     *
     * @param array $data
     * @param string $spiderIdentifier
     * @return array
     */
    protected function _getFormattedItem($data, $spiderIdentifier)
    {
        $fields = array();
        foreach ($this->_headers as $key => $header) {
            $fields[$header] = $data[$key];
        }

        $originalPrice = $fields['price'];
        $originalSpecialPrice = $fields['special_price'];
        $price = $this->_formatPrice($fields['price']);
        $specialPrice = $this->_formatPrice($fields['special_price']);

        unset($fields['price']);
        unset($fields['special_price']);

        $spiderId = $this->_getSpiderId($spiderIdentifier);

        $fields['price'] = $specialPrice < $price && $specialPrice > 0 ? $specialPrice : $price;
        $fields['original_price'] = $originalPrice;
        $fields['original_special_price'] = $originalSpecialPrice;
        $fields['site_id'] = $spiderId;

        return $fields;
    }

    /**
     * Format price
     *
     * @param string $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        // convert "," to "."
        $price = str_replace(',', '.', $price);

        // remove everything except numbers and dot "."
        $price = preg_replace("/[^0-9\.]/", "", $price);

        // remove all separators from first part and keep the end
        $price = str_replace('.', '',substr($price, 0, -3)) . substr($price, -3);

        // return float
        return (float) $price;
    }

    /**
     * Gets spider ID by spider identifier
     *
     * @param string $spiderIdentifier
     * @return int|bool
     */
    protected function _getSpiderId($spiderIdentifier)
    {
        if (is_null($this->_spider)) {
            $spider = Mage::getModel('ho_pricecrawler/sites')
                ->getCollection()
                ->addFieldToFilter('identifier', $spiderIdentifier)
                ->getFirstItem();

            $this->_spider = $spider;
        }

        return $this->_spider->getId() ? $this->_spider->getId() : false;
    }
}