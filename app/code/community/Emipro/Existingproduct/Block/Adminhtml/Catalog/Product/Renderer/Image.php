<?php
class Emipro_Existingproduct_Block_Adminhtml_Catalog_Product_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $_product = Mage::getModel('catalog/product')->load($row->getEntityId());
        if($_product->getImage() != 'no_selection'){
              $image = "<img src='".Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(97)."' title='".$_product->getName()."' style='width:97px'/>";
        }else{
            $image = "<img src='".Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/thumbnail.jpg',array('_area'=>'frontend'))."' title='".$_product->getName()."' style='width:97px'/>";
        }
        return $image;
    }
}