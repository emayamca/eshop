<?php

/**
 * @category    Wyomind
 * @package     Wyomind_Elasticsearch
 * @version     4.5.0
 * @copyright   Copyright (c) 2017 Wyomind (https://wyomind.net)
 */
class Wyomind_Elasticsearch_Block_Autocomplete_Product_Price extends Wyomind_Elasticsearch_Block_Autocomplete_Abstract
{

    /**
     * @var string
     */
    protected $_template = 'wyomind/elasticsearch/autocomplete/product/price.phtml';

    /**
     * @return Zend_Currency
     */
    public function getCurrency()
    {
        if ($this->_config) {
            $currencies = @unserialize($this->_config->getValue('currency_object'));

            $code = $this->_currency;
            if ($currencies[$code] instanceof Zend_Currency) {
                return $currencies[$code];
            }
        }

        return new Zend_Currency();
    }

    /**
     * @return float
     */
    public function getCurrencyRate()
    {
        if ($this->_config) {
            $rates = @unserialize($this->_config->getValue('currency_rate'));


            $code = $this->_currency;
            if (is_float($rates[$code])) {
                return $rates[$code];
            }
        }
    }

    /**
     * @param float $price
     * @return string
     * @throws Zend_Currency_Exception
     */
    public function formatPrice($price)
    {
        $price = sprintf('%F', $price);
        if ($price == -0) {
            $price = 0;
        }

        return $this->getCurrency()->toCurrency($price * $this->getCurrencyRate());
    }

}
