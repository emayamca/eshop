<?php

class Wyomind_Elasticsearch_Adminhtml_ElasticsearchController extends Mage_Adminhtml_Controller_Action {

    public function checkserversAction() {
        
        $warnings = array();
        $hosts = explode(',',$this->getRequest()->getParam('servers'));
        foreach ($hosts as $host) {


            try {
                $ch = curl_init($host); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch);
                curl_close($ch);
                if ($data != false) {
                $warnings[] = array('host' => $host, 'data' => json_decode($data));
                } else {
                    $warnings[] = array('host' => $host, 'data' => json_encode($data));
                }
            } catch (\Exception $e) {
                $warnings[] = array('host' => $host, 'data' => false, 'error' => $e->getMessage());
            }
        }
        
        $this->loadLayout();
        
        $this->getLayout()
                ->getBlock('root')
                ->setData('warnings',json_encode($warnings))
                ->setTemplate('elasticsearch/checkservers.phtml');
        $this->renderLayout();
        return $this;
    }
    



    public function isAllowed() {
        return true;
    }

}
