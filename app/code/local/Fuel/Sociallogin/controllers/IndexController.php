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
 * @package     Fuel_Sociallogin
 */
/**
 * Social Login for Both Login / Register
 *
 * In this class contains the login and create account and forget password operations.
 */
class Fuel_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {
	const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    /**
     * Render Fuel sociallogin pop-up layout
     *
     * @return void
     */
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
	/*
	 * Customer dashboard email address verification code send
	 *
	 * @return json response
	 */
	public function emailAddressOTPAction(){
		$errors = array();
		$cusDatas = $this->getRequest()->getPost();
		if (!Zend_Validate::is($cusDatas['email'], 'NotEmpty')) {
            $errors['email'] = $this->__('Please enter email address');
        } else if (!Zend_Validate::is($cusDatas['email'], 'EmailAddress')) {
            $errors['email'] = $this->__('Please enter a valid email address. For example johndoe@domain.com.');
        }
		//Checking customer entered email address
		if($cusDatas['email'] && empty($errors)){
			$customerCollections = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('email', array('eq' => $cusDatas['email']));
			
			//Checking customers registered with the entered email address
			if(count($customerCollections) == 0){
				$cresponse = Mage::helper ( 'sociallogin' )->CURLsendemail($cusDatas);

				//Condition based on CURL Response
				if($cresponse){
					$cgif = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/base/default/sociallogin/images/ajax-loader.gif";
					/* Verification code Form */
					$codeHtml .= '<form id="save_email" method="post" onkeypress="return event.keyCode != 13;">';
					$codeHtml .= '<div class="input-group">';
					$codeHtml .= '<input type="hidden" name="customer_id" class="input-box" value="'.$cusDatas['customer_id'].'"/>';
					$codeHtml .= '<input type="hidden" name="email" class="input-box" value="'.$cusDatas['email'].'"/>';
					$codeHtml .= '<input type="hidden" name="name" class="input-box" value="'.$cusDatas['name'].'"/>';
					$codeHtml .= '<input type="text" class="form-control" name="otp_code" id="otp_code" class="input-box" placeholder="Enter the (verification code) "/>';
					$codeHtml .= '<div id="progress_image_cmbv" style="display: none"><img src="'.$cgif.'" alt="loading please wait" /></div>';
					$codeHtml .= '<button type="button" onclick="save_email()" class="btn btn-primary emailotpBtn" id="verifiy_email_otp_btn">Verify and Save</button>';
					$codeHtml .= '<button type="button" id="resend_email_otp" onclick="resendEmailOtp()" title="resend Otp" class="btn btn-primary emailotpBtn">Resend CODE</button>';
					$codeHtml .= '</div></form>';
					/* Verification code Form */
					$response_otp['form'] = $codeHtml;
					$response_otp['status'] = 1;
					$response_otp['message'] = 'Verification code has been to sent this email "'.$cusDatas['email'].'". Please enter your verification code and verify';
					//$response_otp['otp_code'] = Mage::getSingleton('core/session')->getVCODE();
				} else {
					$response_otp['status'] = 0;
					$response_otp['message'] = 'This "'.$cusDatas['email'].'" email not valid. Please enter a valid email address.';
				}
			} else {
				$response_otp['status'] = 0;
				$response_otp['message'] = 'This email address "'.$cusDatas['email'].'" Already registered in the website';
			}
		} else {
			$response_otp['status'] = 0;
			$response_otp['message'] = $errors['email'];
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($response_otp) );
		return;
		
	}
	/*
	 * Email address Verification and Save
	 *
	 * @return json response
	 */
	public function saveEmailAction(){
		$saveEmail = array();
		$otpCode = $this->getRequest()->getPost();
		//checking customer entered verification Code
		if($otpCode['otp_code']){
			if(Mage::getSingleton('core/session')->getVCODE() == $otpCode['otp_code']){
				Mage::getModel('customer/customer')->load($otpCode['customer_id'])->setEmail($otpCode['email'])->save();
				$saveEmail['status'] = 1;
				$saveEmail['message'] = 'Your verification code has been verified successfully. Please wait for a moment to reload the page.';
				Mage::getSingleton('core/session')->unsVCODE();
			} else {
				$saveEmail['status'] = 0;
				$saveEmail['message'] = 'Incorrect verification code, please try again.';
			}
		} else {
			$saveEmail['status'] = 0;
			$saveEmail['message'] = 'Please enter the verification code and click verify.';
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($saveEmail) );
		return;
	}
	/*
	 * Resend VCODE
	 *
	 * @return array
	 */
	public function resendVcodeAction(){
		$resend_vcode = array();
		$resendVcode = $this->getRequest()->getPost();

		//checking customer entered verification Code
		if($resendVcode['email']){
			$response_resend_vcode = Mage::helper ( 'sociallogin' )->CURLsendemail($resendVcode);
			if($response_resend_vcode){
				$resend_vcode['success'] = 1;
				$resend_vcode['message'] = 'Verification code has been sent to this email address "'.$resendVcode['email'].'". Please enter your verification code and verify';
				//$resend_vcode['otp_code'] = Mage::getSingleton('core/session')->getVCODE();
			} else {
				$resend_vcode['success'] = 0;
				$resend_vcode['message'] = 'This "'.$resendVcode['email'].'" email address not valid. Please enter a valid email address.';
			}
		} else {
			$resend_vcode['success'] = 0;
			$resend_vcode['message'] = 'Please enter verification code and click verify.';
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($resend_vcode) );
		return;
	}
	/*
	 * Customer dashboard Mobile number verification
	 *
	 * @return integer
	 */
	public function mobileNumberOTPAction(){
		$errors = array();
		$cusDatas = $this->getRequest()->getPost();
		if (!Zend_Validate::is($cusDatas['mobile'], 'NotEmpty')) {
            $errors['mobile'] = $this->__('Please enter mobile number');
        } else if (!Zend_Validate::is($cusDatas['mobile'], 'StringLength', array('min' => 10, 'max' => 10))) {
            $errors['mobile'] = $this->__('Please enter a valid mobile number. Ex 9632587410');
        }
		//Checking customer entered mobile number
		if($cusDatas['mobile'] && empty($errors)){
			$customerCollections = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $cusDatas['mobile']));
			
