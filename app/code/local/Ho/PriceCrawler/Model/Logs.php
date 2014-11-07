<?php

/**
 * Class Ho_PriceCrawler_Model_Logs
 *
 * @method setSiteId(int $value)
 * @method int getSiteId()
 * @method setJobId(int $value)
 * @method string getJobId()
 * @method setDate(string $value)
 * @method string getDate()
 * @method setLevel(int $value)
 * @method int getLevel()
 * @method setMessage(string $value)
 * @method string getMessage()
 * @method setUrl(string $value)
 * @method string getUrl()
 * @method setIsItem(bool $value)
 * @method bool getIsItem()
 */
class Ho_PriceCrawler_Model_Logs extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ho_pricecrawler/logs');
    }
}