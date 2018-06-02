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
class Emipro_FlexiShipping_Model_Resource_FlexiShipping extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
		
        $this->_init('emipro_flexishipping/flexishipping', 'ship_id');  
 
    }
}
