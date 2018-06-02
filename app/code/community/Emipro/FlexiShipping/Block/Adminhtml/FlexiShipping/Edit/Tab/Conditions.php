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
class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {
	
	public function getTabLabel()
	 {
	   return Mage::helper('emipro_flexishipping')->__('Conditions');
	}

	public function getTabTitle() {
	   return Mage::helper('emipro_flexishipping')->__('Conditions');
	}

	public function canShowTab() {
	   return true;
	}

	public function isHidden()
	  {
	return false;
	  }

protected function _prepareForm()
  {
	 
		$id = $this->getRequest()->getParam('id');
		$model = Mage::registry("flexishipping"); 
		$form = new Varien_Data_Form();
		$model->getConditions()->setJsFormObject('rule_conditions_fieldset');
		$form->setHtmlIdPrefix('rule_');

		$renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
		->setTemplate('promo/fieldset.phtml')
		->setNewChildUrl($this->getUrl('*/adminhtml_index/newConditionHtml/form/rule_conditions_fieldset'));
	
		$fieldset = $form->addFieldset('conditions_fieldset', array(
		'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
	)->setRenderer($renderer);

	$fieldset->addField("ship_id",'hidden',array(
		'name' => 'ship_id'));

		$fieldset->addField('conditions', 'text', array(
		'name' => 'conditions',
		'label' => Mage::helper('emipro_flexishipping')->__('Conditions'),
		'title' => Mage::helper('emipro_flexishipping')->__('Conditions'),
		
		'required' => true,
	))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
	  
	 $data=$model->getData();
	$data["website_ids"]=explode(",",$data["website_id"]);     
	 unset($data["website_id"]);



        $fieldset = $form->addFieldset('ship_conditions_fieldset', array(
                'legend' => Mage::helper('emipro_flexishipping')->__('Shipping Conditions')
            )
        );

		 $fieldset->addField('shipto', 'textarea', array(
            'label'     => Mage::helper('emipro_flexishipping')->__('Shipping Zipcode & Country'),
            'name'      => 'shipto',
            'style' 	=> 'height: 50px;',
            'note'		=>'IN(360004)'
           
        ));
      
		$allCountriesFiled=$fieldset->addField('all_countries', 'select', array(
            'label'     => Mage::helper('emipro_flexishipping')->__('Ship to Applicable Countries'),
            'name'      => 'all_countries',
             'values'    => Mage::getSingleton('adminhtml/system_config_source_shipping_allspecificcountries')->toOptionArray()
            
        ));

		$specificCountriesIdsFiled=$fieldset->addField('specific_countries_ids', 'multiselect', array(
            'name'      => 'specific_countries_ids[]',
            'label'     => Mage::helper('emipro_flexishipping')->__('Ship to Specific Countries'),
            'title'     => Mage::helper('emipro_flexishipping')->__('Ship to Specific Countries'),
              'required' => true,
            'values'    => Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray()
        ));

    $form->setValues($model->getData());
  
  
    $this->setForm($form);
 
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($allCountriesFiled->getHtmlId(), $allCountriesFiled->getName())
             ->addFieldMap($specificCountriesIdsFiled->getHtmlId(), $specificCountriesIdsFiled->getName())
            ->addFieldDependence(
                $specificCountriesIdsFiled->getName(),
                $allCountriesFiled->getName(),1)
        );

return parent::_prepareForm();
  }
}
