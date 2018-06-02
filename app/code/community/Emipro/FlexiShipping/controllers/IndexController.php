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
class Emipro_FlexiShipping_IndexController extends Mage_Core_Controller_Front_Action
{
       public function indexAction()
    {
		
		$request=Mage::app()->getRequest()->getParams();
		$cookie = Mage::getModel('core/cookie');
		$period = time()+(86400*30);
		$data=Mage::helper("emipro_flexishipping")->perproduct_shipcost($request);
		
		unset($_COOKIE['country_code']);
		unset($_COOKIE['zip_code']);
		$cookie->set('country_code',$request["country"],$period, '/');
		$cookie->set('zip_code', $request["postcode"],$period, '/');
		$currency=Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		
					$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
					$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
					$currency_symbol = Mage::app()->getLocale()->currency($baseCurrencyCode)->getSymbol();
					$current_symbol = Mage::app()->getLocale()->currency($currentCurrencyCode)->getSymbol();
					$final_amount=Mage::helper('directory')->currencyConvert($data["final_cost"], $baseCurrencyCode, $currentCurrencyCode);
		
		if($request["postcode"]!="")
		{

		if($data["status"] == 1)
		{
			
			$message="Shipping available with ".$currency.$final_amount." ship cost at ".$request["postcode"];
			 echo "<label class='success' style='font-weight: 500;font-size:115%'>".$message."</label><a  title='Change'  class='link' id='change_checkzip' onclick='changeShip()' style='margin:5px;cursor: pointer;' ><span><span>Change</span></span>
			</a>";
		}
		else
		{
		
			$error="Shipping is not available at ".$request["postcode"];
			echo "<label class='error'  style='font-weight: 500;font-size:115%'>".$error."</label><a title='Change'  class='link' id='change_checkzip' onclick='changeShip()' style='margin:5px;cursor: pointer;' ><span><span>Change</span></span>
			</a>";
			
		}	
		}
		else
		{
			$error="Please enter valid Zip code";
				echo "<label class='error'  style='font-weight: 500;font-size:115%'>".$error."</label><a title='Change'  class='link' id='change_checkzip' onclick='changeShip()' style='margin:5px;cursor: pointer;' ><span><span>Change</span></span>
			</a>";

		}
	
	}
	public function flexishippingAction()
		{
			$modules = Mage::getConfig()->getNode('modules')->children();
			$modulesArray = (array)$modules;
			if(isset($modulesArray['Emipro_FlexiShipping']) && $modulesArray['Emipro_FlexiShipping']->active=='true')
			{
				$this->getResponse()->setBody("true");
			}
			else
			{
				$this->getResponse()->setBody("false");
			}
		}
}
