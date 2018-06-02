<?php

class Smartwave_Porto_Block_Adminhtml_Button_Import_Cms extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
		$data = $el->getOriginalData();
        $helper = Mage::helper('porto');
		if (isset($data['process']))
			$process = $data['process'];
		else
			return '<div>Action was not specified</div>';
		
		$buttonSuffix = '';
		if (isset($data['label']))
			$buttonSuffix = ' ' . $data['label'];

		$url = $this->getUrl('adminhtml/porto_import/' . $process);
		$click_event = "setLocation('$url'+'demo_version/'+document.getElementById('porto_settings_install_demo_version').value)+'/'";
        $button_class = "import-cms";
        $after_html = "";
        if(!$helper->checkPurchaseCode()) {
            $click_event = "";
            $button_class = "disabled import-cms";
            $after_html = '<br/><em style="color:#f00;font-size:10px;line-height:1;">Activation is required.</em>';
        }
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass($button_class)
            ->setLabel('Import' . $buttonSuffix)
            ->setOnClick($click_event)
            ->toHtml();
        $html .= $after_html;		
			
        return $html;
    }
}
