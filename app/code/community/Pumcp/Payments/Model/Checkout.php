<?php
class Pumcp_Payments_Model_Checkout extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = 'pumcp';
	protected $title = 'Citrus Pay';
    protected $_paymentMethod = 'shared';
	protected $_usePumcp = TRUE;
	
	protected $adnlinfo='*';
	
	protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;   
	
	
	protected function _construct()
    {
		parent::_construct();
        $this->_init('pumcp/checkout');				
		$this->isEnabled();	
    }
	
	public function isAvailable($quote = null) { 
		$this->isEnabled();		
		//Mage::log('Pumcp test '.$this->adnlinfo, Zend_Log::EMERG);	
		return $this->_usePumcp;	  
 	}
	
	public function getTitle()
	{
		$this->isEnabled();
		return $this->title;
	}
	
	private function isEnabled()
    {	 
		$active =       Mage::getStoreConfig('payment/pumcp/active');
        $env =          Mage::getStoreConfig('payment/pumcp/environment');
		$pumkey = 		Mage::getStoreConfig('payment/pumcp/pumkey');	
		$pumsalt =		Mage::getStoreConfig('payment/pumcp/pumsalt');	
		$cvurl =		Mage::getStoreConfig('payment/pumcp/vanityurl');	
		$caccess =		Mage::getStoreConfig('payment/pumcp/accesskey');	
		$csecret =		Mage::getStoreConfig('payment/pumcp/apikey');	
		$pumroute =		Mage::getStoreConfig('payment/pumcp/routepum');	
		$croute =		Mage::getStoreConfig('payment/pumcp/routecitrus');	
		
		if($active == 0)
		{
			$this->_usePumcp = FALSE;
            Mage::log('Pumcp not enabled', Zend_Log::EMERG);           
            return $this->_usePumcp;
		}
		
		if(empty($pumkey) && empty($pumsalt) && empty($cvurl) && empty($caccess) && empty($csecret))
		{
			$this->_usePumcp = FALSE;
            Mage::log('Pumcp empty configuration, either PayUMoney or Citrus needs to be configured.', Zend_Log::EMERG);
            
            return $this->_usePumcp;
		}
		
		if(($pumroute + $croute) != 100)
		{
			$this->_usePumcp = FALSE;
            Mage::log('Pumcp Routing values not properly configured.', Zend_Log::EMERG);            
            return $this->_usePumcp;
		}
		//Choose either of the gateway as per configuration
		
		if(!empty($pumkey) && !empty($pumsalt) && empty($cvurl) && empty($caccess) && empty($csecret))
		{
			$this->adnlinfo = 'payumoney';
			$this->title = 'PayU Money';
			return $this->_usePumcp;
		}
		elseif(empty($pumkey) && empty($pumsalt) && !empty($cvurl) && !empty($caccess) && !empty($csecret))
		{
			$this->adnlinfo = 'citruspay';
			return $this->_usePumcp;
		}
		
		//read db for %age
		$pCitrus=0;
		$pPayu=0;
		
		$resource = Mage::getSingleton('core/resource');

	    $write = $resource->getConnection('core_write');
		
		$query = "SELECT COUNT(*) / T.total * 100 AS percentPayU FROM sales_flat_order_payment as I , (SELECT COUNT(*) AS total FROM sales_flat_order_payment where amount_paid > 0) AS T WHERE method = 'pumcp' and additional_information like '%payumoney%' and amount_paid > 0";
		
		$readresult=$write->query($query); 
        if ($row = $readresult->fetch() ) {
            $pPayu = $row['percentPayU'];
        }
		
		$query = "SELECT COUNT(*) / T.total * 100 AS percentCitrus FROM sales_flat_order_payment as I , (SELECT COUNT(*) AS total FROM sales_flat_order_payment where amount_paid > 0) AS T WHERE method = 'pumcp' and additional_information like '%citruspay%' and amount_paid > 0";

		$readresult=$write->query($query); 
        if ($row = $readresult->fetch() ) {
            $pCitrus = $row['percentCitrus'];
        }
		
		if($pCitrus > $croute && $pPayu <= $pumroute) {
			$this->title = 'PayU Money';
			$this->adnlinfo = 'payumoney';
		}
		elseif($pCitrus <= $croute && $pPayu  >$pumroute) {
			$this->adnlinfo = 'citruspay';
		}
		else {
			if($pPayu >= $pCitrus)
				$this->adnlinfo = 'citruspay';
			else
			{
				$this->title = 'PayU Money';
				$this->adnlinfo = 'payumoney';					
			}
		}
		return $this->_usePumcp;
	}
	
	public function getAdnlInfo()
	{
		$this->isEnabled();
		return $this->adnlinfo;	
	}
	
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('pumcp/redirect');
    }

    //get Checkout Display
    public function getDisplay() {
        $display = true;
        return $display;
    }

    //get order
    public function getQuote() {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }
    
    public function capture(Varien_Object $payment, $amount)
    {
    	$payment->setStatus(self::STATUS_APPROVED)
    	->setLastTransId($this->getTransactionId());
    
    	return $this;
    }
    
    public function cancel(Varien_Object $payment)
    {
    	$payment->setStatus(self::STATUS_DECLINED)
    	->setLastTransId($this->getTransactionId());
    
    	return $this;
    }   

}