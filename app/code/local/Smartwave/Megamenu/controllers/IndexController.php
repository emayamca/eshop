<?php
class Smartwave_Megamenu_IndexController extends Mage_Core_Controller_Front_Action
{
	public function showpopupAction()
	{
		if($this->getRequest()->isXmlHttpRequest()){ //Check if it was an AJAX request
			$response = array();
            $category_id = $this->getRequest()->getParam("category_id");
			$html = array();
            $level = 0;
            $catModel = Mage::getModel('catalog/category')->load($category_id);
            $_menuHelper = Mage::helper('megamenu');
            $block = $_menuHelper->getMegamenuBlock();
            $blockType = $block->_getBlocks($catModel, 'sw_cat_block_type');
            if (!$blockType || $blockType == 'default')
                $blockType = $_menuHelper->getConfig('general/wide_style');    //Default Format is wide style.
            $activeChildren = $_menuHelper->getActiveChildren($catModel, $level);
            
            $block_top = $block_left = $block_right = $block_bottom = false;
            if ($blockType == 'wide' || $blockType == 'staticwidth') {
                // ---Get Static Blocks for category, only format is wide style, it is enable.
                if ($level == 0) {
                //  block top of category
                    $block_top = $block->_getBlocks($catModel, 'sw_cat_block_top');
                //  block left of category
                    $block_left = $block->_getBlocks($catModel, 'sw_cat_block_left');
                //  block left width of category
                    $block_left_width = (int)$block->_getBlocks($catModel, 'sw_cat_left_block_width');
                    if (!$block_left_width)
                        $block_left_width = 3;
                //  block right of category
                    $block_right = $block->_getBlocks($catModel, 'sw_cat_block_right');
                //  block left width of category
                    $block_right_width = (int)$block->_getBlocks($catModel, 'sw_cat_right_block_width');
                    if (!$block_right_width)
                        $block_right_width = 3;
                //  block bottom of category
                    $block_bottom = $block->_getBlocks($catModel, 'sw_cat_block_bottom');
                }
            }
            
            if ($level == 0 && ($blockType == 'wide' || $blockType == 'staticwidth') ) {
                if ($block_top)
                    $html[] = '<div class="top-mega-block">' . $block_top . '</div>';
                $html[] = '<div class="mega-columns row '.count(Mage::getModel('catalog/category')->getCategories($catModel->getId())).'">';
                if ($block_left)
                    $html[] = '<div class="left-mega-block col-sm-'.$block_left_width.'">' . $block_left . '</div>';
                if (count($activeChildren)) {
                    //columns for category
                    $columns = (int)$catModel->getData('sw_cat_block_columns');
                    if (!$columns)
                        $columns = 6;
                    
                    //columns item width    
                    $columnsWidth = 12;
                    if ($block_left)
                        $columnsWidth = $columnsWidth - $block_left_width;
                    if ($block_right)
                        $columnsWidth = $columnsWidth - $block_right_width;
                        
                    //draw category menu items
                    $html[] = '<div class="block1 col-sm-'.$columnsWidth.'">';
                    $html[] = '<div class="row">';                    
                    $html[] = '<ul>';
                    $html[] = $block->drawColumns($activeChildren, $columns, count($activeChildren),'', 'wide');
                    $html[] = '</ul>';
                    $html[] = '</div>';
                    $html[] = '</div>';
                }
                if ($block_right)
                    $html[] = '<div class="right-mega-block col-sm-'.$block_right_width.'">' . $block_right . '</div>';
                $html[] = '</div>';
                /* Fixed from version 1.0.1 */
                //verion 1.0.1 start
                if ($block_bottom)
                    $html[] = '<div class="bottom-mega-block">' . $block_bottom . '</div>';
                //version 1.0.1 end
            } else if ($level == 0 && $blockType == 'narrow') {                
                $html[] = '<ul>';
                $html[] = $block->drawColumns($activeChildren, '', count($activeChildren),'','narrow', $mode);
                $html[] = '</ul>';
            }
            $html = implode("\n", $html);
            
            $response['popup_content'] = $html;
			$response['status'] = 'SUCCESS';
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            
			return;
		} else {
			$this->_forward('noRoute');
		}
	}
    public function showmobilemenuAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){ //Check if it was an AJAX request
            $response = array();
            $_menuHelper = Mage::helper('megamenu');
            
            $response['popup_content'] = $_menuHelper->getMobileMenuContent();
            $response['status'] = 'SUCCESS';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            
            return;
        } else {
            $this->_forward('noRoute');
        }
    }
    public function onepagecategoryAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){ //Check if it was an AJAX request
            $response = array();
            $_menuHelper = Mage::helper('megamenu');
            
            $cat_id = $this->getRequest()->getParam("category_id");
            $product_count = $this->getRequest()->getParam("product_count");
            $aspect_ratio = $this->getRequest()->getParam("aspect_ratio");
            $ratio_width = $this->getRequest()->getParam("image_width");
            $ratio_height = $this->getRequest()->getParam("image_height");
            $lazy_owl = $this->getRequest()->getParam("lazy_owl");
            $columns = $this->getRequest()->getParam("columns");

            $cat_model = Mage::getModel('catalog/category')->load($cat_id);
            $activeChildren = $_menuHelper->getActiveChildren($cat_model);
            
            $a_class = '';
            $popup = '';
            if (count($activeChildren)) {
                $a_class = ' class="parent"';
                $popup = '<div class="menu-popup"><div style="padding: 30px 0; text-align: center;"><i class="ajax-loader small animate-spin"></i></div></div>';
            }
            
            $title_menu = '<a href="javascript:void(0)" data-id="'.$cat_id.'"'.$a_class.'><span>'.$cat_model->getName().'</span></a>'.$popup;
            
            $products = $this->getLayout()->createBlock("core/template")->setTemplate("smartwave/megamenu/onepagecategory/products_area.phtml")->setData("category_id",$cat_id)->setData("product_count",$product_count)->setData("aspect_ratio",$aspect_ratio)->setData("image_width",$ratio_width)->setData("image_height",$ratio_height)->setData("lazy_owl",$lazy_owl)->setData("columns",$columns)->toHtml();
            
            $_menuHelper = Mage::helper('megamenu');
            $block = $_menuHelper->getMegamenuBlock();
            $additional_content = $block->_getBlocks($cat_model, 'sw_ocat_additional_content');
            
            $response['title_menu'] = $title_menu;
            $response['category_products'] = $products;
            $response['addtional_content'] = $additional_content;
            $response['category_id'] = $cat_id;
            $response['status'] = 'SUCCESS';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            
            return;
        } else {
            $this->_forward('noRoute');
        }
    }
    public function showtitlemenupopupAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){ //Check if it was an AJAX request
            $response = array();
            $category_id = $this->getRequest()->getParam("category_id");
            $html = array();
            $level = 0;
            $catModel = Mage::getModel('catalog/category')->load($category_id);
            $_menuHelper = Mage::helper('megamenu');
            $block = $_menuHelper->getMegamenuBlock();
            $activeChildren = $_menuHelper->getActiveChildren($catModel, $level);
            
            if (count($activeChildren)) {
                //columns for category
                $columns = 5;
                $html[] = '<ul class="columns'.$columns.'">';
                $html[] = $block->drawColumns($activeChildren, $columns, count($activeChildren),'','narrow','mb');
                $html[] = '</ul>';
            }

            $html = implode("\n", $html);
            
            $response['popup_content'] = $html;
            $response['status'] = 'SUCCESS';
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            
            return;
        } else {
            $this->_forward('noRoute');
        }
    }
}
