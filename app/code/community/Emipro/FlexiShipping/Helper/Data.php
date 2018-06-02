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
class Emipro_FlexiShipping_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function __construct()
	{
		
		include_once Mage::getBaseDir('code').'/community/Emipro/FlexiShipping/includes/EmiproShippingHelper.php';	
	}
	public function getProductIds()
	{
		
		$ids=Mage::getModel('emipro_flexishipping/flexiShipping')->getCollection()->getAllIds();
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('emipro_flexi_shipping_rules_products');
		$m= new Mage;
		$version=$m->getVersion();
		foreach ($ids as $id)
		{
		$data=Mage::getModel('emipro_flexishipping/flexiShipping')->load($id);
			if($data->getData("is_active")==1)
			{
				$web_id=explode(",",$data->getData("website_id"));
				$pids=$data->getMatchingProductIds1($web_id);
		
				$ship_id=$id;
				$fields1['ship_id']=$ship_id;  
				$condition = array($writeConnection->quoteInto('ship_id=?', $ship_id));
				$writeConnection->DELETE($table, $condition);  
				$writeConnection->commit();
				if($version=="1.7.0.2" || $version=="1-6-2-0")
				{
					$this->oldversion_insertIds($ship_id,$pids);
				}
				else
				{
					$this->insertIds($ship_id,$pids);
				}
			}
		}	
	}
	public function applyrule($id)
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('emipro_flexi_shipping_rules_products');
		$m= new Mage;
		$version=$m->getVersion();
		$data=Mage::getModel('emipro_flexishipping/flexiShipping')->load($id);
		if($data->getData("is_active")==1)
		{
			$web_id=explode(",",$data->getData("website_id"));
			
				$pids=$data->getMatchingProductIds1($web_id);
		
				$ship_id=$id;
				$fields1['ship_id']=$ship_id;  
				$condition = array($writeConnection->quoteInto('ship_id=?', $ship_id));
				$writeConnection->DELETE($table, $condition);  
				$writeConnection->commit();
				if($version=="1.7.0.2" || $version=="1-6-2-0")
				{
					$this->oldversion_insertIds($ship_id,$pids);
				}
				else
				{
					$this->insertIds($ship_id,$pids);
				}
			}
			
	}
	public function oldversion_insertIds($ship_id,$pids)
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('emipro_flexi_shipping_rules_products');
		foreach ($pids as $value)
			{
				foreach($prod_collection as $value)
					{
						if($value->getEntityId() == $key)
						{
								$fields=array(); 
								$fields['entity_id']= $key;
								$fields['ship_id']=$ship_id; 	
								$writeConnection->insert($table,$fields);  
								$writeConnection->commit();
						}
					}
			}
	}
	
	public function insertIds($ship_id,$pids)
	{
		$seller_admin_id=Mage::getSingleton('emipro_flexishipping/flexiShipping')->load($ship_id)->getSellerAdminId();
		$vendor_id=Mage::helper('marketplace')->getProuctVendorid($seller_admin_id);
		$sellerid=Mage::helper('marketplace')->getSellerIdfromLoginUser();
		
		$prod_collection=Mage::getModel('catalog/product')->getCollection();
		
		if(!$sellerid)
		{
			
			//$prod_collection->addAttributeToFilter("vendor_id",array('null' => true));
		
			$Ids=Mage::getSingleton('catalog/product')->getCollection()->addAttributeToFilter("vendor_id",array('neq' => null))->getAllIds();
			$prod_collection->addAttributeToFilter("entity_id",array('nin'=>$Ids));

		}
		else
		{
			$prod_collection->addAttributeToFilter("vendor_id",array('eq'=>$vendor_id));
		}
		
		
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('emipro_flexi_shipping_rules_products');
		

		foreach($pids as $key=>$value)
			{
				if($value[1]==1 && $value[0]==1)
				{	
					foreach($prod_collection as $value)
					{
						if($value->getEntityId() == $key)
						{
								$fields=array(); 
								$fields['entity_id']= $key;
								$fields['ship_id']=$ship_id; 	
								$writeConnection->insert($table,$fields);  
								$writeConnection->commit();
						}
					}
				}
			}
	}
		
