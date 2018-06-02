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
class EmiproShippingHelper
{
	public function _addressMatch($address_filter,$country_id,$postcode)
	{	
		
		$address_filter=$this->_trim($address_filter);
		$condition_array=$this->getcondition_array($address_filter);
		foreach($condition_array as $value)
		{
			$country_code=current(explode("(", $value, 2));
			if(strpos($country_code,"-")!==false)
			{
				$res=$this->checkrestriction($value,$country_id,$postcode);
				if($res==false)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			if(substr_count($value,"(") == 0 && substr_count($value,")") == 0)
			{
				
				$country_condition=$this->checkcountry($value,$country_id);
				if($country_condition==true)
				{
					return true;
				}
			}
			if(substr_count($value,"(*)") > 0 )
			{
				$country_condition=$this->checkcountry($value,$country_id);
				if($country_condition==true)
				{
					return true;
				}
			}
			
			if(substr_count($value,"(-)") > 0 )
			{
				$country_condition=$this->checkcountry($value,$country_id);
				if($country_condition==false)
				{
					return false;
				}
			}
			
			if(substr_count($value,"*") <= 0 && substr_count($value,"-")<=0)
			{
				$val=$this->checkseparatezip($value,$country_id,$postcode);
				if($val==true)
				{
					return true;
				}
			}
			
			if(substr_count($value,"*")>0)
			{
				$val_allzip=$this->checkallzip($value,$country_id,$postcode);
				if($val_allzip==true)
				{
					return true;
				}
			}
			
			if(substr_count($value,"-")>0)
			{
				$val_range= $this->checkziprange($value,$country_id,$postcode);
				if($val_range==true)
				{
					return true;
				}
			}
			
		}
		return false;
	}
	
	public function checkcountry($condition,$country_id)
	{
		if(strpos($condition,$country_id)!==false)
		{
			return true;
		}
	}
	
	public function checkziprange($condition,$country_id,$postcode)
	{
		$zip_rang=substr($condition,2);
		$sub_str=str_replace("("," ",$zip_rang);
		$sub_str1=str_replace(")"," ",$sub_str);
		$first_value = $this->_trim(current(explode("-", $sub_str1, 2)));
		$last_value = $this->_trim(substr($sub_str1, strpos($sub_str1, "-") + 1));  
		if(strpos($condition,$country_id)!==false)
			{
				if($postcode>=$first_value && $postcode<=$last_value)
				{
					return true;
				}
			}
	}
	
	public function checkallzip($condition,$country_id,$postcode)
	{
		$zipcode=substr($postcode,0,2);
		
		if(strpos($condition,$country_id)!==false)
			{
				if(strpos($condition,$zipcode)!==false)
				{
					return true;
				}
			}
	}
	
	public function checkseparatezip($condition,$country_id,$postcode)
	{
		if(strpos($condition,$country_id)!==false)
			{
				if(preg_match('~\\b' . $postcode . '\\b~i', $condition))
				{	
					
					return true;
				}
			}
	}
	
	public function getcondition_array($str)
	{
		$data=explode("),",$str);
		$size=count($data);
		for($i=0;$i<$size;$i++)
		{
			if($i!=($size-1))
			{
				$newdata[$i]=$data[$i].")";
			}
			else
			{
				$newdata[$i]=$data[$i];
			}
		}

		return $newdata;
		
	}
	public function _trim($value)
	{
			return trim($value);
	}
	public function checkrestriction($value,$country_id,$postcode)
	{
		
		$value=str_replace("-(","(",$value);
		if(substr_count($value,"*") == 0 && substr_count($value,"-")==0)
			{
				
				$val=$this->restrictseparatezip($value,$country_id,$postcode);
				if($val==false)
				{
					return false;
				}
				
			}
			if(substr_count($value,"*")>0)
			{
				$val_allzip=$this->restrictallzip($value,$country_id,$postcode);
				if($val_allzip==false)
				{
					return false;
				}
			}
			if(substr_count($value,"-")>0)
			{
				$val_range= $this->restrictziprange($value,$country_id,$postcode);
				if($val_range==false)
				{
					return false;
				}
			}
			return true;
	}
	
	public function restrictziprange($condition,$country_id,$postcode)
	{
		$zip_rang=substr($condition,2);
		$sub_str=str_replace("("," ",$zip_rang);
		$sub_str1=str_replace(")"," ",$sub_str);
		$first_value = $this->_trim(current(explode("-", $sub_str1, 2)));
		$last_value = $this->_trim(substr($sub_str1, strpos($sub_str1, "-") + 1));  
		if(strpos($condition,$country_id)!==false)
			{
						
				if($postcode>=$first_value && $postcode<=$last_value)
				{
					return false;
				}
			}
		return true;
	}
	
	public function restrictallzip($condition,$country_id,$postcode)
	{
		$zipcode=substr($postcode,0,2);
		if(strpos($condition,$country_id)!==false)
			{
				if(strpos($condition,$zipcode)!==false)
				{
					return false;
				}
			}
			return true;
	}
	
	public function restrictseparatezip($condition,$country_id,$postcode)
	{
		if(strpos($condition,$country_id)!==false)
			{
				if(preg_match('~\\b' . $postcode . '\\b~i', $condition))
				{	
					return false;
				}
			}
			return true;
	}
}

