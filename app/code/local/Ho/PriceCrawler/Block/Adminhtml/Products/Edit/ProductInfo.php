<?php

/**
 * Class Ho_PriceCrawler_Block_Adminhtml_Products_Edit_ProductInfo
 *
 * @method string getId()
 * @method setId(string $value)
 * @method string getClass()
 * @method setClass(string $value)
 * @method string getHeader()
 * @method setHeader(string $value)
 * @method string getName()
 * @method setName(string $value)
 * @method string getSku()
 * @method setSku(string $value)
 * @method string getPrice()
 * @method setPrice(string $value)
 * @method string getImageUrl()
 * @method setImageUrl(string $value)
 * @method string getProductUrl()
 * @method setProductUrl(string $value)
 */
class Ho_PriceCrawler_Block_Adminhtml_Products_Edit_ProductInfo extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ho/pricecrawler/product_info.phtml');
    }
}