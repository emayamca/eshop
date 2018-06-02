<?php


class Pumcp_Payments_Model_Observer extends Mage_Core_Block_Abstract {

    public function output_pumcp_redirect(Varien_Object $observer) {
		$pumcp = Mage::getModel('pumcp/checkout');
        if (isset($_POST['payment']['method']) && $_POST['payment']['method'] == "pumcp" && $pumcp->getAdnlInfo()== 'citruspay') {
            
			$environment = Mage::getStoreConfig('payment/pumcp/environment');
			
        	$controller = $observer->getEvent()->getData('controller_action');
            
            $result = Mage::helper('core')->jsonDecode(
                $controller->getResponse()->getBody('default'),
                Zend_Json::TYPE_ARRAY
            );

            //un minified https://sboxcontext.citruspay.com/static/kiwi/app-js/icp.js
            //for live https://checkout-static.citruspay.com/kiwi/app-js/icp.min.js			
            $js = '<script>
                var chead = $$("head")[0];
                var cscript = new Element("script", { type: "text/javascript", id:"context", src: "https://sboxcontext.citruspay.com/static/kiwi/app-js/icp.js" });
                chead.appendChild(cscript);
                </script>';
            if ($environment == "production")
        	{
				 $js = '<script>
                var chead = $$("head")[0];
                var cscript = new Element("script", { type: "text/javascript", id:"context", src: "https://checkout-static.citruspay.com/kiwi/app-js/icp.min.js" });
                chead.appendChild(cscript);
                </script>';
			}
			
			
            if (empty($result['error'])) {
                $controller->loadLayout('checkout_onepage_review');
                $html = $js;
                $html .= $controller->getLayout()->createBlock('pumcp/redirect')->toHtml();

                $result['update_section'] = array(
                    'name' => 'pumcpiframe',
                    'html' => $html
                );
                $result['redirect'] = false;
                $result['success'] = false;
                $controller->getResponse()->clearHeader('Location');
                $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                
                //Mage::log("Obsever End - Result: ".print_r($result,true));
            }
        }
        return $this;
    }
}
?>
