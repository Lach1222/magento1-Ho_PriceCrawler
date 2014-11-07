<?php

class Ho_PriceCrawler_Adminhtml_SitesController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();

        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ho_pricecrawler/sites');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This site no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New Site'));

        $data = Mage::getSingleton('adminhtml/session')->getSiteData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('ho_pricecrawler', $model);

        $this->_addBreadcrumb($id ? $this->__('Edit Site') : $this->__('New Site'), $id ? $this->__('Edit Site') : $this->__('New Site'))
            ->_addContent($this->getLayout()->createBlock('ho_pricecrawler/adminhtml_sites_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('ho_pricecrawler/sites');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The site has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this site.'));
            }

            Mage::getSingleton('adminhtml/session')->setSitesData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $site = Mage::getModel('ho_pricecrawler/sites')->load($id);

            try {
                $site->delete();
                $this->_getSession()->addSuccess($this->__('The site has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
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
            ->_setActiveMenu('ho_pricecrawler/ho_pricecrawler_sites')
            ->_title($this->__('PriceCrawler'))->_title($this->__('Sites'))
            ->_addBreadcrumb($this->__('PriceCrawler'), $this->__('PriceCrawler'))
            ->_addBreadcrumb($this->__('Sites'), $this->__('Sites'));

        return $this;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ho_pricecrawler/ho_pricecrawler_sites');
    }
}