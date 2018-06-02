<?php

class Juicy_Geoip_Block_Countrydefine extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoOptions;

    public function __construct()
    {
        // create columns
        $this->addColumn('countryCode', array(
            'label' => Mage::helper('adminhtml')->__('Country Code'),
            'size' => 28,
        ));
        $this->addColumn('currencyCode', array(
            'label' => Mage::helper('adminhtml')->__('Currency Code'),
            'size' => 28
        ));
        
        $this->addColumn('store', array(
            'label' => Mage::helper('adminhtml')->__('Store'),
            'size' => 28
        ));
        
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Line');

        parent::__construct();
        $this->setTemplate('juicy/geoip/system/config/form/field/array.phtml');

    }

    protected function _renderCellTemplate($columnName)
    {           
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        
        if($columnName === "currencyCode"){
            $currencyStr = "";
            $currencyArr = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
            foreach($currencyArr as $code => $currency){
                $currencyStr .= '<option value="'.$currency.'">'.$currency.'</option>';
            }
            return '<select name="' . $inputName . '">'.$currencyStr.'</select>';
        } else if($columnName === "countryCode"){
            $countryStr = "";
            $countryArr = Mage::helper('geoip')->getCountryList();
            foreach($countryArr as $code => $country){
                $countryStr .= '<option value="'.$code.'">'.$country.'</option>';
            }            
            return '<select name="' . $inputName . '">'.$countryStr.'</select>';
        } else if($columnName === "store"){
            $storeStr = "";
            foreach (Mage::app()->getWebsites() as $website) {  
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    $storeStr .= '<optgroup label="'.$group->getName().'">';
                    foreach ($stores as $store) {
                        $storeStr .= '<option value="'.$store->getId().'">'.$store->getName().'</option>';
                    }
                    $storeStr .= '</optgroup>';
                }
            }        
            return '<select name="' . $inputName . '">'.$storeStr.'</select>';
        }else{
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }
        
        
    }
}