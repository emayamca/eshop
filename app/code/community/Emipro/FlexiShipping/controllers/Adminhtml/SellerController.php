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
class Emipro_FlexiShipping_Adminhtml_SellerController extends Mage_Adminhtml_Controller_Action
{   
    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Flexi Shipping Seller Rules').' - '.Mage::getStoreConfig('general/store_information/name',Mage::app()->getStore()->getId())); 
		$this->_setActiveMenu('marketplace/seller');
		$this->_addContent($this->getLayout()->createBlock('emipro_flexishipping/adminhtml_sellerRules'));
        $this->renderLayout();
    }
     public function gridAction()
    {
        $this->loadLayout();
        
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('emipro_flexishipping/adminhtml_sellerRules_grid')->toHtml()
        );
    }
    public function newAction()
    {  
       $this->_forward('edit');
    } 
   public function editAction()
      {
		$Id = $this->getRequest()->getParam('id');
        $shippingModel = Mage::getModel('emipro_flexishipping/flexiShipping')->load($Id);
		if ($shippingModel->getShipId() || $Id == 0)
           {
             Mage::register('flexishipping', $shippingModel);
             $this->loadLayout();
             $this->getLayout()->getBlock('head')->setTitle($this->__('Flexi Shipping Seller Rules').' - '.Mage::getStoreConfig('general/store_information/name',Mage::app()->getStore()->getId())); 
			$this->_setActiveMenu('marketplace/seller');
             $this->getLayout()->getBlock('head')
                  ->setCanLoadExtJs(true);
              $this->_addContent($this->getLayout()
                  ->createBlock('emipro_flexishipping/adminhtml_flexiShipping_edit'))
                  ->_addLeft($this->getLayout()
                  ->createBlock('emipro_flexishipping/adminhtml_flexiShipping_edit_tabs')
              );
             $this->renderLayout();
           }
           else
           {
			Mage::getSingleton('adminhtml/session')->addError('Shipping does not exist');
                $this->_redirect('*/*/');
            }
       }
	public function saveAction()
	{
		
		if ($this->getRequest()->getPost())
		{	
			try 
			{
                $model = Mage::getModel('emipro_flexishipping/flexiShipping');
                $data = $this->getRequest()->getPost();
				$data1 = $this->getRequest()->getPost('website_id');
				$data2 = $this->getRequest()->getPost('customer_group_id');
				$data3 = $this->getRequest()->getPost('specific_countries_ids');
				$data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('ship_id'))
                 {
					$model->load($id);
                   if($id != $model->getShipId())
                    {
                        Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
					}
                    else
                    {
						$model->setId($model->getShipId());
					}
                    
                }
                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
			
                $autoApply = false;
                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                }
				 unset($data["website_ids"]);
				unset($data["customer_group_ids"]);
				$model->loadPost($data);
				 $wids= implode(",",$data1);
				$customer=implode(",",$data2);
				 $country=implode(",",$data3);
				 $model->setData("specific_countries_ids",$country);
				$model->setData("customer_group_id",$customer);
                $model->setData("website_id",$wids);
               
               $ship_fees=Mage::helper('emipro_flexishipping')->check_fees($model->getFees());
               if($ship_fees==1)
				{
					$model->save();
				}
				else
				{
					$this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the shipping fees.')
                );
                  $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return; 
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalogrule')->__('The rule has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                    $this->getRequest()->setParam('ship_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('catalogrule/flag')->loadSelf()
                        ->setState(1)
                        ->save();
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
            }     
		}
	public function newConditionHtmlAction()
	{
		
		$id = $this->getRequest()->getParam('id');
		$typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
		$type = $typeArr[0];
		$model = Mage::getModel($type)
		->setId($id)
		->setType($type)
		->setRule(Mage::getModel('emipro_flexishipping/flexiShipping'))
		->setPrefix('conditions');
			if (!empty($typeArr[1]))
			{
				  $model->setAttribute($typeArr[1]);
			}
		if ($model instanceof Mage_Rule_Model_Condition_Abstract)
		{
		  $model->setJsFormObject($this->getRequest()->getParam('form'));
		  $html = $model->asHtmlRecursive();
		}
		 else {
		  $html = '';
		}
		$this->getResponse()->setBody($html);
	  }
	public function chooserAction()
    {
		
        switch ($this->getRequest()->getParam('attribute')) {
            case 'sku':
                $type = 'adminhtml/promo_widget_chooser_sku';
                break;

            case 'categories':
                $type = 'adminhtml/promo_widget_chooser_categories';
                break;
        }
        if (!empty($type)) {
            $block = $this->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }
	public function applyRulesAction()
    {
        $errorMessage = Mage::helper('emipro_flexishipping')->__('Unable to apply rules.');
        try {
				$id=$this->getRequest()->getParam("ship_id");
		 		Mage::helper('emipro_flexishipping')->applyrule($id);
           // Mage::getSingleton('emipro_flexishipping/indexer')->reindexAll();
            $this->_getSession()->addSuccess(Mage::helper('catalogrule')->__('The rules have been applied.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
        }
        $this->_redirect('*/*');
    }
  /**
     * @deprecated since 1.5.0.0
     */
    public function addToAlersAction()
    {
    }
 
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/seller');
    }
  /**
     * Set dirty rules notice message
     *
     * @param string $dirtyRulesNoticeMessage
     */
    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage)
    {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * Get dirty rules notice message
     *
     * @return string
     */
    public function getDirtyRulesNoticeMessage()
    {
        $defaultMessage = Mage::helper('catalogrule')->__('There are rules that have been changed but were not applied. Please, click Apply Rules in order to see immediate effect in the shipping rule.');
        return $this->_dirtyRulesNoticeMessage ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }
   
  public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
			 try {
                $model = Mage::getModel('emipro_flexishipping/flexiShipping');
              $model->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('emipro_flexishipping')->__('The rule has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('emipro_flexishipping')->__('An error occurred while deleting the rule. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('emipro_flexishipping')->__('Unable to find a rule to delete.')
        );
        $this->_redirect('*/*/');
    }
}
