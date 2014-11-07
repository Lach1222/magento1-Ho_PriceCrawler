<?php

/**
 * Class Ho_PriceCrawler_Model_Products
 *
 * @method setProductId(int $value)
 * @method int getProductId()
 * @method setSiteId(int $value)
 * @method int getSiteId()
 * @method setProductIdentifier(string $value)
 * @method string getProductIdentifier()
 * @method setName(string $value)
 * @method string getName()
 * @method setDescription(string $value)
 * @method string getDescription()
 * @method setCategory(string $value)
 * @method string getCategory()
 * @method setOriginalPrice(string $value)
 * @method string getOriginalPrice()
 * @method setOriginalSpecialPrice(string $value)
 * @method string getOriginalSpecialPrice()
 * @method setPrice(float $value)
 * @method float getPrice()
 * @method setProductEntityId(int $value)
 * @method int getProductEntityId()
 * @method setImage(string $value)
 * @method string getImage()
 * @method setStock(string $value)
 * @method string getStock()
 * @method setUrl(string $value)
 * @method string getUrl()
 * @method setDatePriceUpdated(string $value)
 * @method string getDatePriceUpdated()
 * @method setDateProductUpdated(string $value)
 * @method string getDateProductUpdated()
 */
class Ho_PriceCrawler_Model_Products extends Mage_Core_Model_Abstract
{
    const XML_PATH_PRICE_DIFFERENCE_PERCENTAGE  = 'ho_pricecrawler/products_overview/notice_price_difference_percentage';
    const XML_PATH_USE_SPECIAL_PRICES           = 'ho_pricecrawler/products_overview/use_special_prices';

    protected function _construct()
    {
        $this->_init('ho_pricecrawler/products');
    }
}