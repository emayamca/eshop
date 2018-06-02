<?php
class Pumcp_Payments_Model_Env
{
	public function toOptionArray()
	{
		return array(
				array('value' => 'sandbox','label' => 'sandbox'),
				array('value' => 'production','label' => 'production')
				);
	}
}