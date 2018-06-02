<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Helper_Catalogsearch extends Mage_CatalogSearch_Helper_Data
{
    /**
     * @return string
     */
    public function getSuggestUrl()
    {
        $url = parent::getSuggestUrl();

        $helper = Mage::helper('elasticsearch/autocomplete');
        if ($helper->isActiveEngine() && $helper->isFastAutocompleteEnabled()) {
            $url = sprintf('%sautocomplete.php?store=%s&currency=%s&fallback_url=%s',
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, Mage::app()->getStore()->isCurrentlySecure()),
                Mage::app()->getStore()->getCode(),
                Mage::app()->getStore()->getCurrentCurrencyCode(),
                $url
            );
        }

        return $url;
    }

    /**
     * Get Elasticsearch engine
     *
     * @return Wyomind_Elasticsearch_Model_Resource_Engine
     */
    public function getEngine()
    {
        return Mage::getResourceSingleton('elasticsearch/engine');
    }
}
