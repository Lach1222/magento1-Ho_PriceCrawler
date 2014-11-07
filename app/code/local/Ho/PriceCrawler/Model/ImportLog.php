<?php

/**
 * Class Ho_PriceCrawler_Model_ImportLog
 *
 * @method setSiteId(int $value)
 * @method int getSiteId()
 * @method setStartDate(string $value)
 * @method string getStartDate()
 * @method setEndDate(string $value)
 * @method string getEndDate()
 * @method setImported(int $value)
 * @method int getImported()
 * @method setErrors(int $value)
 * @method int getErrors()
 * @method setMemoryUsage(int $value)
 * @method int getMemoryUsage()
 */
class Ho_PriceCrawler_Model_ImportLog extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ho_pricecrawler/importLog');
    }
}