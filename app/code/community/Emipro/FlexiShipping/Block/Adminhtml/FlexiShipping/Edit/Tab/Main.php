<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit_Tab_Main
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
        return Mage::helper('emipro_flexishipping')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('emipro_flexishipping')->__('Rule Information');
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
        $model = Mage::registry('flexishipping');
     
       $admin_info = Mage::getSingleton('admin/session');
		$SellerId = $admin_info->getUser()->getUserId();
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('rule_');
		$fieldset = $form->addFieldset('base_fieldset',
            array('legend '=> Mage::helper('emipro_flexishipping')->__('General Information'))
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
               'label'    => $SellerId
            ));
		 $model->setSellerAdminId($SellerId);
		
		}

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('emipro_flexishipping')->__('Rule Name'),
            'title' => Mage::helper('emipro_flexishipping')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('emipro_flexishipping')->__('Description'),
            'title' => Mage::helper('emipro_flexishipping')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('emipro_flexishipping')->__('Status'),
            'title'     => Mage::helper('emipro_flexishipping')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('emipro_flexishipping')->__('Active'),
                '0' => Mage::helper('emipro_flexishipping')->__('Inactive'),
            ),
        ));

       if (Mage::app()->isSingleStoreMode()) {
          $websiteId = Mage::app()->getStore(true)->getWebsiteId();
          $fieldset->addField('website_id', 'hidden', array(
                'name'     => 'website_id[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteId($websiteId);
        } else {
            $field = $fieldset->addField('website_id', 'multiselect', array(
                'name'     => 'website_id[]',
                'label'     => Mage::helper('emipro_flexishipping')->__('Websites'),
                'title'     => Mage::helper('emipro_flexishipping')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
           
        }

        $fieldset->addField('customer_group_id', 'multiselect', array(
            'name'      => 'customer_group_id[]',
            'label'     => Mage::helper('emipro_flexishipping')->__('Customer Groups'),
            'title'     => Mage::helper('emipro_flexishipping')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('emipro_flexishipping')->__('From Date'),
            'title'  => Mage::helper('emipro_flexishipping')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('emipro_flexishipping')->__('To Date'),
            'title'  => Mage::helper('emipro_flexishipping')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('emipro_flexishipping')->__('Priority'),
            'note'=>'Less Priority rule applied first.If multiple rule with same priority will be applied, then first matched rule will be applied.',
        ));
	
        $form->setValues($model->getData());
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
