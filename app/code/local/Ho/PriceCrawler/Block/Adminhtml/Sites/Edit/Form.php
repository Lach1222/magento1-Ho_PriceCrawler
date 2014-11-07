<?php

class Ho_PriceCrawler_Block_Adminhtml_Sites_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('ho_pricecrawler_sites_form');
        $this->setTitle($this->__('Site Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('ho_pricecrawler');
        $helper = Mage::helper('ho_pricecrawler');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $helper->__('Site Information'),
            'class'     => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('site_id', 'hidden', array(
                'name' => 'site_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $helper->__('Name'),
            'required'  => true,
            'after_element_html' => '<p><small>' . $helper->__('Enter a free to choose name') . '</small></p>',
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => $helper->__('Scrapinghub identifier'),
            'required'  => true,
            'after_element_html' => '<p><small>' .
                $helper->__('This is found under \'Spiders\' when logged in at Scrapinghub, ' .
                    'under the first column in the overview (\'Spider\')') . '</small></p>',
        ));

        $fieldset->addField('active', 'select', array(
            'name'      => 'active',
            'label'     => $helper->__('Active'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'after_element_html' => '<p><small>' .
                $helper->__('When a site is inactive, it will not schedule jobs for this ' .
                    'spider and not import any found products.') . '</small></p>',
        ));

        $fieldset->addField('description', 'text', array(
            'name'      => 'description',
            'label'     => $helper->__('Description'),
            'required'  => false,
            'after_element_html' => '<p><small>' . $helper->__('Enter a free to choose description') . '</small></p>',
        ));

        $options = $form->addFieldset('options_fieldset', array(
            'legend'    => $helper->__('Options'),
            'class'     => 'fieldset-wide',
        ));

        $options->addField('show_category', 'select', array(
            'name'      => 'show_category',
            'label'     => $helper->__('Show Category'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'after_element_html' => '<p><small>' .
                $helper->__("Show the product category after the product name in the 'Product Match' list") . '</small></p>',
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}