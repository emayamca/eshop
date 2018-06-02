<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    Fuel
 * @package     Fuel_Bulkorderform
 */
/**
 * Bulkorder frontend controller
 */
class Fuel_Bulkorderform_IndexController extends Mage_Core_Controller_Front_Action
{
	/*
	 * Added home page redirection for frontend action
	 */
    public function indexAction()
    {
		$this->_redirectUrl(Mage::getBaseUrl());
    }
	/*
	 * Bulk Order Form Submission
	 * Sent an email to general store email address
	 */
 	public function saveBulkOrderAction(){
		$bulkOrder = $this->getRequest()->getPost();
		$country = Mage::getModel('directory/country')->loadByCode($bulkOrder['customer']['country_id']);
		if($bulkOrder['customer']['customerstate']){
			$customerstate = $bulkOrder['customer']['customerstate'];
		} else {
			$customerstate = $bulkOrder['customerstate'];
		}
		$post = array("fullname"=>$bulkOrder['customername'],"emailaddress"=>$bulkOrder['customeremail'],"mobilenumber"=>$bulkOrder['telephone'],"country"=>$country->getName(),"city"=>$customerstate,"productname"=>$bulkOrder['customerproductname'],"quantity"=>$bulkOrder['customerqty'],"status"=> 1);
		if($post){
			$toEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			$toName = Mage::getStoreConfig('trans_email/ident_general/name');
			
			$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
			$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( Mage::getStoreConfig('sociallogin/bulkorders/email_template') );
			$emailTemplate->setSenderName ( $toName );
			$emailTemplate->setSenderEmail ( $toEmail );
			$emailTemplate->setDesignConfig ( array (
				'area' => 'frontend' 
			) );
			$emailTemplate->getProcessedTemplate ( $post );
			
			try {
				$emailTemplate->send ( $toEmail, $toName, $post );

				$model = Mage::getModel('bulkorderform/bulkorderform');		
				$model->setData($post)
						->setCreatedTime(now())
						->setUpdateTime(now());
				$model->save();

				Mage::getSingleton('core/session')
                    ->addSuccess($this->__('Your bulk order request was submitted and we will be responded to you as soon as possible. Thank you!'));
                $this->_redirectUrl(Mage::getBaseUrl());

                return;
			} catch (Exception $e) {
                $translate->setTranslateInline(true);
 
                Mage::getSingleton('core/session')->addError($this->__('Unable to submit your request. Please, try again later'));
                $this->_redirectUrl(Mage::getBaseUrl());
                return;
            }
		} else {
            $this->_redirectUrl(Mage::getBaseUrl());
        }
		
	}
	
	/*
	 * Submit Enquiry
	 * Sent an email to general store email address
	 */
 	public function saveSubmitEnquiryAction(){
		$enquiry = $this->getRequest()->getPost();

		$post = array("fullname"=>$enquiry['customername'],"emailaddress"=>$enquiry['customeremail'],"mobilenumber"=>$enquiry['telephone'],"productname"=>$enquiry['customerproductname'],"comments"=>$enquiry['comments'],"status"=> 1);
		if($post){
			$toEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			$toName = Mage::getStoreConfig('trans_email/ident_general/name');
			
			$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
			$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( Mage::getStoreConfig('sociallogin/enquiryform/template') );
			$emailTemplate->setSenderName ( $toName );
			$emailTemplate->setSenderEmail ( $toEmail );
			$emailTemplate->setDesignConfig ( array (
				'area' => 'frontend' 
			) );
			$emailTemplate->getProcessedTemplate ( $post );

			try {
				$emailTemplate->send ( $toEmail, $toName, $post );
				
				$model = Mage::getModel('bulkorderform/bulkorderform');		
				$model->setData($post)
						->setform(1)
						->setCreatedTime(now())
						->setUpdateTime(now());
				$model->save();
				
				Mage::getSingleton('core/session')
                    ->addSuccess($this->__('Your enquiry was submitted and we will be responded to you as soon as possible. Thank you!'));
                $this->_redirectUrl(Mage::getBaseUrl());

                return;
			} catch (Exception $e) {
                $translate->setTranslateInline(true);
 
                Mage::getSingleton('core/session')->addError($this->__('Unable to submit your request. Please, try again later'));
                $this->_redirectUrl(Mage::getBaseUrl());
                return;
            }
		} else {
            $this->_redirectUrl(Mage::getBaseUrl());
        }
		
	}
}