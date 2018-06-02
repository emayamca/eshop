<?php

class Wyomind_Elasticsearch_Model_Attribute_Source_Weight extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        if (is_null($this->_options)) {
            for ($i = 1; $i <= 10; $i++) {
                $this->_options[] = array('label' => $i , "value" => $i);
            }
        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
