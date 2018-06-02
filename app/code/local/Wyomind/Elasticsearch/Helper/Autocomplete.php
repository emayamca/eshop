<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Helper_Autocomplete extends Wyomind_Elasticsearch_Helper_Data
{

    /**
     * @param mixed $store
     * @return int
     */
    public function getAutocompleteLimit($entity)
    {
        $store = Mage::app()->getStore();
        
        return Mage::getStoreConfig('elasticsearch/' . $entity . '/autocomplete_limit', $store);
    }

    /**
     * @param mixed $store
     * @return array
     */
    public function getLabels($store = null)
    {
        $labels = array();
        $config = @unserialize(Mage::getStoreConfig('elasticsearch/autocomplete/labels', $store));
        if (is_array($config)) {
            foreach ($config as $data) {
                $labels[$data['label']] = $data['translation'];
            }
        }

        return $labels;
    }

    /**
     * Checks if autocomplete is enabled/available for given entity and store
     *
     * @param string $entity
     * @param mixed $store
     * @return bool
     */
    public function isAutocompleteEnabled($entity, $store = null)
    {
        return $this->isIndexationEnabled($entity, $store) &&
                Mage::getStoreConfigFlag('elasticsearch/' . $entity . '/enable_autocomplete', $store);
    }

    /**
     * Checks if autocomplete is enabled for given store
     *
     * @param mixed $store
     * @return bool
     */
    public function isGlobalAutocompleteEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('elasticsearch/autocomplete/enable', $store);
    }

    /**
     * Checks if fast autocomplete is enabled for given store
     *
     * @param mixed $store
     * @return bool
     */
    public function isFastAutocompleteEnabled($store = null)
    {
        return $this->isGlobalAutocompleteEnabled($store) &&
                Mage::getStoreConfigFlag('elasticsearch/autocomplete/enable_fast', $store);
    }

    /**
     * Save Elasticsearch configuration so it can be used for fast autocomplete
     */
    public function saveConfig()
    {
        $config = array();
        $currentStore = Mage::app()->getStore();
        $currency_list = array();
        $rate_list = array();

        $currencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
        foreach ($currencies as $currency) {
            $currency_list[$currency] = Mage::app()->getLocale()->currency($currency);
        }
        foreach (Mage::app()->getStores() as $store) {
            /** @var Mage_Core_Model_Store $store */
            if ($store->getIsActive()) {
                Mage::app()->setCurrentStore($store);

                $locale = Mage::getSingleton('core/locale');
                $locale->emulate($store->getId());
                $base_currency = $store->getBaseCurrency();
                foreach ($currencies as $currency) {
                    $rate_list[$currency] = floatval($base_currency->getRate($currency));
                }

                $config[$store->getCode()] = array(
                    'client_config' => $this->getEngineConfigData($store),
                    'config' => Mage::getStoreConfig('elasticsearch', $store),
                    'analyzers' => $this->getStoreAnalyzers($store),
                    'currency_object' => serialize($currency_list),
                    'currency_rate' => serialize($rate_list),
                    'base_url' => $store->getUrl('/'),
                    'package' => Mage::getStoreConfig('design/package/name', $store),
                    'theme' => Mage::getStoreConfig('design/theme/template', $store),
                );

                foreach ($this->getStoreTypes($store) as $type) {
                    if (!$this->isAutocompleteEnabled($type, $store)) {
                        continue;
                    }

                    /** @var Wyomind_Elasticsearch_Helper_Indexer_Abstract $indexer */
                    $elasticsearchClient = Mage::getResourceModel('elasticsearch/client');
                    $indexer = $elasticsearchClient->getIndexer($type);
                    $config[$store->getCode()]['types'][$type] = array(
                        'block' => $indexer->getBlockClass(),
                        'index_properties' => $indexer->getStoreIndexProperties($store),
                        'filter' => get_class(Mage::helper('elasticsearch/filter_' . $type)),
                        'additional_fields' => $indexer->getAdditionalFields(),
                    );
                }
            }
        }

        Mage::dispatchEvent('wyomind_elasticsearch_autocomplete_save_config_before', array('config' => &$config));

        Wyomind_Elasticsearch_Config::save($config);

        Mage::app()->setCurrentStore($currentStore);
    }

    /**
     * Save synonym config file
     * @param text $queryText
     * @param text $synonymFor
     */
    public function saveSynonym()
    {
        $config = array();

        $seachTerms = Mage::getModel("CatalogSearch/Query")->getCollection()
                ->addFieldToFilter("synonym_for", array("notnull" => true))
                ->addFieldToFilter("synonym_for", array("neq" => ""))
                ->addFieldToFilter("is_active", array("eq" => 1));
        foreach ($seachTerms as $seachTerm) {
            $config[] = array(
                "query_text" => $seachTerm->getData("query_text"),
                "synonym_for" => $seachTerm->getData("synonym_for"),
                "store" => Mage::app()->getStore($seachTerm->getData("store_id"))->getCode()
            );
        }


        $class = new Wyomind_Elasticsearch_Config(true);
        $class->save($config, true);
    }

}
