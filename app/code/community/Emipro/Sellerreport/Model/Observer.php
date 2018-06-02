<?php

class Emipro_Sellerreport_Model_Observer 
{ 
	public function checkblock($observer){
		if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Report_Product_Sold_Grid) {
			$observer->getBlock()->setTemplate('sellerreport/grid.phtml');
		}
	}
} 
