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
 * This observer file to move javascript files to footer
 */
class Fuel_Footerjs_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function handleBlockInlineJs(Varien_Event_Observer $observer)
    {
		if(Mage::getBlockSingleton('page/html_header')->getIsHomePage()):
			Varien_Profiler::start('FuelFooterJs');

			/** @var Fuel_Footerjs_Helper_Data $helper */
			$helper = Mage::helper('fuel_footerjs');
			if (!$helper->isEnabled()) {
				Varien_Profiler::stop('FuelFooterJs');
				return $this;
			}

			/** @var Varien_Object $transport */
			$transport = $observer->getTransport();

			/** @var Mage_Core_Block_Abstract $block */
			$block = $observer->getBlock();

			if (in_array($block->getNameInLayout(), $helper->getBlocksToSkipMoveJs())) {
				$transport->setHtml($helper->addJsToExclusion($transport->getHtml()));
			}

			if (Mage::app()->getRequest()->getModuleName() == 'pagecache') {
				$transport->setHtml($helper->removeJs($transport->getHtml()));
				Varien_Profiler::stop('FuelFooterJs');
				return $this;
			}

			if (!is_null($block->getParentBlock())) {
				Varien_Profiler::stop('FuelFooterJs');
				// Only look for JS at the root block
				return $this;
			}

			$transport->setHtml($helper->moveJsToEnd($transport->getHtml()));

			Varien_Profiler::stop('FuelFooterJs');
			return $this;
		endif;
    }


}