/* Useful for Product Desciption Page*/
	public function perproduct_shipcost($request)
	{
		$status=0;
		$id=$request["id"];
		//$id=$request;
		$final_cost=0;
		$rule_count=0;
		$collection=Mage::getModel('emipro_flexishipping/flexiShipping1')->getCollection();
		$collection->getSelect()->join(array("ship"=>Mage::getConfig()->getTablePrefix()."emipro_flexi_shipping_rules"),"main_table.ship_id=ship.ship_id")->where("main_table.entity_id=".$id)->order("ship.sort_order");
		foreach($collection as $val)
		{
			$customer=explode(",",$val["customer_group_id"]);
			$website_id=explode(",",$val["website_id"]);
			$date=$this->check_date_range($val["from_date"],$val["to_date"]);
			$country=$this->perproduct_getCountry($val["all_countries"],$val["specific_countries_ids"],$request["country"],$val["shipto"],$request);
			$zip=$this->perproduct_checkzip($val["shipto"],$request);
			if($val["is_active"]==1  && $date==1 && ($country==1 || $zip==1)  && $rule_count==0)
			{
				$cost=$this->perproduct_getShip_cost($val["fees"],$id);
				$status=1;
				$rule_count=1;
				$final_cost=$cost;
			}
		}
		$pro_cost=array("final_cost"=>$final_cost,"status"=>$status);
		return $pro_cost;
	}
	public function perproduct_getCountry($all_country,$specific_countries,$country_id,$zip,$request)
	{
		if($all_country==0)
		{ 
			if(strpos($zip,$country_id) !== false)
			{
				return $this->perproduct_checkzip($zip,$request);
			}	
			else
			{
			return 1;
			} 
		}
		else
		{
			$countryId =$country_id;
			$country=explode(",",$specific_countries);
			if(in_array($countryId,$country))
			{
				if(strpos($zip,$country_id) !== false)
				{
				return $this->perproduct_checkzip($zip,$request);
				}	
				return true;
				}
		}
	}
	public function perproduct_checkzip($zip,$request)
	{
		if(!empty($zip)){
		$emipro=new EmiproShippingHelper();
		$postcode =$request["postcode"];
		$country_id=$request["country"];
		
		$data=$emipro->_addressMatch($zip,$country_id,$postcode);
		return $data;}
		else{return true;}
	}
	public function perproduct_getShip_cost($fees,$id)
	{
		$price=0;
		$product = Mage::getModel('catalog/product')->load($id);
		$weight = $product->getWeight();
		$pro_price=$product->getPrice();
		$qty="1";
		if($pro_price!=0)
		{
		$str=str_replace(array("{prod.qty}","{prod.weight}","{prod.price}"),array($qty,ceil($weight),$pro_price),$fees);
		$price=$this->calculate_string($str);
		return $price;	
		}
		
	}
	public function getVendor()
	{
		$cartinfo = Mage::getModel('checkout/cart')->getQuote();
		foreach($cartinfo->getAllItems() as $item)
		{
			$id=$item->getProductId();
			$_product = Mage::getSingleton('catalog/product')->load($id);
			$vendor_id[]=$_product->getVendorId();
		}
				return $vendor_id;
	}	
