<?php

class Wyomind_Notificationmanager_Model_Adminhtml_System_Config_Form_Field_Link extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $html = '<a id="' . $element->getHtmlId() . '" ' 
                . $element->serialize($element->getHtmlAttributes()) . '>' . $element->getEscapedValue() 
                . "</a>\n";
        $html .= $element->getAfterElementHtml();
        return $html;
    }

}