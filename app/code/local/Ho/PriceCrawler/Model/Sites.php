<?php

/**
 * Class Ho_PriceCrawler_Model_Sites
 *
 * @method setSiteId(int $value)
 * @method int getSiteId()
 * @method setName(string $value)
 * @method string getName()
 * @method setIdentifier(string $value)
 * @method string getIdentifier()
 * @method setActive(int $value)
 * @method int getActive()
 * @method setDescription(string $value)
 * @method string getDescription()
 * @method setShowCategory(int $value)
 * @method int getShowCategory()
 */
class Ho_PriceCrawler_Model_Sites extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ho_pricecrawler/sites');
    }

    /**
     * @return Ho_PriceCrawler_Model_Resource_Sites_Collection
     */
    public function getActiveSites()
    {
        $collection = $this
            ->getCollection()
            ->addFieldToFilter('active', true);

        return $collection;
    }
}