/* 	Useful for Cart Page */
		public function getRules()
	{
		$cartinfo = Mage::getModel('checkout/cart')->getQuote();
		$final_cost=0;
		$name="";
		$grandtotal=Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
		$cost=0;
		$temp_rules_pro_id=array();
		$ship_fees=array();
		foreach($cartinfo->getAllItems() as $item)
		{
			//calculate cart products weight
			$weight += (ceil($item->getProduct()->getWeight()) * $item->getQty());		
		}
		//Fetch first flexi shipping rules
		$collection = Mage::getModel('emipro_flexishipping/flexiShipping')->getCollection()
					->addFieldToSelect('*')->getFirstItem();
		//Calculate shipping price based on rules
		$str = str_replace(array("{prod.weight}"),array($weight),$collection->getfees());
		$ship_fees = $this->calculate_string($str);
		//Set shipping price
		Mage::getSingleton('core/session')->setShippingCharge($ship_fees);
		$final_cost_name = array("final_cost"=>$ship_fees,"name"=>'',"status"=>1);
		return $final_cost_name;		
	}
	
	public function getFinal_Cost()
	{
		$fees=$this->getRules();
		if(empty($fees["name"]) && $fees["status"]==1)
		{
			$arr=array("fees"=>$fees["final_cost"],"status"=>"1","message"=>"test");
		}
		else
		{
			$config_msg=Mage::getStoreConfig('carriers/flexishipping/specificerrmsg');
			$message=str_replace("{products}",$fees["name"],$config_msg);
			$arr=array("fees"=>$fees["final_cost"],"status"=>"2","message"=>$message);
		}
		return $arr;
	}
	public function check_date_range($start_date,$end_date)
	{
		$current_date=date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
		if(!empty($start_date) && !empty($end_date))
		{
			$start_ts = strtotime($start_date);
			$end_ts = strtotime($end_date);
			$user_ts = strtotime($current_date);
			return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
		}
		if(!empty($start_date))
		{
			$start_ts = strtotime($start_date);
			$user_ts = strtotime($current_date);
			return (($user_ts >= $start_ts));
		}
		if(!empty($end_date))
		{
			$end_ts = strtotime($end_date);
			$user_ts = strtotime($current_date);
			return (($user_ts <= $end_ts));
		}
		if(empty($start_date) && empty($end_date))
		{
			$start_ts = date('Y-m-d', strtotime($current_date .' -1 day'));
			$end_ts = date('Y-m-d', strtotime($current_date .' +1 day'));
			$user_ts = $current_date;
			return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
		}
	}
	public function getCountry($all_country,$specific_countries,$zip)
	{

		$countryId=Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountryId();
		if($all_country==0)
		{ 
			if(strpos($zip,$countryId) !== false)
			{
				return $this->checkzip($zip);
			}	
			else
			{return 1;} 
		}
		else
		{
			
			$country=explode(",",$specific_countries);
			if(in_array($countryId,$country))
			{
				if(strpos($zip,$countryId) !== false)
				{
				return $this->checkzip($zip);
				}	
				return 1;
				
			}
			else
			{return 0;}
		}
	}
	public function checkzip($zip)
	{
		if(!empty($zip)){
		$emipro=new EmiproShippingHelper();
		$postcode =Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getPostcode();
		$country_id=Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountryId();
		$data=$emipro->_addressMatch($zip,$country_id,$postcode);
		return $data;}
		else{return true;}
	}	
	public function getShip_cost($fees,$id)
	{
		$total=0;
		$price=0;
		$cart_price=0;
		$cartinfo=Mage::getSingleton('checkout/cart')->getQuote();
		foreach($cartinfo->getAllItems() as $item)
		{
			if($item->getProductId()==$id )
			{
				$weight += (ceil($item->getProduct()->getWeight()) * $item->getQty());
				/*$qty=$item->getQty();
				$weight=$item->getProduct()->getWeight();
				$pro_price=$item->getPrice();
				if($pro_price!=0)
				{
					$str=str_replace(array("{prod.qty}","{prod.weight}","{prod.price}"),array($qty,ceil($weight),$pro_price),$fees);
					$price=$this->calculate_string($str);
					$total=$total+$price;
				}*/
			}			
		}
		$str = str_replace(array("{prod.weight}"),array($weight),$fees);
		//$price = $this->calculate_string($str)
	return $this->calculate_string($str);	
	}
	public function calculate_string( $mathString ) 
	{
		$mathString = trim($mathString); 
		$mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    
		$compute = create_function("", "return (" . $mathString . ");" );
		return 0 + $compute();
	}
	public function check_fees($fees_string)
	{
		$qty=1;
		$weight=1;
		$pro_price=1;
		$mathString=str_replace(array("{prod.qty}","{prod.weight}","{prod.price}"),array($qty,ceil($weight),$pro_price),$fees_string);
		$mathString = trim($mathString); 
		$mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    
		$compute = create_function("", "return (" . $mathString . ");" );
		if(!empty($compute))
		{return true;}
		else
		{return false;}	
	} 
	
}

