<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 *
 * @category   Fuel
 * @package    Fuel_Footerjs
 * @author     Emayavaramban <emayavaramban@businessfuel.in>
 * @copyright  Copyright (c) 2017 Metacrust.com
 * @license    https://www.magentocommerce.com/
 * @version    1.0.0
 * @since      2017
 */
/**
 * This observer file to move javascript files to footer when the pagecache is enabled
 */
class Fuel_Footerjs_Model_PageCache_Observer extends Enterprise_PageCache_Model_Observer
{


    /**
     * Render placeholder tags around the block if needed
     *
     * Modified to not save JS to container cache.
     * Rely on the fact that JS is being moved to the end of the page
     * and that the JS is not changed after initial generation.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Enterprise_PageCache_Model_Observer
     */
    public function renderBlockPlaceholder(Varien_Event_Observer $observer)
    {
        if (!$this->_isEnabled) {
            return $this;
        }
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $placeholder = $this->_config->getBlockPlaceholder($block);

        if ($transport && $placeholder && !$block->getSkipRenderTag()) {
            $blockHtml = $transport->getHtml();
            $footerJs = Mage::helper('fuel_footerjs');
            if (in_array($block->getNameInLayout(), $footerJs->getBlocksToSkipMoveJs())) {
                $blockHtml = $footerJs->addJsToExclusion($blockHtml);
            }

            $request = Mage::app()->getFrontController()->getRequest();
            /** @var $processor Enterprise_PageCache_Model_Processor_Default */
            $processor = $this->_processor->getRequestProcessor($request);
            if ($processor && $processor->allowCache($request)) {
                $container = $placeholder->getContainerClass();
                if ($container && !Mage::getIsDeveloperMode()) {
                    $container = new $container($placeholder);
                    $container->setProcessor(Mage::getSingleton('enterprise_pagecache/processor'));
                    $container->setPlaceholderBlock($block);

                    // Modify to not save block with JS in it as JS is being moved to the end of the page.
                    $container->saveCache($footerJs->removeJs($blockHtml));
                }
            }

            $blockHtml = $placeholder->getStartTag() . $blockHtml . $placeholder->getEndTag();
            $transport->setHtml($blockHtml);
        }
        return $this;
    }
}