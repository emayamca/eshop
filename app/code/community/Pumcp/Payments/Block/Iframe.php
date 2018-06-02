<?php

class Pumcp_Payments_Block_Iframe extends Mage_Core_Block_Template
{
    protected $_params = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pumcp/iframe.phtml');
    }

    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }
}
