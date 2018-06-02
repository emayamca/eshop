<?php
/*
 * //////////////////////////////////////////////////////////////////////////////////////
 *
 * @author Emipro Technologies
 * @Category Emipro
 * @package Emipro_FlexiShipping
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * //////////////////////////////////////////////////////////////////////////////////////
 */
class  Emipro_FlexiShipping_Model_Catalog_Rule extends Mage_CatalogRule_Model_Rule
{
	public function _construct() 
	{
		parent::_construct();
		$this->_init('emipro_flexishipping/flexiShipping');
	}
	public function getConditionsInstance()
	{
		return Mage::getModel('catalogrule/rule_condition_combine');
	}
}
