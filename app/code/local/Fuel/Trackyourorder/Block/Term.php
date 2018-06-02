<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalogsearch term block
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Fuel_Trackyourorder_Block_Term extends Mage_CatalogSearch_Block_Term
{
    protected $_terms;
    protected $_minPopularity;
    protected $_maxPopularity;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load terms and try to sort it by names
     *
     * @return Mage_CatalogSearch_Block_Term
     */
    protected function _loadTerms()
    {
		$query = Mage::app()->getRequest()->getParam('q');
		$explode = explode(" ", $query);
		$likeQuery = Mage::helper('autosuggestion')->getLikeQuery($explode);
        if (empty($this->_terms)) {
            $this->_terms = array();
            $terms = Mage::getResourceModel('catalogsearch/query_collection')
                ->setPopularQueryFilter(Mage::app()->getStore()->getId())
				->addFieldToFilter('query_text',array($likeQuery))
				//->addFieldToFilter('query_text', array('like' => '%'.$query.'%'))
                ->setPageSize(10)
				->setOrder('popularity', 'desc')
                ->load()
                ->getItems();
            if( count($terms) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($terms)->getPopularity();
            $this->_minPopularity = end($terms)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ( $range == 0 ) ? 1 : $range;
            foreach ($terms as $term) {
                if( !$term->getPopularity() ) {
                    continue;
                }
                $term->setRatio(($term->getPopularity()-$this->_minPopularity)/$range);
                $temp[$term->getName()] = $term;
                $termKeys[] = $term->getName();
            }
            natcasesort($termKeys);

            foreach ($termKeys as $termKey) {
                $this->_terms[$termKey] = $temp[$termKey];
            }
        }
        return $this;
    }

    public function getTerms()
    {
        $this->_loadTerms();
        return $this->_terms;
    }

    public function getSearchUrl($obj)
    {
        $url = Mage::getModel('core/url');
        /*
        * url encoding will be done in Url.php http_build_query
        * so no need to explicitly called urlencode for the text
        */
        $url->setQueryParam('q', $obj->getName());
        return $url->getUrl('catalogsearch/result');
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
	
	public function getPopularTermsCollection(){
		$limits = Mage::getStoreConfig('autosuggestion/custom_group/autosuggestion_suggestion_limit');
		$terms = Mage::getResourceModel('catalogsearch/query_collection')
			->setPopularQueryFilter(Mage::app()->getStore()->getId())
			->setPageSize($limits)
			->setOrder('popularity', 'asc')
			->load()
			->getItems();
        return $terms;
	}
}
