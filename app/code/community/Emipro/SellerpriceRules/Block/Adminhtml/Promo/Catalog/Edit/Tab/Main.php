<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @Author Emipro Technologies Private Limited
 * @Category Emipro
 * @Package  Emipro_SellerpriceRules
 * @License http://shop.emiprotechnologies.com/license-agreement/
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_SellerpriceRules_Block_Adminhtml_Promo_Catalog_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalogrule')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_catalog_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

         $admin_info = Mage::getSingleton('admin/session');
		$SellerAdminId = $admin_info->getUser()->getUserId();
        
        $fieldset = $form->addFieldset('base_fieldset',
            array('legend '=> Mage::helper('catalogrule')->__('General Information'))
        );

        $fieldset->addField('auto_apply', 'hidden', array(
            'name' => 'auto_apply',
        ));

		
		if($model->getSellerAdminId())
		{
		 $fieldset->addField('seller_admin_id', 'hidden', array(
                'name'     => 'seller_admin_id',
               'label'    => $model->getSellerAdminId()
            ));
		 $model->setSellerAdminId($model->getSellerAdminId());
		}
		else
		{
			$fieldset->addField('seller_admin_id', 'hidden', array(
                'name'     => 'seller_admin_id',
               'label'    => $SellerAdminId
            ));
		 $model->setSellerAdminId($SellerAdminId);
		
		}
		 
        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('catalogrule')->__('Rule Name'),
            'title' => Mage::helper('catalogrule')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('catalogrule')->__('Description'),
            'title' => Mage::helper('catalogrule')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Status'),
            'title'     => Mage::helper('catalogrule')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('catalogrule')->__('Active'),
                '0' => Mage::helper('catalogrule')->__('Inactive'),
            ),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'     => Mage::helper('catalogrule')->__('Websites'),
                'title'     => Mage::helper('catalogrule')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('catalogrule')->__('From Date'),
            'title'  => Mage::helper('catalogrule')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('catalogrule')->__('To Date'),
            'title'  => Mage::helper('catalogrule')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('catalogrule')->__('Priority'),
        ));

	
        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
