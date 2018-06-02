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
class Emipro_FlexiShipping_Block_Adminhtml_FlexiShipping_Edit_Tab_Actions
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
        return Mage::helper('emipro_flexishipping')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('emipro_flexishipping')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
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

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => Mage::helper('emipro_flexishipping')->__('Shipping Fees')
            )
        );

        $fieldset->addField('fees', 'textarea', array(
            'label'     => Mage::helper('emipro_flexishipping')->__('Fees'),
            'name'      => 'fees',
             'required' => true,
             'style' => 'height: 50px;',
             
            'note'		=>'You can set mathematical expression here to calculate shipping fees based on product quantity, price & weight.
For that you need to use keywords {prod.qty}, {prod.price}, {prod.weight} in expression.<br> <B>Example</B> {prod.qty}*10'
							
        ));
          $freeShipFiled=$fieldset->addField('free_shipping', 'select', array(
           'label'     => Mage::helper('emipro_flexishipping')->__('Free Shipping '),
           'name'      => 'free_shipping',
           'options'    => array(
                '1' => Mage::helper('emipro_flexishipping')->__('Yes'),
                '0' => Mage::helper('emipro_flexishipping')->__('No'),
            ),
             'note'=>'Select Yes for allow Free Shipping.',
        ));
          $cartPriceFiled=$fieldset->addField('cart_price','text' ,array(
            'name' => 'cart_price',
            'label' => Mage::helper('emipro_flexishipping')->__('Free Shipping above cart price'),
            'required' => true,
           'note'=>'Enter cart price.',
            
        ));
	$form->setValues($model->getData());
       if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($freeShipFiled->getHtmlId(), $freeShipFiled->getName())
             ->addFieldMap($cartPriceFiled->getHtmlId(), $cartPriceFiled->getName())
            ->addFieldDependence(
                $cartPriceFiled->getName(),
                $freeShipFiled->getName(),1)
        );
        return parent::_prepareForm();
    }
}
