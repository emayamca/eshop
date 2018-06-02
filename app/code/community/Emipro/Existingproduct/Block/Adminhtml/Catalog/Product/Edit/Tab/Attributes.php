<?php
class Emipro_Existingproduct_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    /**
     * Load Wysiwyg on demand and prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')
            && Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
        ) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare attributes form
     *
     * @return null
     */
    protected function _prepareForm()
    {
        $group = $this->getGroup();
        if ($group) {
            $form = new Varien_Data_Form();

            // Initialize product object as form property to use it during elements generation
            $form->setDataObject(Mage::registry('product'));

            $fieldset = $form->addFieldset('group_fields' . $group->getId(), array(
                'legend' => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                'class' => 'fieldset-wide'
            ));

            $attributes = $this->getGroupAttributes();

            $this->_setProductFieldset($attributes, $fieldset, array('gallery'));

            $urlKey = $form->getElement('url_key');
            if ($urlKey) {
                $urlKey->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
                );
            }
            $sku = $form->getElement('sku');
            if ($sku) {
            $sku->setRenderer(
                $this->getLayout()->createBlock('existingproduct/adminhtml_catalog_product_edit_tab_attributes_extend')
                    ->setDisableChild(false)
            );
            }

            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                );
            }

            $groupPrice = $form->getElement('group_price');
            if ($groupPrice) {
                $groupPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_group')
                );
            }

            $recurringProfile = $form->getElement('recurring_profile');
            if ($recurringProfile) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_recurring')
                );
            }

            // Add new attribute button if it is not an image tab
            if (!$form->getElement('media_gallery')
                && Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')
            ) {
                $headerBar = $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_attributes_create');

                $headerBar->getConfig()
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $fieldset->setHeaderBar($headerBar->toHtml());
            }

            if ($form->getElement('meta_description')) {
                $form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
            }

            $values = Mage::registry('product')->getData();

            // Set default attribute values for new product
            if (!Mage::registry('product')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if (Mage::registry('product')->hasLockedAttributes()) {
                foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
                    $element = $form->getElement($attribute);
                    if ($element) {
                        $element->setReadonly(true, true);
                    }
                }
            }
            $form->addValues($values);
            $form->setFieldNameSuffix('product');

            Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form' => $form));

            $this->setForm($form);
        }
    }

    protected function _setProductFieldset($attributes, $fieldset, $exclude = array()) {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || !$attribute->getIsVisible()) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType()) && !in_array($attribute->getAttributeCode(), $exclude) && ('media_image' != $inputType)
            ) {

                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
                if ($sellerid && $sellerid != '') {
                    if($attribute->getManageBySeller() == 1){
                        $fieldType = 'hidden';
                    }
                    if ($attribute->getAttributeCode() == "esin") {
                        $configuration = array(
                            'name' => $attribute->getAttributeCode(),
                            'label' => __($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                            'note' => $attribute->getNote(),
                            'disabled' => 'disabled',
                        );
                    }else if ($attribute->getAttributeCode() == "marketplace_product_approve") {
                        $configuration = array(
                            'name' => $attribute->getAttributeCode(),
                            'label' => __($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                            'note' => $attribute->getNote(),
                            'disabled' => 'disabled',
                        );
                    } else if ($attribute->getAttributeCode() == "vendor_id") {
                        $configuration = array(
                            'name' => $attribute->getAttributeCode(),
                            'label' => __($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                            'note' => $attribute->getNote(),
                            'disabled' => 'disabled',
                        );
                    } else {
                        $configuration = array(
                            'name' => $attribute->getAttributeCode(),
                            'label' => __($attribute->getFrontend()->getLabel()),
                            'class' => $attribute->getFrontend()->getClass(),
                            'required' => $attribute->getIsRequired(),
                            'note' => $attribute->getNote(),
                        );
                    }
                } else {
                    $configuration = array(
                        'name' => $attribute->getAttributeCode(),
                        'label' => __($attribute->getFrontend()->getLabel()),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote(),
                    );
                }
                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType, $configuration
                        )
                        ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
                
                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } elseif ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                }
            }
        }
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'weight'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_weight'),
            'gallery'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_gallery'),
            'image'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response' => $response));

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
