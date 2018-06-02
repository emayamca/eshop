<?php
// Include the Original Mage_Customer's AccountController.php file
// where the class 'Mage_Customer_AccountController' is defined
require_once Mage::getModuleDir ( 'controllers', 'Mage_Customer' ) . DS . 'AccountController.php';

// Now extend the class 'Mage_Customer_AccountController'
class Fuel_Customer_AccountController extends Mage_Customer_AccountController {
   /**
     * Processes login action
     * @return bool
     * @throws Exception
     */
    public function loginPostAction()
    {   
        $session = $this->_getSession();
      
            $collection = Mage::getModel('customer/customer')->getCollection();
             $customerData = $this->getRequest()->getPost();
             
             $username = $customerData['username'];
             $password = $customerData['password'];
             $sso = $customerData['sso'];
			 
			//Customer id using customer mobile number
			$customerCollections = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $username));
			if($customerCollections->getFirstItem()->getId()){
				$authenticate = Mage::helper('core')->validateHash($password, $customerCollections->getFirstItem()->getPasswordHash());
				if(!$authenticate) {
					if ($sso) {
						$R['status'] = 0;
						echo json_encode($R); die;
					}
				}
			} else {
				$R['status'] = 0;
				echo json_encode($R); die;
			}

            if (!empty($username) && !empty($password)) {
                try {
					$session->loginById ( $customerCollections->getFirstItem()->getId(), $login ['password'] );
				
                    //$session->login($username, $password);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                    /*
					 * Cookies set here
					 */
                    $magento = array();
                    $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
                    $magento['userID'] = $session->getCustomerId();
                    $magento['userEmail'] = $customer->getEmail();
                    $magento['firstname'] = $customer->getFirstname();
                    $magento['lastname'] = $customer->getLastname();
					$magento['mobile'] = $customer->getMobile();
					$magento['login_provider'] = $customer->getLoginProvider();
					
					// Get customer default billing information
					$customerBillingAddressId = $session->getCustomer()->getDefaultBilling();
					if ($customerBillingAddressId){
						$billingAddress = Mage::getModel('customer/address')->load($customerBillingAddressId);
						$magento['billingAddress'] = $billingAddress->getData();
					}
					// Get customer default shipping information
					$customerShippingAddressId = $session->getCustomer()->getDefaultShipping();
					if ($customerShippingAddressId){
						$shippingAddress = Mage::getModel('customer/address')->load($customerShippingAddressId);
						$magento['shippingAddress'] = $shippingAddress->getData();
					}

					// set cookie on domain
					$cookie = Mage::getSingleton('core/cookie');
					$domain = Mage::helper ( 'sociallogin' )->getHostDomain();
					//$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.metacrust.com' : false;
					$cookie->set('token', $session->getCustomerId(),10800, '/', $domain, TRUE, TRUE);
					$cookie->set('userDatas', serialize($magento),10800, '/', $domain, TRUE, TRUE);
					$R['token'] = $session->getCustomerId();
					$R['userDatas'] = serialize($magento);
					$R['status'] = 1;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($username);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
					if(!$sso){
						$session->addError($message);
					}
                    $session->setUsername($username);
					$R['status'] = 0;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
					$R['status'] = 0;
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
				$R['status'] = 0;
            }
            if ($sso) {
				echo json_encode($R); 
			} else {
				$this->_loginPostRedirect();
			}
        }
  
    /**
     * Gets customer session
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
     
    function isEmail($user) {
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $user)) {
             return true;
        } else {
             return false;
        }
    }

    /**
     * Customer logout action
     */
    public function logoutAction()
    {
        $session = $this->_getSession();
        $session->logout()->renewSession();

        $customerData = $this->getRequest()->getPost();
        /**
         * Unset Cookie values
         */
        if(isset($_COOKIE['token']) && isset($_COOKIE['userDatas'])){
			$domain = Mage::helper ( 'sociallogin' )->getHostDomain();
        	//$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.metacrust.com' : false;
            unset($_COOKIE['token']);
            unset($_COOKIE['userDatas']);
            setcookie('token', null, -1, '/', $domain);
            setcookie('userDatas', null, -1, '/', $domain);
            if($customerData['customer']) {
                $R['status'] = 1;
                echo json_encode($R);
                die;
            }
        }
        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_redirectReferer();
    }
	/**
     * Confirm customer account by id and confirmation key
     */
    public function confirmAction()
    {
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_getSession()->logout()->regenerateSessionId();
        }
        try {
            $id      = $this->getRequest()->getParam('id', false);
            $key     = $this->getRequest()->getParam('key', false);
            $backUrl = $this->getRequest()->getParam('back_url', false);
            if (empty($id) || empty($key)) {
                throw new Exception($this->__('Bad request.'));
            }

            // load customer by id (try/catch in case if it throws exceptions)
            try {
                $customer = $this->_getModel('customer/customer')->load($id);
                if ((!$customer) || (!$customer->getId())) {
                    throw new Exception('Failed to load customer by id.');
                }
            }
            catch (Exception $e) {
                throw new Exception($this->__('Wrong customer account specified.'));
            }

            // check if it is inactive
            if ($customer->getConfirmation()) {
                if ($customer->getConfirmation() !== $key) {
                    throw new Exception($this->__('Wrong confirmation key.'));
                }

                // activate customer
                try {
                    $customer->setConfirmation(null);
                    $customer->save();
                }
                catch (Exception $e) {
                    throw new Exception($this->__('Failed to confirm customer account.'));
                }

                // log in and send greeting email, then die happy
                $session->setCustomerAsLoggedIn($customer);
				
				/*
				 * Cookies set here
				 */
				$magento = array();
				$customers = Mage::getModel('customer/customer')->load($session->getCustomerId());
				$magento['userID'] = $session->getCustomerId();
				$magento['userEmail'] = $customers->getEmail();
				$magento['firstname'] = $customers->getFirstname();
				$magento['lastname'] = $customers->getLastname();
				$magento['mobile'] = $customer->getMobile();
				$magento['login_provider'] = $customer->getLoginProvider();
				// set cookie
				$cookie = Mage::getSingleton('core/cookie');
				$domain = Mage::helper ( 'sociallogin' )->getHostDomain();
				//$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.metacrust.com' : false;
				$cookie->set('token', $session->getCustomerId(), 10800, '/', $domain, TRUE, TRUE);
				$cookie->set('userDatas', serialize($magento), 10800, '/', $domain, TRUE, TRUE);
						
                $successUrl = $this->_welcomeCustomer($customer, true);
                $this->_redirectSuccess($backUrl ? $backUrl : $successUrl);
                return;
            }

            // die happy
            $this->_redirectSuccess($this->_getUrl('*/*/index', array('_secure' => true)));
            return;
        }
        catch (Exception $e) {
            // die unhappy
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectError($this->_getUrl('*/*/index', array('_secure' => true)));
            return;
        }
    }
}
?>