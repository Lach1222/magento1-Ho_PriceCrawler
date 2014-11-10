<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('ho_pricecrawler_products_form');
        $this->setTitle($this->__('Product Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $productId = $this->getRequest()->getParam('id');

        $model = Mage::registry('ho_pricecrawler');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $productId)),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Product Information'),
            'class'     => 'fieldset-wide product-match-form',
        ));

        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'product_entity_id',
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Name'),
            'title'     => Mage::helper('checkout')->__('Name'),
            'required'  => true,
            'readonly'  => true,
            'disabled'  => true,
        ));

        $searchMatchesUrl = $this->getUrl('ho_pricecrawler/adminhtml_products/search', array('id' => $productId));
        $fieldset->addField('search_string', 'text', array(
            'name'      => 'search_string',
            'label'     => Mage::helper('ho_pricecrawler')->__('Manual search'),
            'title'     => Mage::helper('ho_pricecrawler')->__('Manual search'),
            'after_element_html' => '
                <button id="search-matches" type="button" class="search-matches-button show-hide"
                    onclick="searchMatches(\'' . $searchMatchesUrl . '\')">
                        <span><span>' . $this->__('Manually search for matches') . '</span></span>
                </button>
                <div id="search-error"></div>
            ',
        ));
        // Set value of search string
        $model->setSearchString($model->getName());

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);

        // Mage_Catalog_Model_Product_Image throws an exception when an image is not found
        try {
            $imageUrl = Mage::helper('catalog/image')->init($product, 'small_image');
        } catch (Exception $e) {
            $imageUrl = false;
        }

        $note = $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_products_edit_productInfo')
            ->setIsOriginalProduct(true)
            ->setHeader($this->__('Product information:'))
            ->setName($product->getName())
            ->setSku($product->getSku())
            ->setPrice(!is_null($product->getFinalPrice()) ? Mage::helper('core')->currency($product->getFinalPrice(), true, false) : false)
            ->setImageUrl($imageUrl)
            ->_toHtml() .
            '<label class="result-query">' . Mage::helper('ho_pricecrawler')->__('Listing results for:') . '</label>' .
            '<label for="search_string" class="result-query" id="result-query">' . $product->getName() . '</span>' .
            '<div class="last-note"></div>';

        $fieldset->addField('note', 'note', array(
            'text' => $note,
        ));

        /** @var Ho_PriceCrawler_Helper_Product $productHelper */
        $productHelper = Mage::helper('ho_pricecrawler/product');
        $productMatches = $productHelper->getProductMatches($model->getName());
        foreach ($productMatches as $site) {
            $matches = $productHelper->createMatchesDropdown($site, $productId);
            $matchedProduct = $productHelper->getMatchedProduct($productId, $site->getId());

            $productMatchField[$site->getId()] = $fieldset->addField('product_match_' . $site->getId(), 'select', array(
                'name'      => 'product_match_' . $site->getId(),
                'label'     => Mage::helper('ho_pricecrawler')->__('Product Match - %s', $site->getName()),
                'required'  => true,
                'value'     => '1',
                'values'    => $matches,
                'onchange'  => 'selectedProductInfo(\'' . $this->getUrl('ho_pricecrawler/adminhtml_products/getCrawledProduct') . '\',' . $site->getId() . ')',
            ));

            $noteHtml = $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_products_edit_productInfo')
                ->setId('selected_info_' . $site->getId())
                ->setClass('selected_info hidden')
                ->setHeader($this->__('Currently selected match:'))
                ->setName('<span class="product_name"></span>')
                ->setSku('<span class="product_identifier"></span>')
                ->setPrice('<span class="product_price"></span>')
                ->setDateProductUpdated('<span class="product_date_product_updated"></span>')
                ->setDatePriceUpdated('<span class="product_date_price_updated"></span>')
                ->setImageUrl(true)
                ->setProductUrl(true)
                ->_toHtml();
            $fieldset->addField('note_selected_' . $site->getId(), 'note', array(
                'text' => $noteHtml,
            ));

            $customMatchField[$site->getId()] = $fieldset->addField('product_identifier_' . $site->getId(), 'text', array(
                'name'      => 'product_identifier_' . $site->getId(),
                'label'     => Mage::helper('checkout')->__('Product Identifier %s', $site->getName()),
                'title'     => Mage::helper('checkout')->__('Product Identifier %s', $site->getName()),
                'required'  => true,
            ));

            if ($matchedProduct) {
                $fieldset->addField('delete_match_' . $site->getId(), 'checkbox', array(
                    'name'      => 'delete_match_' . $site->getId(),
                    'label'     => Mage::helper('ho_pricecrawler')->__('Delete match'),
                    'value'     => '1',
                    'after_element_html' => '<small>' . Mage::helper('ho_pricecrawler')->__('Check this box to delete the match for this site.') . '</small>',
                ));
                $model->setData('delete_match_' . $site->getId(), 1);

                $productNote =
                    $this->getLayout()->createBlock('ho_pricecrawler/adminhtml_products_edit_productInfo')
                    ->setHeader($this->__('Currently selected match:'))
                    ->setName($matchedProduct->getName())
                    ->setSku($matchedProduct->getProductIdentifier())
                    ->setPrice(!is_null($matchedProduct->getPrice()) ? Mage::helper('core')->currency($matchedProduct->getPrice(), true, false) : false)
                    ->setImageUrl($matchedProduct->getImage())
                    ->setProductUrl($matchedProduct->getUrl())
                    ->setDateProductUpdated(Mage::helper('ho_pricecrawler')->formatDate($matchedProduct->getDateProductUpdated()))
                    ->setDatePriceUpdated(Mage::helper('ho_pricecrawler')->formatDate(($matchedProduct->getDatePriceUpdated())))
                    ->_toHtml() .
                    '<div class="match-note last-note">' . $this->__('Note: Name and price can be empty, when a product identifier is manually entered and the product is not crawled yet.') . '</div>';
            }
            else {
                $productNote = $this->__('Select a product from the list, or choose \'Enter manually\' to enter a article number.') . '<br />' .
                    '<div class="last-note"><small>' . $this->__('There is no match saved for this product yet.') . '</small></div>';
            }

            $fieldset->addField('note_' . $site->getId(), 'note', array(
                'text' => $productNote,
            ));
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        // Add dependencies
        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $dependenceBlock */
        $dependenceBlock = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        foreach (Mage::getModel('ho_pricecrawler/sites')->getActiveSites() as $site) {
            $siteId = $site->getId();
            $dependenceBlock
                ->addFieldMap($productMatchField[$siteId]->getHtmlId(), $productMatchField[$siteId]->getName())
                ->addFieldMap($customMatchField[$siteId]->getHtmlId(), $customMatchField[$siteId]->getName())
                ->addFieldDependence(
                    $customMatchField[$siteId]->getName(),
                    $productMatchField[$siteId]->getName(),
                    'manual'
                );
        }
        $this->setChild('form_after', $dependenceBlock);

        return parent::_prepareForm();
    }
}