<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Marketplace_Block_Adminhtml_Sellerprofile_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        $countries = Mage::getModel('directory/country')->getCollection();
        foreach ($countries as $country) {
            $countryname[$country->getId()] = $country->getName();
        }
        $store = Mage::registry('store_info');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix("_storeinfo");
        $form->setFieldNameSuffix('storeinfo');

        $fieldset = $form->addFieldset('storeinfo_form', array('legend' => Mage::helper('marketplace')->__('My Profile')));
        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'entity_id',
            'value' => 'entity_id',
        ));
        $fieldset->addField('company_logo', 'image', array(
            'label' => Mage::helper('marketplace')->__('Store logo'),
            'after_element_html' => '<div><small>' . $this->__('Image height and width not more than 300px.') . '</small></div>',
            'name' => "company_logo",
            'onchange' => "checkimage('_storeinfocompany_logo',300,300)",    
        ));
         $fieldform = $fieldset->addField('banner', 'image', array(
            'label' => Mage::helper('marketplace')->__('Store Banner'),
            'name' => "banner",
            'onchange' => "checkimage('_storeinfobanner',900,300)", 
        ));  

        $fieldform->setAfterElementHtml( 
           "<div><small>". $this->__('Image not more than height 300px and width 900px. Valid file type .jpg, .jpeg, .gif, .png.')."</small></div>
            <script type='text/javascript'> 
            function checkimage(elementid,imgwidth,imgheight) {  
                var imageupload = document.getElementById(elementid);
                var regex = new RegExp('([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.png|.gif)$');
                if (regex.test(imageupload.value.toLowerCase())) {
                 
                    if (typeof (imageupload.files[0]) != 'undefined') {   
                        var readerfile = new FileReader();    
                        readerfile.readAsDataURL(imageupload.files[0]); 
                        readerfile.onload = function (e) { 
                            var image_file = new Image();  
                            image_file.src = e.target.result;  
                            image_file.onload = function () {
                                var height = this.height; 
                                var width = this.width;
                                if (height > imgheight || width > imgwidth) {   
                                    alert('Image not more than height '+imgheight+'px and width '+imgwidth+'px.');
                                    document.getElementById(elementid).value = ''; 
                                    return false;  
                                }
                            };
                        }
                    } else { 
                        alert('This browser does not support HTML5.');
                        return false;
                    }
                } else {
                    alert('Please select a valid Image file.');
                    return false; 
                }    
            }
            </script>");

        $fieldset->addField('store_name', 'text', array(
            'label' => Mage::helper('marketplace')->__('Store Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'store_name',
        ));
        $fieldset->addField('store_url', 'link', array(
            'label' => Mage::helper('marketplace')->__('Store URL'),
            'href' => isset($store) ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $store->getStoreUrl() : '',
            'value' => isset($store) ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $store->getStoreUrl() : '',
        ));
        $fieldset->addField('website_url', 'text', array(
            'label' => Mage::helper('marketplace')->__('Website URL'),
            'name' => 'website_url',
            'class' => 'validate-custom-web-url',
            'after_element_html' => "<script>Validation.add('validate-custom-web-url', 'Please enter a valid Website URL. For example http://www.emipro.com,http://www.emipro-tech.com,http://www.emipro_tech.com,http://www.emipro123.com', function(v) {
       v = (v || '').replace(/^\s+/, '').replace(/\s+$/, ''); 
       return Validation.get('IsEmpty').test(v) || /^(http(s?):\/\/)?(www\.)+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,3})+(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/.test(v);   
});
</script>",
        ));
        $fieldset->addField('fb_id', 'text', array(
            'label' => Mage::helper('marketplace')->__('Facebook ID'),
            'name' => 'fb_id',
        ));
        $fieldset->addField('twitter_id', 'text', array(
            'label' => Mage::helper('marketplace')->__('Twitter ID'),
            'name' => 'twitter_id',
        ));
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config');
        $fieldset->addField('company_description', 'editor', array(
            'name' => 'company_description',
            'label' => Mage::helper('marketplace')->__('Company Description'),
            'title' => Mage::helper('marketplace')->__('Company Description'),
            'style' => 'height: 400px;width:600px;',
            'wysiwyg' => true,
            'required' => false,
            'config' => $wysiwygConfig
        ));
        $fieldset->addField('shipping_policies', 'editor', array(
            'name' => 'shipping_policies',
            'label' => Mage::helper('marketplace')->__('Shipping Policy'),
            'title' => Mage::helper('marketplace')->__('Shipping Policy'),
            'style' => 'height: 400px;width:600px;',
            'wysiwyg' => true,
            'required' => false,
            'config' => $wysiwygConfig
        ));
        $fieldset->addField('return_policies', 'editor', array(
            'name' => 'return_policies',
            'label' => Mage::helper('marketplace')->__('Return Policy'),
            'title' => Mage::helper('marketplace')->__('Return Policy'),
            'style' => 'height: 400px;width:600px;',
            'wysiwyg' => true,
            'required' => false,
            'config' => $wysiwygConfig
        ));
        $fieldset->addField('country_id', 'select', array(
            'label' => Mage::helper('marketplace')->__('Country'),
            'name' => 'country_id',
            "values" => $countryname,
        ));

        $fieldset->addField('region', 'text', array(
            'label' => Mage::helper('marketplace')->__('State'),
            'name' => "region",
        ));
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('marketplace')->__('City'),
            'name' => "city",
        ));

        $fieldset->addField('vat_number', 'text', array(
            'label' => Mage::helper('marketplace')->__('VAT Number'),
            'name' => 'vat_number',
             'class' => 'required-entry',
            'required' => true,
        ));

       
        $fieldset->addField('meta_keywords', 'text', array(
            'label' => Mage::helper('marketplace')->__('Meta Keywords'),
            'name' => "meta_keywords",
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'label' => Mage::helper('marketplace')->__('Meta Description'),
            'name' => "meta_description",
        ));
        $fieldset->addField('replacement_gaurantee', 'text', array(
            'label' => Mage::helper('marketplace')->__('Replacement Guarantee'),
            'name' => "replacement_gaurantee",
            'maxlength' => '3',
            'class' => 'validate-digits',
            'style' => 'width:50px !important;',
            'after_element_html' => Mage::helper('marketplace')->__('<span style="margin:5px;">days after delivery</span>'),
        ));
        $fieldset->addField('delivered_in', 'text', array(
            'label' => Mage::helper('marketplace')->__('Delivered In'),
            'name' => "delivered_in",
            'after_element_html' => '<div><small>' . Mage::helper('marketplace')->__('E.g. 5-10 days OR Maximum 7 days') . '</small></div>',
        ));
        $flag = 'No';
        if ($store['default_product_approve']) {
            $flag = 'Yes';
        }
        $fieldset->addField('default_product_approve', 'note', array(
            'label' => Mage::helper('marketplace')->__('Default Product Approved'),
            'text' => $flag,
            'after_element_html' => '<div><small>' . Mage::helper('marketplace')->__('Note : Marketplace product approved default value when seller add new product in store.') . '</small></div>',
        ));

        $sessiondata = Mage::getSingleton('adminhtml/session')->getData('form_data');
        Mage::getSingleton('adminhtml/session')->unsFormData();    
        if (isset($sessiondata['storeinfo'])) {      
            $form->setValues($sessiondata['storeinfo']);        
            if(isset($store['banner']) && !empty($store['banner'])){
                $form->addValues(array('banner'=>$store['banner']));    
            }
            if(isset($store['company_logo']) && !empty($store['company_logo'])){
                $form->addValues(array('company_logo'=>$store['company_logo']));    
            }
            if(isset($store['store_url']) && !empty($store['store_url'])){
                $form->addValues(array('store_url'=>$store['store_url']));    
            }    
        } else if (Mage::registry('store_info')) { 
            if ($store->getRegion() == '' && $store->getRegionId()) { 
                $regionModel = Mage::getModel('directory/region')->load($store->getRegionId());
                $region = $regionModel->getName(); 
                $store['region'] = $region;
            }
            $form->setValues($store); 
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
