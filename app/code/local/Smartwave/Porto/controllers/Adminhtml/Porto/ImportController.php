<?php 
    class Smartwave_Porto_Adminhtml_Porto_ImportController extends Mage_Adminhtml_Controller_Action{ 
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/porto_settings/"));
        }
        public function blocksAction() {
            $isoverwrite = Mage::helper('porto')->getCfg('install/overwrite_blocks');
            $demo_version = $this->getRequest()->getParam("demo_version");
            Mage::getSingleton('porto/import_cms')->importCms('cms/block', 'blocks', $demo_version, $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/porto_settings/"));
        }
        public function pagesAction() {
            $isoverwrite = Mage::helper('porto')->getCfg('install/overwrite_pages');
            $demo_version = $this->getRequest()->getParam("demo_version");
            Mage::getSingleton('porto/import_cms')->importCms('cms/page', 'pages', $demo_version, $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/porto_settings/"));
        }
    }
?>