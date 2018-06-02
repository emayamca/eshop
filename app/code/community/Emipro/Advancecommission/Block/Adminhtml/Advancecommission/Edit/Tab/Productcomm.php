<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Advancecommission 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Advancecommission_Block_Adminhtml_Advancecommission_Edit_Tab_Productcomm extends Mage_Adminhtml_Block_Widget_Form {

    public function _prepareForm() {
        //echo "demo"; exit;//
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_productcomm');
        $form->setFieldNameSuffix('productcomm');
        $this->setForm($form);
        $fieldset = $form->addFieldset('productcomm_form', array('legend' => Mage::helper('emipro_advancecommission')->__('Seller Product Commission')));
        $productdata = Mage::registry('current_product');
        $id = $productdata->getId();
        $store = $productdata->getStoreId();
        $sellerfield = '';
        $model = Mage::getModel('emipro_advancecommission/advanceproductcommission')->getCollection()->addFieldToFilter('store_id',$store)->addFieldToFilter('product_id',$id);
        $val_array = array();
        if($model->count()==1){
            $comm_data = $model->getFirstItem();
            $val_array['commission_type']=$comm_data->getCommissionType();
            $val_array['commission']=$comm_data->getCommission();
            $comm_type =  $val_array['commission_type'];
            $comm_rate = $val_array['commission'];
        }
        else{
            $comm_rate = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_rate');
            $comm_type = Mage::getStoreConfig('marketplace_setting/categorycommission/commission_type');
        }


        if($comm_type == 0){
            $comm_name = 'Percent';
        }
        else if($comm_type == 1){
            $comm_name = 'Fixed';
        }
        else
        {
            $comm_name = '';    
        }
        $sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
		//Displayed advance commission amount field on seller dashboard
       /* if($sellerid){
            $fieldset->addField('commission_type', 'note', array(
                'label' => Mage::helper('marketplace')->__('Product Commission Type'),
                'text' => $comm_name,
            ));
            $fieldset->addField('commission', 'note', array(
                'label' => Mage::helper('marketplace')->__('Commission Amount'),
                'text' => $comm_rate,
            ));
        }
        else{ */  
            $fieldset->addField('commission_type', 'select', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Product Commission Type'),
                /*'class' => 'required-entry',*/
                /*'required' => true,*/
                'name' => 'commission_type',
                'values' => array('0' => 'Percent', '1' => 'Fixed'),
            ));

            $fieldset->addField('commission', 'text', array(
                'label' => Mage::helper('emipro_advancecommission')->__('Commission'),
                'class' => 'validate-zero-or-greater',
                /*'required' => true,*/
                'name' => 'commission',
            ));
        //}

        $model = Mage::getModel('emipro_advancecommission/advanceproductcommission')->getCollection()->addFieldToFilter('store_id',$store)->addFieldToFilter('product_id',$id);
        if($model->count()==1){
            $comm_data = $model->getFirstItem();
            $form->setValues($val_array);
        }
        return parent::_prepareForm();
    }

}
