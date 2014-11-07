<?php

class Ho_PriceCrawler_Helper_Product extends Mage_Core_Helper_Abstract
{
    public function getNextProductId($productId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $select = $connection->select();
        $select->from('catalog_product_entity', 'entity_id');
        $select->where('entity_id > ?', $productId);
        $select->limit(1);

        $row = $connection->fetchRow($select);

        return $row['entity_id'];
    }

    public function getLowestPrice($productId)
    {
        $product = Mage::getModel('ho_pricecrawler/products')
            ->getCollection()
            ->addFieldToFilter('product_entity_id', $productId)
            ->addFieldToFilter('price', array('notnull' => true))
            ->addOrder('price', Varien_Data_Collection::SORT_ORDER_ASC)
            ->getFirstItem();

        return $product->getPrice();
    }

    public function getAveragePrice($productId)
    {
        $collection = Mage::getModel('ho_pricecrawler/products')
            ->getCollection()
            ->addFieldToFilter('product_entity_id', $productId);
        $select = $collection->getSelect();
        $select->columns('AVG(price) AS average_price');

        $product = $collection->getFirstItem();

        return $product->getAveragePrice();
    }

    public function getProductMatches($searchQuery)
    {
        $sites = Mage::getModel('ho_pricecrawler/sites')->getActiveSites();

        // Strip MySQL Match characters
        $stripCharacters = array('-', '+', '~', '*');
        $searchQuery = str_replace($stripCharacters, ' ', $searchQuery);

        // Make first word required and give more weight to second word in search string
        $search = explode(' ', $searchQuery);
        $query = '';
        $i = 1;
        foreach ($search as $s) {
            $operator = '';
            if ($i == 1) {
                $operator = '+';
            }
            elseif ($i == 2) {
                $operator = '>';
            }
            $query .= $operator . $s;
            $query .= $i < count($search) ? ' ' : '';
            $i++;
        }
        $searchQuery = $query;

        foreach ($sites as $site) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

            $select = $connection->select();

            $select->from('ho_pricecrawler_products', array(
                '*',
                'relevance' => $connection->quoteInto('MATCH(name) AGAINST(? IN BOOLEAN MODE)', $searchQuery)
            ));
            $select->where('product_entity_id IS NULL');
            $select->where('MATCH(name) AGAINST (? IN BOOLEAN MODE)', $searchQuery);
            $select->where('site_id = ?', $site->getId());

            $sorting = Mage::getStoreConfig('ho_pricecrawler/product_matching/sorting');
            if ($sorting == Ho_PriceCrawler_Model_System_Config_Source_Sorting::RELEVANCE) {
                $select->order('relevance DESC');
            }
            elseif ($sorting == Ho_PriceCrawler_Model_System_Config_Source_Sorting::ALPHABETICAL) {
                $select->order('name ASC');
            }
            $select->order('site_id', 'ASC');

            $rows = $connection->fetchAll($select);

            $site->setProducts($rows);
        }

        return $sites;
    }

    /**
     * @param int $productId
     * @param int $siteId
     * @return Ho_PriceCrawler_Model_Products
     */
    public function getMatchedProduct($productId, $siteId)
    {
        $match = Mage::getModel('ho_pricecrawler/products')
            ->getCollection()
            ->addFieldToFilter('product_entity_id', $productId)
            ->addFieldToFilter('site_id', $siteId)
            ->getFirstItem();

        return $match->getId() ? $match : false;
    }

    /**
     * @param Ho_PriceCrawler_Model_Sites $site
     * @param int $productId
     * @return array
     */
    public function createMatchesDropdown($site, $productId)
    {
        $matchedProduct = $this->getMatchedProduct($productId, $site->getId());
        $matches = array();

        if ($matchedProduct) {
            $matches[0] = array(
                'label' => $this->__('Currently saved match'),
                'value' => array(array(
                    'value' => $matchedProduct->getId(),
                    'label' => $matchedProduct->getProductIdentifier()
                        . ' — ' . $matchedProduct->getName()
                        . ($site->getShowCategory() ? ' — ' . $matchedProduct->getCategory() : ''),
                ))
            );
        }
        else {
            $matches[] = '';
        }
        $foundMatches = $otherOptions = array();
        foreach ($site->getProducts() as $match) {
            $label = $match['name'];
            $label .= $site->getShowCategory() ? ' — ' . $match['category'] : '';
            $foundMatches[] = array('value' => $match['product_id'], 'label' => $label);
        }

        $otherOptions[] = array('value' => 'manual', 'label' => $this->__('Enter manually'));

        $matches[] = array('label' => $this->__('Proposed products'), 'value' => $foundMatches);
        $matches[] = array('label' => $this->__('Other options'), 'value' => $otherOptions);

        return $matches;
    }
}