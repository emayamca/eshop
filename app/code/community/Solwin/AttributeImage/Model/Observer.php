<?php

class Solwin_AttributeImage_Model_Observer
{
    public function updateLayout()
    {
        $layout = Mage::getSingleton('core/layout');

        $head = $layout->getBlock('head');
        $head->setCanLoadExtJs(true)
             ->addJs('mage/adminhtml/variables.js')
             ->addJs('mage/adminhtml/wysiwyg/widget.js')
             ->addJs('lib/flex.js')
             ->addJs('lib/FABridge.js')
             ->addJs('mage/adminhtml/flexuploader.js')
             ->addJs('mage/adminhtml/browser.js')
             ->addJs('prototype/window.js')
             ->addItem('js_css', 'prototype/windows/themes/default.css');

        // Less than 1.7
        if (version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            $head->addItem('js_css', 'prototype/windows/themes/magento.css');
        } else {
            $head->addCss('lib/prototype/windows/themes/magento.css');
        }
    }
}