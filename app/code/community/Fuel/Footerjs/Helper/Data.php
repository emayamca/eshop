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
 * This Helper file helps to includes configuration values
 */
class Fuel_Footerjs_Helper_Data extends Mage_Core_Helper_Abstract {

    // Regular expression that matches one or more script tags (including conditions but not comments)
    const REGEX_JS            = '#(\s*<!--\[if[^\n]*>\s*(<script.*</script>)+\s*<!\[endif\]-->)|(\s*<script.*</script>)#isU';
    const REGEX_DOCUMENT_END  = '#</body>\s*</html>#isU';

    const XML_CONFIG_FOOTERJS_ENABLED = 'dev/js/meanbee_footer_js_enabled';
    const XML_CONFIG_FOOTERJS_EXCLUDED_BLOCKS = 'dev/js/meanbee_footer_js_excluded_blocks';

    const EXCLUDE_FLAG = 'data-footer-js-skip="true"';
    const EXCLUDE_FLAG_PATTERN = 'data-footer-js-skip';

    /** @var array */
    protected $_blocksToExclude;

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_FOOTERJS_ENABLED, $store);
    }

    public function removeJs($html)
    {
        $patterns = array(
            'js'             => self::REGEX_JS
        );

        foreach($patterns as $pattern) {
            $matches = array();

            $success = preg_match_all($pattern, $html, $matches);
            if ($success) {
                foreach ($matches[0] as $key => $js) {
                    if (strpos($js, self::EXCLUDE_FLAG_PATTERN) !== false) {
                        unset($matches[0][$key]);
                    }
                }
                $html = str_replace($matches[0], '', $html);
            }
        }

        return $html;
    }

    public function moveJsToEnd($html)
    {
        $patterns = array(
            'js'             => self::REGEX_JS,
            'document_end'   => self::REGEX_DOCUMENT_END
        );

        foreach($patterns as $pattern) {
            $matches = array();

            $success = preg_match_all($pattern, $html, $matches);
            if ($success) {
                foreach ($matches[0] as $key => $js) {
                    if (strpos($js, self::EXCLUDE_FLAG_PATTERN) !== false) {
                        unset($matches[0][$key]);
                    }
                }
                $text = implode('', $matches[0]);
                $html = str_replace($matches[0], '', $html);
                $html = $html . $text;
            }
        }

        return $html;
    }

    /**
     * Add skip flag to all js in given html
     * 
     * @param string $html
     * @return string
     */
    public function addJsToExclusion($html)
    {
        return str_replace('<script', '<script ' . self::EXCLUDE_FLAG, $html);
    }

    /**
     * Get list of block names (in layout) to exclude their JS from moving to footer
     *
     * @return array
     */
    public function getBlocksToSkipMoveJs()
    {
        if (is_null($this->_blocksToExclude)) {
            $string = Mage::getStoreConfig(self::XML_CONFIG_FOOTERJS_EXCLUDED_BLOCKS);
            $exludedBlocks = explode(',', $string);
            foreach ($exludedBlocks as $key => $blockName) {
                $exludedBlocks[$key] = trim($blockName);
                if (strpos($exludedBlocks[$key], "\n") || strpos($exludedBlocks[$key], ' ')) {
                    Mage::log('Missing comma in setting "' . self::XML_CONFIG_FOOTERJS_EXCLUDED_BLOCKS . '"', Zend_Log::ERR, null, true);
                }
            }
            $this->_blocksToExclude = array_filter($exludedBlocks);
        }
        return $this->_blocksToExclude;
    }
}
