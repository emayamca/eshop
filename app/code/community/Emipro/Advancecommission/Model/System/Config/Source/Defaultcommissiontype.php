<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_Advancecommission_Model_System_Config_Source_Defaultcommissiontype {

    public function toOptionArray(){
        return array(
            array(
                'value' => '0',
                'label' => 'Category Commission',
            ),
            array(
                'value' => '1',
                'label' => 'Product Commission',
            ),
        );
    }

}