			//Checking customers registered with the entered mobile number
			if(count($customerCollections) == 0){
				$cresponse = Mage::helper ( 'sociallogin' )->CURLsendsms($cusDatas['mobile'], $form_id = "dashboard");

				//Condition based on CURL Response
				if($cresponse == 200){
					$cgif = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/base/default/sociallogin/images/ajax-loader.gif";
					/*OTP Form*/
					$otpHtml .= '<form id="save_mobile" method="post" onkeypress="return event.keyCode != 13;">';
					$otpHtml .= '<div class="input-group">';
					$otpHtml .= '<input type="hidden" name="customer_id" class="input-box" value="'.$cusDatas['customer_id'].'"/>';
					$otpHtml .= '<input type="hidden" name="mobile" class="input-box" value="'.$cusDatas['mobile'].'"/>';
					$otpHtml .= '<input type="hidden" name="form_id" class="input-box" value="dashboard"/>';
					$otpHtml .= '<input type="text" class="form-control" name="otp_code" id="otp_code" class="input-box" placeholder="Enter the (OTP) "/>';
					$otpHtml .= '<div id="progress_image_cmbv" style="display: none"><img src="'.$cgif.'" alt="loading please wait" /></div>';
					$otpHtml .= '<button type="button" onclick="save_mobile()" class="btn btn-primary otpBtn" id="verifiy_otp_btn">Verify and Save</button>';
					$otpHtml .= '<button type="button" id="resend_otp" onclick="resendOtp()" title="resend Otp" class="btn btn-primary otpBtn">Resend OTP</button>';
					$otpHtml .= '</div></form>';
					/*OTP Form*/
					$response_otp['form'] = $otpHtml;
					$response_otp['status'] = 1;
					$response_otp['message'] = 'OTP has been to sent this Number "'.$cusDatas['mobile'].'". Please enter your OTP and verify';
					//$response_otp['otp_code'] = Mage::getSingleton('core/session')->getCOTP();
				} else {
					$response_otp['status'] = 0;
					$response_otp['message'] = 'This "'.$cusDatas['mobile'].'" mobile number not valid. Please enter a valid mobile number.';
				}
			} else {
				$response_otp['status'] = 0;
				$response_otp['message'] = 'This Mobile number "'.$cusDatas['mobile'].'" Already registered in the website';
			}
		} else {
			$response_otp['status'] = 0;
			$response_otp['message'] = $errors['mobile'];
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($response_otp) );
		return;
		
	}
	/*
	 * Mobile Number Verification
	 *
	 * @return integer
	 */
	public function saveMobileAction(){
		$saveMobile = array();
		$otpCode = $this->getRequest()->getPost();
		//checking customer entered OTP Code
		if($otpCode['otp_code']){
			if(Mage::getSingleton('core/session')->getCOTP() == $otpCode['otp_code']){
				Mage::getModel('customer/customer')->load($otpCode['customer_id'])->setMobile($otpCode['mobile'])->save();
				$saveMobile['status'] = 1;
				$saveMobile['message'] = 'Your OTP has been verified successfully. Please wait for a moment to reload the page.';
				Mage::getSingleton('core/session')->unsCOTP();
			} else {
				$saveMobile['status'] = 0;
				$saveMobile['message'] = 'Incorrect OTP, please enter correct OTP to register in the website.';
			}
		} else {
			$saveMobile['status'] = 0;
			$saveMobile['message'] = 'Please enter OTP and click verify.';
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($saveMobile) );
		return;
	}
	
	/*
	 * Resend OTP
	 *
	 * @return array
	 */
	public function resendOtpAction(){
		$resend = array();
		$resendOtp = $this->getRequest()->getPost();

		//checking customer entered OTP Code
		if($resendOtp['mobile']){
			if($resendOtp['form_id']){
				$response_resend_otp = Mage::helper ( 'sociallogin' )->CURLsendsms($resendOtp['mobile'], $resendOtp['form_id']);
			} else {
				$response_resend_otp = Mage::helper ( 'sociallogin' )->CURLsendsms($resendOtp['mobile'], $form_id = "register");
			}
			if($response_resend_otp){
				$resend['success'] = 1;
				$resend['message'] = 'OTP has been to sent this Number "'.$resendOtp['mobile'].'". Please enter your OTP and verify';
				//$resend['otp_code'] = Mage::getSingleton('core/session')->getOTP();
			} else {
				$resend['success'] = 0;
				$resend['message'] = 'This "'.$resendOtp['mobile'].'" mobile number not valid. Please enter a valid mobile number.';
			}
		} else {
			$resend['success'] = 0;
			$resend['message'] = 'Please enter OTP and click verify.';
		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($resend) );
		return;
	}

	/*
	 * Verification Step 1
	 *
	 * @return array
	 */
	public function verifyMobileAction(){
		$returnArray = array();
		// Form Post data
		$customerData = $this->getRequest()->getPost();
		if($customerData){
			//Checking Mobile number already registered in the website.
			$customerCollection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $customerData['mobile']));
			//Checking Email address already registered in the website.
			$cModel = Mage::getModel('customer/customer')->loadByEmail($customerData['email']);

			if($customerCollection->getFirstItem()->getId()){
				$returnArray['success'] = 0;
				$returnArray['message'] = 'This Mobile number "'.$customerData['mobile'].'" Already registered in the website';
			} elseif($cModel->getId()){
				$returnArray['success'] = 0;
				$returnArray['message'] = 'This Email address "'.$customerData['email'].'" Already registered in the website';
			} else {

				/*Form URL based on the SSL configuration*/
				$currentUrl = Mage::helper('core/url')->getCurrentUrl();
				$secure=strstr($currentUrl,"https");
				if($secure == true) {
					$mobile_verification = Mage::getUrl('sociallogin/index/mobileVerification/' ,array('_secure'=>true));
					$createRedirection=Mage::getUrl('sociallogin/index/createPost/' ,array('_secure'=>true));
				} else {
					$mobile_verification = Mage::getUrl('sociallogin/index/mobileVerification/');
					$createRedirection = Mage::getUrl('sociallogin/index/createPost/');
				} 
				
				//$OTP = strtoupper(substr(md5(uniqid()), 0, 4));
				$response = Mage::helper ( 'sociallogin' )->CURLsendsms($customerData['mobile'], $form_id = "register");

				//Condition based on CURL Response
				if($response == 200){
					$gif = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/base/default/sociallogin/images/ajax-loader.gif";
					/*OTP HTML */
					$formHtml .='<div id="success_msg" class="success-sociallogin">OTP has been to sent this Number "'.$customerData['mobile'].'". Please enter your OTP and verify</div>';
					$formHtml .= '<form id="mobile_verification" action="'.$createRedirection.'" onkeypress="return event.keyCode != 13;">';
					$formHtml .= '<input type="hidden" name="firstname" value="'.$customerData['firstname'].'"/>';
					$formHtml .= '<input type="hidden" name="lastname" value="'.$customerData['lastname'].'"/>';
					$formHtml .= '<input type="hidden" name="email" value="'.$customerData['email'].'"/>';
					$formHtml .= '<input type="hidden" name="mobile" value="'.$customerData['mobile'].'"/>';
					$formHtml .= '<input type="hidden" name="password" value="'.$customerData['password'].'"/>';
					$formHtml .= '<input type="hidden" name="confirmation" value="'.$customerData['confirmation'].'"/>';
					$formHtml .= '<input type="text" name="otpcode" class="form-control input-box required" value="" placeholder="Enter Your OTP"/>';
					$formHtml .= '<div id="progress_image_mbv" style="display: none"><img src="'.$gif.'" alt="loading please wait" /></div>';
					$formHtml .= '<button type="button" id="mobile_verification_btn" onclick="mobileVerification()" title="Verify" class="btn btn-primary popup_click_btn"><span><span>Verify OTP</span></span></button>';
					$formHtml .= '<button type="button" id="resend_otp" onclick="resendOtp()" title="resend Otp" class="btn btn-primary popup_click_btn"><span><span>Resend OTP</span></span></button>';
					$formHtml .= '</form>';
					/*OTP HTML */
					$returnArray['form'] = $formHtml;
					$returnArray['success'] = 1;
					//$returnArray['otp_code'] = Mage::getSingleton('core/session')->getOTP();
				} else {
					$response_otp['status'] = 0;
					$response_otp['message'] = 'This "'.$cusDatas['mobile'].'" mobile number not valid. Please enter a valid mobile number.';
				}
			}
		} else {
			$returnArray['success'] = 0;
			$returnArray['message'] = 'Invalid Customer Information.';

		}
		$this->getResponse ()->setBody ( Zend_Json::encode ($returnArray) );
		return;
	}

	/*
	 * Verification Step 2
	 *
	 * @param $otpData array
	 * @return array
	 */
	public function mobileVerificationAction($otpData){
		$otpVerification = array();
		if($otpData){
			$createRedirection = Mage::getUrl('sociallogin/index/createPost/');
			if(Mage::getSingleton('core/session')->getOTP() == $otpData['otpcode']){
				$otpVerification['success'] = 1;
				$otpVerification['message'] = 'Your OTP has been verified successfully. Please wait for a moment to reload the page.';
				Mage::getSingleton('core/session')->unsOTP();
			} else {
				$otpVerification['success'] = 0;
				$otpVerification['message'] = 'Incorrect OTP, please enter correct OTP to register in the website.';
			}
		} else {
			$otpVerification['success'] = 0;
			$otpVerification['message'] = 'Please enter OTP and click verify.';
		}
		return $otpVerification;
	}
    /*
     * Deals page Action
     */
    public function dealsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Deals'));
        $this->renderLayout();
    }
    /*
     * Brands page Action
     */
    public function brandsAction(){
        $optionid = $this->getRequest ()->getParam('optionid');
        Mage::getSingleton('core/session')->setBrands($optionid);
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Brands'));
        $this->renderLayout();
    }
    /**
     * Customer Register Action
     *
     * @param string $firstname            
     * @param string $lastname            
     * @param string $email            
     * @param string $provider            
     *
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider, $site = null) {
        $customer = Mage::getModel ( 'customer/customer' );
        $standardInfo ['email'] = $email;
        $standardInfo ['first_name'] = $firstname;
        $standardInfo ['last_name'] = $lastname;
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $standardInfo ['email'] );
        if ($customer->getId ()) {
            $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
            $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Your account has been successfully connected through' . ' ' . $provider ) );
			//Set login data in cookie
			if($site){
				$magento = $this->setLoginCookie($site);
				$magento['status'] = 1;
				echo json_encode($magento); die;
			} else {
				$this->setLoginCookie();
			}
            $this->loggedInSuccessfully ();
            return;
        }
        $randomPassword = $customer->generatePassword ( 8 );
        $customer->setId ( null )->setSkipConfirmationIfEmail ( $standardInfo ['email'] )->setFirstname ( $standardInfo ['first_name'] )->setLastname ( $standardInfo ['last_name'] )->setEmail ( $standardInfo ['email'] )->setPassword ( $randomPassword )->setConfirmation ( $randomPassword )->setLoginProvider ( $provider );
        $customer->save ();
        $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s', Mage::app ()->getStore ()->getFrontendName () ) . '. ' . $this->__ ( 'You will receive welcome email with registration info in a moment.' ) );
        $customer->sendNewAccountEmail ();
        $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
		//Set login data in cookie
		if($site){
			$magento = $this->setLoginCookie($site);
			$magento['status'] = 1;
			echo json_encode($magento); die;
		} else {
			$this->setLoginCookie();
		}
        $this->loggedInSuccessfully ();
        return;
    }
    /**
     * Function for logged in successfully redirect the the url.
     *
     * @return string
     */
    public function loggedInSuccessfully() {
        $link = Mage::getSingleton ( 'customer/session' )->getLink ();
        $requestPath = trim ( $link, '/' );
        if (strstr ( $requestPath, 'onepage' )) {
            $this->_redirectUrl ( $requestPath );
        } else {
            $redirect = $this->redirection ();
            $authReferer = Mage::getSingleton ( 'customer/session' )->getBeforeAuthUrl ();
            $redirect = ($redirect) ? $redirect : $authReferer;
            $this->_redirectUrl ( $redirect );
        }
    }
    /**
     * Function to enable redirection to account dashboard or referer link
     *
     * @return string $redirect
     */
    public function redirection() {
        $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
        if ($enableRedirectStatus) {
            $redirect = Mage::helper ( 'customer' )->getAccountUrl ();
        } else {
            $redirect = Mage::getSingleton ( 'customer/session' )->getLink ();
        }
        return $redirect;
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    private function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    /**
     * Twitter login action
     *
     * @return void
     */
    public function twitterloginAction() {
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';
        $twOauthToken = Mage::getSingleton ( 'customer/session' )->getTwToken ();
        $twOauthTokenSecret = Mage::getSingleton ( 'customer/session' )->getTwSecret ();
        $twitterOauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $twOauthToken, $twOauthTokenSecret );
        /**
         * Get Accesss token from @Twitter oAuth
         */
        $oauthVerifier = $this->getRequest ()->getParam ( 'oauth_verifier' );
        $twitterOauth->getAccessToken ( $oauthVerifier );
        $userInfo = $twitterOauth->get ( 'account/verify_credentials' );
        $firstname = $userInfo->name;
        $email = Mage::getSingleton ( 'customer/session' )->getTwemail ();
        $lastname = ' ';
        if (isset ( $userInfo->error ) || $email == '' || $firstname == '') {
            Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
            $url = Mage::helper ( 'customer' )->getAccountUrl ();
            return $this->_redirectUrl ( $url );
        } else {
            $this->customerAction ( $firstname, $lastname, $email, 'Twitter' );
        }
    }
    /**
     * Twitter post action to redirect twitter
     *
     * @return string Returns Twitter page URL for Authendication
     */
    public function twitterpostAction() {
        $twitterEmail = ( string ) $this->getRequest ()->getPost ( 'email_value' );
        Mage::getSingleton ( 'customer/session' )->setTwemail ( $twitterEmail );
        $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ();
        $url = ($url) ? $url : 'Twitter consumer key or secret key is invalid';
        $this->referalSession ();
        $this->getResponse ()->setBody ( $url );
    }
    /**
     * facebook login action - Connect facebook Using oAuth coonection.
     *
     * @return string redirect URL
     *        
     */
    public function fbloginAction() {
       /**
		 * Condition to check the email is retrieved from the user.
		 */
       if($this->getRequest()->getParam('email')) {
	      $email = $this->getRequest()->getParam('email');
	      $firstName = $this->getRequest()->getParam('fname');
	      $lastName = $this->getRequest()->getParam('lname');
	      $data = $this->getRequest()->getParam('fb');
		  $site = $this->getRequest()->getParam('site');
		  if($site){
			$this->customerAction($firstName, $lastName, $email, 'Facebook', $site);
		  } else {
			$this->customerAction($firstName, $lastName, $email, 'Facebook');
		  }
	   } else {
	      Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
	      $this->_redirect();
	   }
    }
    /**
     * Google login action - Connect Google Using oAuth coonection.
     *
     * @return string redirect URL
     */
    public function googlepostAction() {
        require_once 'sociallogin/src/Google_Client.php';
        require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
        $googleClientId = Mage::getStoreConfig ( 'sociallogin/google/google_id' );
        $googleClientSecret = Mage::getStoreConfig ( 'sociallogin/google/google_secret' );
        $googleRedirectUrl = Mage::getUrl () . 'sociallogin/index/googlepost/';
        /**
         * Create the object @var $gClient from google client
         */
        $gClient = new Google_Client ();
        $gClient->setApplicationName ( 'login' );
        $gClient->setClientId ( $googleClientId );
        $gClient->setClientSecret ( $googleClientSecret );
        $gClient->setRedirectUri ( $googleRedirectUrl );
        $googleOauthV2 = new Google_Oauth2Service ( $gClient );
        $token = Mage::getSingleton ( 'core/session' )->getGoogleToken ();
        $reset = $this->getRequest ()->getParam ( 'reset' );
        if ($reset) {
            unset ( $token );
            $gClient->revokeToken ();
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
        }
        $code = $this->getRequest ()->getParam ( 'code' );
        if (isset ( $code )) {
            $gClient->authenticate ( $code );
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $gClient->getAccessToken () );
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
            $this->_redirectUrl ( $googleRedirectUrl );
            return;
        }
        /**
         * If $token is non-empty set the access token
         */
        if (isset ( $token )) {
            $gClient->setAccessToken ( $token );
        }
        if ($gClient->getAccessToken ()) {
            $user = $googleOauthV2->userinfo->get ();
            $email = filter_var ( $user ['email'], FILTER_SANITIZE_EMAIL );
            $token = $gClient->getAccessToken ();
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $token );
        } else {
            $authUrl = $gClient->createAuthUrl ();
        }
        if (isset ( $authUrl )) {
            $this->_redirectUrl ( $authUrl );
        } else {
            $firstname = $user ['given_name'];
            $lastname = $user ['family_name'];
            $email = $user ['email'];
            if ($email == '') {
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Google Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                
                return $this->_redirectUrl ( $url );
            } else {
                $this->customerAction ( $firstname, $lastname, $email, 'Google' );
            }
        }
    }
    /**
     * Set customer session Auth url to http_referrer
     *
     * @return string
     */
    public function referalSession() {
        $redirect = Mage::app ()->getRequest ()->getServer ( 'HTTP_REFERER' );
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( $redirect );
    }
    /**
     * Validation for Tax/Vat field for current store
     *
     * @return boolean true|false
     */
    public function _isVatValidationEnabled($store = null) {
        return Mage::helper ( 'customer/address' )->isVatValidationEnabled ( $store );
    }
    /**
     * Customer welcome function
     *
     * Its used for print welcome message once successfully logged in
     *
     * @return string $successUrl
     */
    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );
        $customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
        return $this->redirection ();
    }
    /**
     * Customer login Action
     * validate the social login form posted values if the user is registered user or not
     *
     * @return string Redirect URL.
     */
    public function customerloginpostAction() {
        $session = $this->_getCustomerSession ();
        $login ['username'] = $this->getRequest ()->getParam ( 'mobile' );
        $login ['password'] = $this->getRequest ()->getParam ( 'password' );
        if ($session->isLoggedIn ()) {
            $message = 'Already loggedin';
            $this->getResponse ()->setBody ( $message );
            return;
        }
        if (($this->getRequest ()->isPost ()) && ! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
            try {
				//Customer id using customer mobile number
				$customerCollections = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $login ['username']));
				if($customerCollections->getFirstItem()->getId()){
					$authenticate = Mage::helper('core')->validateHash($login ['password'], $customerCollections->getFirstItem()->getPasswordHash());
					if(!$authenticate) {
						Mage::throwException('Invalid mobile number or password.');
						return;
					}
				} else {
					Mage::throwException('This mobile number not registered in the website.');
					return;
				}

				$session->loginById ( $customerCollections->getFirstItem()->getId(), $login ['password'] );
				//Set login data in cookie
				$this->setLoginCookie();
                if ($session->getCustomer ()->getIsJustConfirmed ()) {
                    $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $session->getCustomer (), true ) );
                }
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Exception warning message when invalid email id used.
                 */
                switch ($e->getCode ()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
                        $value = Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $login ['username'] );
                        $message = Mage::helper ( 'customer' )->__ ( 'Account Not Confirmed', $value );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
                        $message = $this->__ ( 'Invalid Email Address or Password' );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    default :
                        $message = $e->getMessage ();
                        $this->getResponse ()->setBody ( $message );
                }
                $session->setUsername ( $login ['username'] );
            } catch ( Exception $e ) {
                return $e;
            }
            /**
             * After successful logged-in, its redirect to the respective page.
             */
            if ($session->getCustomer ()->getId ()) {
                $link = Mage::getSingleton ( 'customer/session' )->getLink ();
                $requestPath = '';
                $requestPath = trim ( $link, '/' );
                if (strstr ( $requestPath, 'onepage' )) {
                    $this->getResponse ()->setBody ( $requestPath );
                } else {
                    $redirect = $this->redirection ();
                    $this->getResponse ()->setBody ( $redirect );
                }
            }
        }
    }
    /**
     * Customer Register Action
     * validate the social regiter form posted values
     *
     * @return string Redirect URL.
     */
    public function createPostAction() {
		$isJustConfirmed = false;
        $customer = Mage::getModel ( 'customer/customer' );
        $session = $this->_getCustomerSession ();
        if ($session->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
        }
        $enableCaptcha = Mage::getStoreConfig ( 'customer/captcha/enable' );
        /**
         * Condition to check whether captcha is enabled.
         */
        if ($enableCaptcha == '1') {
            $newcaptcha = $this->getRequest ()->getPost ( 'captcha' );
            $_captcha = Mage::getModel ( 'customer/session' )->getData ( 'user_create_word' );
            $captchaImgData = $_captcha ['data'];
            if ($newcaptcha ['user_create'] != $captchaImgData) {
                $this->getResponse ()->setBody ( $this->__ ( 'Incorrect CAPTCHA.' ) );
                return;
            }
        }
        /**
         * Preventing the Cross-site Scripting (XSS) injection from an user inputs
         */
        $session->setEscapeMessages ( true );
        if ($this->getRequest ()->isPost ()) {
			
            $errors = array ();
			if(!$this->getRequest ()->getPost ( 'customer' )){ 
				$response = $this->mobileVerificationAction($this->getRequest()->getPost());
				if($response['success'] == 0){
					$this->getResponse ()->setBody ( Zend_Json::encode ($response) );
					return;
				}
			}
            if (! $customer = Mage::registry ( 'current_customer' )) {
                $customer = Mage::getModel ( 'customer/customer' )->setId ( null );
            }
            $customerForm = Mage::getModel ( 'customer/form' );
            $customerForm->setFormCode ( 'customer_account_create' )->setEntity ( $customer );
            $customerData = $customerForm->extractData ( $this->getRequest () );
            $sso =  $this->getRequest ()->getPost ( 'customer' );

            $isSubscribed = $this->getRequest ()->getParam ( 'is_subscribed', false );
            $customer = $this->isSubscribedtrue ( $isSubscribed, $customer );
            $customer->getGroupId ();
            /**
             * Condition to check the create address field is present.
             */
            if ($this->getRequest ()->getPost ( 'create_address' )) {
                $address = Mage::getModel ( 'customer/address' );
                $addressForm = Mage::getModel ( 'customer/form' );
                $addressForm->setFormCode ( 'customer_register_address' )->setEntity ( $address );
                $addressData = $addressForm->extractData ( $this->getRequest (), 'address', false );
                $addressErrors = $addressForm->validateData ( $addressData );
                if ($addressErrors === true) {
                    $address->setId ( null )->setIsDefaultBilling ( $this->getRequest ()->getParam ( 'default_billing', false ) )->setIsDefaultShipping ( $this->getRequest ()->getParam ( 'default_shipping', false ) );
                    $addressForm->compactData ( $addressData );
                    $customer->addAddress ( $address );
                    $addressErrors = $address->validate ();
                    if (is_array ( $addressErrors )) {
                        $errors = array_merge ( $errors, $addressErrors );
                    }
                } else {
                    $errors = array_merge ( $errors, $addressErrors );
                }
            }
            try {
                $customerErrors = $customerForm->validateData ( $customerData );
                if ($customerErrors !== true) {
                    $errors = array_merge ( $customerErrors, $errors );
                }
                $customerForm->compactData ( $customerData );
                $customer->setPassword ( $this->getRequest ()->getPost ( 'password' ) );
                /**
                 * check magento version 1.9.1 and above
                 */
                $customer->setPasswordConfirmation ( $this->getRequest ()->getPost ( 'confirmation' ) );
                $customerErrors = $customer->validate ();
                /**
                 * If @var $validationResult is true dispatching the event into customer_register_success
                 *
                 * @return string URL
                 */
                $validationResult = count ( $errors ) == 0;
                if (true === $validationResult) {
                    $customer->save ();
                    if(!$sso){
                        Mage::dispatchEvent ( 'customer_register_success', array (
                                'account_controller' => $this,
                                'customer' => $customer 
                        ) );
                    }
                    if ($customer->isConfirmationRequired ()) {
						$isJustConfirmed = true;
                        $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
                        $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
                        $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                                '_secure' => true 
                        ) ) );
                    } else {
                        $session->setCustomerAsLoggedIn ( $customer );
                        /*
                         * Cookies set here
                         */
                            $magento = array();
                            //Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
                            $customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
                            $magento['userID'] = $session->getCustomerId();
                            $magento['userEmail'] = $customer->getEmail();
                            $magento['firstname'] = $customer->getFirstname();
                            $magento['lastname'] = $customer->getLastname();
							$magento['mobile'] = $customer->getMobile();
							$magento['login_provider'] = $customer->getLoginProvider();
                        // set cookie
                        $cookie = Mage::getSingleton('core/cookie');
						$domain = Mage::helper ( 'sociallogin' )->getHostDomain();
						//$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.metacrust.com' : false;
						$cookie->set('token', $session->getCustomerId(), 10800, '/', $domain, TRUE, TRUE);
						$cookie->set('userDatas', serialize($magento), 10800, '/', $domain, TRUE, TRUE);
                        /* SSO registration */
						$returnData['token'] = $session->getCustomerId();
						$returnData['userDatas'] = serialize($magento);
                        $this->ssoRegistration($customer, $sso, $isJustConfirmed, $returnData);
                        $url = $this->_welcomeCustomer ( $customer, $isJustConfirmed);
                        if(!$sso){
                            $this->getResponse ()->setBody ( $url );
                        }
                    }
                } else {
                    $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                    $session->addError ( $this->__ ( 'Invalid customer data' ) );
                    /* SSO registration error message */
                    $ssoerror = $this->__ ( 'Invalid customer data' );
                    $this->ssoError($ssoerror, $sso);
                }
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Throws the exception if email already exists
                 *
                 * @return string $message
                 */
                $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                if ($e->getCode () === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__ ( 'Email already exists' );
                    if(!$sso){
                        $this->getResponse ()->setBody ( $message );
                        $session->setEscapeMessages ( false );
                    }
                    /* SSO registration error message */
                    $this->ssoError($message, $sso);
                    return;
                } else {
                    $message = $e->getMessage ();
                    if(!$sso){
                        $this->getResponse ()->setBody ( $message );
                    }
                    /* SSO registration error message */
                    $this->ssoError($message, $sso);
                    return;
                }
                $session->addError ( $message );
            } catch ( Exception $e ) {
                $session->setCustomerFormData ( $this->getRequest ()->getPost () )->addException ( $e, $this->__ ( 'Cannot save the customer.' ) );
                    /* SSO registration error message */
                    $ssoerror = $e.'-'.$this->__ ( 'Cannot save the customer.');
                    $this->ssoError($ssoerror, $sso);
            }
        }
        if (! empty ( $message )) {
            if(!$sso){
                $this->getResponse ()->setBody ( $message );
            }
            /* SSO registration error message */
            $this->ssoError($message, $sso);
        }
		if($response['success'] == 1){
			$this->getResponse ()->setBody ( Zend_Json::encode ($response) );
			return;
		}
        if(!$sso){
            $this->redirection();
        }
    }
    /**
     * Function to set subscrption to customer
     *
     * @return array $customer
     */
    public function isSubscribedtrue($isSubscribed, $customer) {
        if ($isSubscribed) {
            $customer->setIsSubscribed ( 1 );
        }
        return $customer;
    }
    /**
     * ForgetPassword Action - Forget password action for forget password form
     *
     * @return string $message.
     */
    public function forgotPasswordPostAction() {
        $email = ( string ) $this->getRequest ()->getParam ( 'forget_password' );
        $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        if ($customer->getId ()) {
            try {
                $newResetPasswordLinkToken = Mage::helper ( 'customer' )->generateResetPasswordLinkToken ();
                $customer->changeResetPasswordLinkToken ( $newResetPasswordLinkToken );
                $customer->sendPasswordResetConfirmationEmail ();
            } catch ( Exception $exception ) {
                $this->_getCustomerSession ()->addError ( $exception->getMessage () );
                return;
            }
            $message = $this->__ ( 'You will receive an email ' ) . $email . ' ';
            $message = $message . $this->__ ( 'with a link to reset your password' );
        } else {
            $message = $this->__ ( 'If there is no account associated with this email please enter your correct email-id' );
        }
        $this->getResponse ()->setBody ( $message );
    }

    public function ssoRegistration($customer, $sso, $isJustConfirmed = false, $returnData){
        //$isJustConfirmed = true;
		$customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
        if($sso){
            $returnData['status'] = 1;
            echo json_encode($returnData);
            return;
        }
    }

    public function ssoError($ssoerror, $sso){
        if($sso){
            $R['status'] = 0;
            $R['message'] = $ssoerror;
            echo json_encode($R);
            return;
        }
    }
	
	/*
	 * No Products Form Submission
	 * Sent an email to general store email address
	 */
 	public function noproductsAction(){
		$noProductsData = $this->getRequest()->getParams();
		$post = array("fullname"=>$noProductsData['fullname'],"mobilenumber"=>$noProductsData['mobilenumber'],"emailaddress"=>$noProductsData['emailaddress'], "lookingfor"=>$noProductsData['lookingfor']);
		if($post){
			$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
			$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( Mage::getStoreConfig('sociallogin/noproducts/email_template') );
			$emailTemplate->setSenderName ( Mage::getStoreConfig('trans_email/ident_general/name') );
			$emailTemplate->setSenderEmail ( Mage::getStoreConfig('trans_email/ident_general/email') );
			$emailTemplate->setDesignConfig ( array (
				'area' => 'frontend' 
			) );
			$emailTemplate->getProcessedTemplate ( $post );

			try {
				$emailTemplate->send ( Mage::getStoreConfig('trans_email/ident_general/email'), Mage::getStoreConfig('trans_email/ident_general/name'), $post );
				$response['status'] = 1;
				$response['message'] = $this->__('Your request was submitted and we will be responded to you as soon as possible. Thank you!');
                $this->getResponse ()->setBody ( Zend_Json::encode ($response) );
				return;
			} catch (Exception $e) {
                $translate->setTranslateInline(true);
				$response['status'] = 0;
				$response['message'] = $this->__('Unable to submit your request. Please, try again later');
                $this->getResponse ()->setBody ( Zend_Json::encode ($response) );
				return;
            }
		} else {
			$response['status'] = 0;
			$response['message'] = $this->__('Unable to submit your request. Please, try again later');
            $this->getResponse ()->setBody ( Zend_Json::encode ($response) );
			return;
        }
		
	}
	/*
	 * Altius Form Submission
	 * Sent an email to general store email address
	 */
 	public function altiusformAction(){
		$altiusForm = $this->getRequest()->getPost();
		$post = array("name"=>"Metcrust","altius_name"=>$altiusForm['altius_name'],"company_name"=>$altiusForm['company_name'],"phonenumber"=>$altiusForm['phonenumber'],"emailaddress"=>$altiusForm['emailaddress'],"location_requiement"=>$altiusForm['location_requiement'],"modelandbrand"=>$altiusForm['modelandbrand'],"purpose"=>$altiusForm['purpose'],"dateofrequirement"=>$altiusForm['dateofrequirement'],);
		if($post){
			$translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
			$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( Mage::getStoreConfig('sociallogin/altius/email_template') );
			$emailTemplate->setSenderName ( Mage::getStoreConfig('trans_email/ident_general/name') );
			$emailTemplate->setSenderEmail ( Mage::getStoreConfig('trans_email/ident_general/email') );
			$emailTemplate->setDesignConfig ( array (
				'area' => 'frontend' 
			) );
			$emailTemplate->getProcessedTemplate ( $post );

			try {
				$emailTemplate->send ( Mage::getStoreConfig('trans_email/ident_general/email'), Mage::getStoreConfig('trans_email/ident_general/name'), $post );
				Mage::getSingleton('core/session')
                    ->addSuccess($this->__('Your details has been submitted and we will be respond to you as soon as possible. Thank you!'));
                $this->_redirectUrl($altiusForm['page_url']);

                return;
			} catch (Exception $e) {
                $translate->setTranslateInline(true);
 
                Mage::getSingleton('core/session')->addError($this->__('Unable to submit your Enquiry. Please, try again later'));
                $this->_redirectUrl($altiusForm['page_url']);
                return;
            }
		} else {
            $this->_redirectUrl($altiusForm['page_url']);
        }
		
	}
	/**
	 * Get state collection
	 * @param string $countryCode
	 * @return array
	 */
	public function stateAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $html = "";
        $statearray = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countrycode)->load();
        if(count($statearray) > 0){
			$html .= "<span><i class='fa fa-map-marker'></i></span>";
            $html .= "<select name='customer[customerstate]' id='customer:customerstate' class='form-control validate-select'><option value=''>--Choose your city--</option>";
            foreach ($statearray as $_state) {
                $html .= "<option value='" . $_state->getDefaultName() . "'>" . $_state->getDefaultName() . "</option>";
            }
            $html .= "</select>";
        } else {
			$html .= "<span><i class='fa fa-map-marker'></i></span>";
            $html .= "<input name='customerstate' placeholder='".$this->__('Enter your city')."' id='customerstate' title='".$this->__('State')."' value='' class='form-control required-entry' type='text' />";
        }
        echo $html;
    }
	
	/**
	 * Check Customer mobile and email from New-equipment / rentnsell website.
	 */
	public function ciCustomerAction() {
		$customerData = $this->getRequest()->getPost();
        //Checking Mobile number already registered in the website.
		$customerCollection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('mobile', array('eq' => $customerData['mobile']));
		//Checking Email address already registered in the website.
		$cModel = Mage::getModel('customer/customer')->loadByEmail($customerData['email']);
		
		if($cModel->getId()){
			$returnArray['status'] = 0;
			$returnArray['message'] = 'This Email address "'.$customerData['email'].'" Already registered in the website';
		} elseif(count($customerCollection) == 1){
			$returnArray['status'] = 0;
			$returnArray['message'] = 'This Mobile number "'.$customerData['mobile'].'" Already registered in the website';
		} else {
			$returnArray['status'] = 1;
		}
		
		echo json_encode($returnArray); die;
    }

	/*
	 * Get customer data from customer_session and set in cookie
	 */
	public function setLoginCookie($site = null){
		/*
		 * Cookies set here
		 */
		$session = $this->_getCustomerSession ();
		$magento = array();
		//Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
		$customer = Mage::getModel('customer/customer')->load($session->getCustomerId());
		$magento['userID'] = $session->getCustomerId();
		$magento['userEmail'] = $customer->getEmail();
		$magento['mobile'] = $customer->getMobile();
		$magento['firstname'] = $customer->getFirstname();
		$magento['lastname'] = $customer->getLastname();
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
			
		// set cookie
		$cookie = Mage::getSingleton('core/cookie');
		$domain = Mage::helper ( 'sociallogin' )->getHostDomain();
		//$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? '.metacrust.com' : false;
		$cookie->set('token', $session->getCustomerId(), 10800, '/', $domain, TRUE, TRUE);
		$cookie->set('userDatas', serialize($magento), 10800, '/', $domain, TRUE, TRUE);
		//Single SingIn 
		if($site){
			return $magento;
		}
	}
}