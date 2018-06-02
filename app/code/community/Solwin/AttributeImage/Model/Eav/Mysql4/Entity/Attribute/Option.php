<?php

class Solwin_AttributeImage_Model_Eav_Mysql4_Entity_Attribute_Option extends Mage_Eav_Model_Mysql4_Entity_Attribute_Option
{
    public function getAttributeImages()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'image'));

        return $this->getReadConnection()->fetchPairs($select);
    }
    
    public function getAttributeThumbs()
    {
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getTable('eav/attribute_option'), array('option_id', 'thumb'));

        return $this->getReadConnection()->fetchPairs($select);
    }
}