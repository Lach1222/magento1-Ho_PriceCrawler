<?php

class Ho_PriceCrawler_Block_Adminhtml_Products_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'ho_pricecrawler';
        $this->_controller = 'adminhtml_products';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('ho_pricecrawler')->__('Save and back to grid'));
        $this->_updateButton('save', 'class', 'save');
        $this->_addButton('save_stay', array(
                'label' => Mage::helper('ho_pricecrawler')->__('Save Matches'),
                'class' => 'save',
                'onclick'   => 'saveAndStayOnPage()',
            ), 0
        );
        $this->_addButton('save_next', array(
                'label' => Mage::helper('ho_pricecrawler')->__('Save And Go To Next'),
                'class' => 'save',
                'onclick'   => 'saveAndGoToNext()',
            ), 1
        );
        $this->_addButton('next', array(
                'label' => Mage::helper('ho_pricecrawler')->__('Next without save'),
                'class' => 'back go',
                'onclick' => "window.location.href = '" .
                    Mage::getUrl('*/hopricecrawler_products/edit', array('id' =>
                        Mage::helper('ho_pricecrawler/product')->getNextProductId(Mage::registry('ho_pricecrawler')->getId())
                    )) . "'",
            ), 2
        );
        $this->_removeButton('delete');

        $this->_formScripts[] = "
            function saveAndGoToNext(){
                editForm.submit($('edit_form').action+'next/1/');
            }
            function saveAndStayOnPage() {
                editForm.submit($('edit_form').action+'stay/1/');
            }
        ";
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('ho_pricecrawler')->getId()) {
            return $this->__('Edit Product');
        }
        else {
            return $this->__('New Product');
        }
    }
}
