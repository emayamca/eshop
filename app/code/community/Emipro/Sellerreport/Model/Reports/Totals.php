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
 
class Emipro_Sellerreport_Model_Reports_Totals extends Mage_Reports_Model_Totals
{
	public function countTotals($grid, $from, $to)
    {
		$columns = array();
        foreach ($grid->getColumns() as $col) {
            $columns[$col->getIndex()] = array("total" => $col->getTotal(), "value" => 0);
        } 

        $count = 0;
        $report = $grid->getCollection()->getReportFull($from, $to);
        $sellerproducts = Mage::registry('sellerproductids');
        foreach ($report as $item) {
                if(isset($sellerproducts) && !empty($sellerproducts))
                {
                    if(!in_array($item->getEntityId(),$sellerproducts)){
                       continue;
                    }  
                } 
        
            if ($grid->getSubReportSize() && $count >= $grid->getSubReportSize()) {
                continue;
            }
            $data = $item->getData();

            foreach ($columns as $field=>$a) {
                if ($field !== '') {
                    $columns[$field]['value'] = $columns[$field]['value'] + (isset($data[$field]) ? $data[$field] : 0);
                }
            }
            $count++;
        }
        $data = array();
        foreach ($columns as $field => $a) {
            if ($a['total'] == 'avg') {
                if ($field !== '') {
                    if ($count != 0) {
                        $data[$field] = $a['value']/$count;
                    } else {
                        $data[$field] = 0;
                    }
                }
            } else if ($a['total'] == 'sum') {
                if ($field !== '') {
                    $data[$field] = $a['value'];
                }
            } else if (strpos($a['total'], '/') !== FALSE) {
                if ($field !== '') {
                    $data[$field] = 0;
                }
            }
        }

        $totals = new Varien_Object();
        $totals->setData($data);

        return $totals;
    }
}
		
