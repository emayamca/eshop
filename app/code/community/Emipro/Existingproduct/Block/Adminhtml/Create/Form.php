<?php
class Emipro_existingproduct_Block_Adminhtml_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('existingproduct_create_form');
    }

    /**
     * Retrieve url for loading blocks
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock');
    }

    /**
     * Retrieve url for form submiting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

}
