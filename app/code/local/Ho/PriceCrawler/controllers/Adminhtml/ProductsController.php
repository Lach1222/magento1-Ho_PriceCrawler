<?php

class Ho_PriceCrawler_Adminhtml_ProductsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $this->_initAction();

        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalog/product');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This product no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('Edit product'));

        $data = Mage::getSingleton('adminhtml/session')->getProductData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('ho_pricecrawler', $model);

        $this->_addBreadcrumb($id ? $this->__('Edit product') : $this->__('New product'), $id ? $this->__('Edit product') : $this->__('New product'))
            ->_addContent($this->getLayout()->createBlock('ho_pricecrawler/adminhtml_products_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function searchAction()
    {
        $response = array();
        $searchQuery = $this->getRequest()->getParam('query');
        $productId = $this->getRequest()->getParam('id');

        try {
            /** @var Ho_PriceCrawler_Helper_Product $productHelper */
            $productHelper = Mage::helper('ho_pricecrawler/product');
            $productMatches = $productHelper->getProductMatches($searchQuery);
            foreach ($productMatches as $site) {
                $matches = $productHelper->createMatchesDropdown($site, $productId);
                $matchedProduct = $productHelper->getMatchedProduct($productId, $site->getId());
                $response[] = array(
                    'site_id'       => $site->getId(),
                    'matches'       => $matches,
                    'matchedProduct'=> $matchedProduct,
                );
            }
            $response = json_encode($response);
        }
        catch(Exception $e) {
            $response = $e->getMessage();
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($response);
    }

    public function getCrawledProductAction()
    {
        $productId = $this->getRequest()->getParam('id');

        $product = Mage::getModel('ho_pricecrawler/products')
            ->load($productId);

        $product->setPrice(Mage::helper('core')->currency($product->getPrice(), true, false));

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($product->getData()));
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $redirectNext = $this->getRequest()->getParam('next');
            $redirectStay = $this->getRequest()->getParam('stay');
            $productId = $postData['product_entity_id'];
            $sites = Mage::getModel('ho_pricecrawler/sites')->getActiveSites();

            try {
                // Save Magento product ID at crawled product entries
                foreach ($sites as $site) {
                    $matchProductId = $postData['product_match_' . $site->getId()];
                    $deleteMatch = array_key_exists('delete_match_' . $site->getId(), $postData)
                        ? $postData['delete_match_' . $site->getId()]
                        : false;

                    // Remove old link, if there is one
                    $oldItem = Mage::getModel('ho_pricecrawler/products')
                        ->getCollection()
                        ->addFieldToFilter('product_entity_id', $productId)
                        ->addFieldToFilter('site_id', $site->getId())
                        ->getFirstItem();

                    if ($oldItem->getId()) {
                        $oldItem->setProductEntityId(null);
                        $oldItem->save();
                        // If match must be deleted, stop here
                        if ($deleteMatch) continue;
                    }

                    // No value, don't save
                    if ($matchProductId == '0') continue;

                    // Create new entry based on manually entered product identifier
                    if ($matchProductId == 'manual') {
                        $productIdentifier = $postData['product_identifier_' . $site->getId()];

                        // Check if product with this identifier already exists
                        $product = Mage::getModel('ho_pricecrawler/products')
                            ->getCollection()
                            ->addFieldToFilter('product_identifier', $productIdentifier)
                            ->addFieldToFilter('site_id', $site->getId())
                            ->getFirstItem();
                        if ($product->getId()) {
                            $product->setProductEntityId($productId);
                            $product->save();
                        }
                        else {
                            $model = Mage::getModel('ho_pricecrawler/products')
                                ->setProductIdentifier($productIdentifier)
                                ->setDescription($this->__('Created via PriceCrawler in Magento'))
                                ->setProductEntityId($productId)
                                ->setSiteId($site->getId());
                            $model->save();
                        }
                    }
                    // Load entry and save Magento product ID
                    else {
                        $model = Mage::getModel('ho_pricecrawler/products')
                            ->load($matchProductId)
                            ->setProductEntityId($productId);
                        $model->save();
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The product matches have been saved.'));
                if ($redirectNext) {
                    $nextProductId = Mage::helper('ho_pricecrawler/product')->getNextProductId($productId);
                    $this->_redirect('*/*/edit', array('id' => $nextProductId));
                }
                elseif ($redirectStay) {
                    $this->_redirect('*/*/edit', array('id' => $productId));
                }
                else {
                    $this->_redirect('*/*/');
                }
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving these product matches.'));
            }

            $this->_redirectReferer();
        }
    }

    public function setLowestPriceAction()
    {
        $id = $this->getRequest()->getParam('id');
        $price = Mage::helper('ho_pricecrawler/product')->getLowestPrice($id);

        $this->_setProductPrice($price);
    }

    public function setAveragePriceAction()
    {
        $id = $this->getRequest()->getParam('id');
        $price = Mage::helper('ho_pricecrawler/product')->getAveragePrice($id);

        $this->_setProductPrice($price);
    }

    protected function _setProductPrice($price)
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalog/product');

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This product no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        try {
            $model->setPrice($price);
            $model->save();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The new product price has been saved'));
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this product.'));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ho_pricecrawler/ho_pricecrawler_products')
            ->_title($this->__('PriceCrawler'))->_title($this->__('Products'))
            ->_addBreadcrumb($this->__('PriceCrawler'), $this->__('PriceCrawler'))
            ->_addBreadcrumb($this->__('Products'), $this->__('Products'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ho_pricecrawler/ho_pricecrawler_products');
    }
}