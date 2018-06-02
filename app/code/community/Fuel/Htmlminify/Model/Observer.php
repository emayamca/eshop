<?php
/**
 * Fuel_Htmlminify_Model_Observer
 *
 * @category   Fuel
 * @package    Fuel_Htmlminify
 * @author     Emayavaramban <emayavaramban@businessfuel.in>
 * @copyright  Copyright (c) 2017 Metacrust.com
 * @license    https://www.magentocommerce.com/
 * @version    1.0.0
 * @since      2017
 */
class Fuel_Htmlminify_Model_Observer {

    public function Htmlminify($observer) {
        if (Mage::getStoreConfig('sociallogin/htmlminify/enable_htmlminify')) {
            // Fetches the current event
            $event = $observer->getEvent();
            $controller = $event->getControllerAction();
            $allHtml = $controller->getResponse()->getBody();

            // Trim each line
            $allHtml = preg_replace('/^\\s+|\\s+$/m', '', $allHtml);

            // Remove HTML comments
            $allHtml = preg_replace_callback(
                    '/<!--([\\s\\S]*?)-->/', array($this, '_commentCB'), $allHtml);

            // Remove ws around block/undisplayed elements
            $allHtml = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
                    . '|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
                    . '|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
                    . '|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
                    . '|ul)\\b[^>]*>)/i', '$1', $allHtml);

            // Remove ws outside of all elements
            $allHtml = preg_replace_callback(
                    '/>([^<]+)</', array($this, '_outsideTagCB'), $allHtml);
            $controller->getResponse()->setBody($allHtml);
        }
    }

    protected function _outsideTagCB($m) {
        return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
    }

    protected function _commentCB($m) {
        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0] : '';
    }

}
