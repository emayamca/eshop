<?php

class Smartwave_Porto_Block_Adminhtml_Button_Import_Demo extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $el)
    {
		$data = $el->getOriginalData();
        $helper = Mage::helper('porto');
		if (isset($data['process']))
			$process = $data['process'];
		else
			return '<div>Action was not specified</div>';
        if (isset($data['demo']))
            $demo = $data['demo'];
        else
            return '<div>Demo param was not specified</div>';
		$buttonSuffix = '';
		if (isset($data['label']))
			$buttonSuffix = ' ' . $data['label'];

        $url = $this->getUrl('adminhtml/porto_demo/' . $process).'demoversion/'.$demo;

        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
        {
            $url .= "/website/".$code;
        }
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
        {
            $url .= "/store/".$code;
        }
        
        $click_event = "setLocation('$url')";
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
