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
class Emipro_FlexiShipping_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
	
  /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'flexishipping_match_result';
 
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );
 
    /**
     * Retrieve Indexer name
     * @return string
     */
    public function getName()
    {
        return 'Emipro Flexi Shipping Rules Index';
    }
 
    /**
     * Retrieve Indexer description
     * @return string
     */
    public function getDescription()
    {
        return 'Shipping Rules Index';
    }
 
    /**
     * Register data required by process in event object
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $dataObj = $event->getDataObject();
        if($event->getType() == Mage_Index_Model_Event::TYPE_SAVE){
            $event->addNewData('catalogdisplayrules_update_product_id', $dataObj->getId());
        }elseif($event->getType() == Mage_Index_Model_Event::TYPE_DELETE){
            $event->addNewData('catalogdisplayrules _delete_product_id', $dataObj->getId());
        }elseif($event->getType() == Mage_Index_Model_Event::TYPE_MASS_ACTION){
            $event->addNewData('catalogdisplayrules _mass_action_product_ids', $dataObj->getProductIds());
        }
    }
 
    /**
     * Process event
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if(!empty($data['catalogdisplayrules _update_product_id'])){
            $this->doSomethingOnUpdateEvent(($data['catalogdisplayrules _update_product_id']));
        }elseif(!empty($data['catalogdisplayrules _delete_product_id'])){
            $this->doSomethingOnDeleteEvent($data['catalogdisplayrules _delete_product_id']);
        }elseif(!empty($data['catalogdisplayrules_mass_action_product_ids'])){
             $this->doSomethingOnMassActionEvent($data['catalogdisplayrules _mass_action_product_ids']);
        }
    }
 
 
    /**
     * match whether the reindexing should be fired
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        $entity = $event->getEntity();
        $result = true;
        if($entity != Mage_Catalog_Model_Product::ENTITY){
            return;
        }
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);
        return $result;
    }
 
    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
		Mage::helper('emipro_flexishipping')->getProductIds();
    }
}
