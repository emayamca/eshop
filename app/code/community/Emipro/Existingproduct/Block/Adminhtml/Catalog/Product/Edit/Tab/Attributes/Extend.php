<?php
class Emipro_Existingproduct_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    const DYNAMIC = 0;
    const FIXED = 1;
    public function __construct()
    {
        parent::__construct();
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }
    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();
        $skuprefix = Mage::helper('existingproduct')->getSkuPrefix($this->getProduct());
         

        if (!($this->getAttribute()->getAttributeCode() == 'price' && $this->getCanReadPrice() === false)) 
        {
            //$skuprefix = Mage::helper('existingproduct')->getSkuPrefix($this->getProduct()); 
            //$skuName = substr($this->getProduct()->getSku(),0,3);
            
            if($this->getAttribute()->getAttributeCode() == 'sku'){
                if(isset($skuprefix))
                {
                    $html = "<label >".$skuprefix."</label>";
                    $skuCheck = $skuprefix;
                }
                else
                {
                    $html = "";
                }
                if(isset($skuprefix))
                {
                    $length = strlen($skuprefix);
                    $skuName = substr($this->getProduct()->getSku(),0,$length);
                    
                    if($skuCheck == $skuName)
                    {
                        $sku = substr($this->getProduct()->getSku(),$length);
                    }
                    else
                    {
                        $sku = $this->getProduct()->getSku();                    
                    }
                }
                else
                {
                    $sku = $this->getProduct()->getSku(); 
                }

                $html .= '<span class="next-toselect">'.'<input id="sku" name="product[sku]" value="'.$sku.'" class=" required-entry input-text required-entry" type="text">' . '</span>';
            }else{
                $html .= '<span class="next-toselect">'.$elementHtml. '</span>';
            }
            //$html .= '<span class="next-toselect">test'.$elementHtml. '</span>';
         }

        if ($this->getDisableChild() && !$this->getElement()->getReadonly()) {
            $html .= "<script type=\"text/javascript\">
                function " . $switchAttributeCode . "_change() {
                    if ($('" . $switchAttributeCode . "').value == '" . self::DYNAMIC . "') {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {
                            $('" . $this->getAttribute()->getAttributeCode() . "').disabled = true;
                            $('" . $this->getAttribute()->getAttributeCode() . "').value = '';
                            $('" . $this->getAttribute()->getAttributeCode() . "').removeClassName('required-entry');
                        }

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').show();
                        }
                    } else {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {";

            if ($this->getAttribute()->getAttributeCode() == 'price'
                && $this->getCanEditPrice() === false
                && $this->getCanReadPrice() === true
                && $this->getProduct()->isObjectNew()
            ) {
                $defaultProductPrice = ($this->getDefaultProductPrice()) ? $this->getDefaultProductPrice() : "''";
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').value = " . $defaultProductPrice . ";";
            } else {
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').disabled = false;
                          $('" . $this->getAttribute()->getAttributeCode() . "').addClassName('required-entry');";
            }

            $html .= "}

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').hide();
                        }
                    }
                }";

            if (!($this->getAttribute()->getAttributeCode() == 'price'
                && !$this->getCanEditPrice()
                && !$this->getProduct()->isObjectNew())
            ) {
                $html .= "$('" . $switchAttributeCode . "').observe('change', " . $switchAttributeCode . "_change);";
            }
            $html .= $switchAttributeCode . "_change();
            </script>";
        }
        return $html;
    }

    public function getProduct()
    {
        if (!$this->getData('product')){
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
}
