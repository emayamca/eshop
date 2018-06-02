<?php
/**
 * Business Fuel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Fuel
 * @package     Fuel_Bulkorderform
 */
/**
 * Bulkorder adminhtml grid
 */
class Fuel_Bulkorderform_Block_Adminhtml_Bulkorderform_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('bulkorderformGrid');
      $this->setDefaultSort('bulkorderform_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  /*
   * Bulkorderform collection for grid
   *
   * @return array
   */
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('bulkorderform/bulkorderform')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  /*
   * Bulkorderform form columns
   *
   * @return array
   */
  protected function _prepareColumns()
  {
	$this->addColumn('bulkorderform_id', array(
          'header'    => Mage::helper('bulkorderform')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'bulkorderform_id',
      ));

	$this->addColumn('fullname', array(
          'header'    => Mage::helper('bulkorderform')->__('Customer Name'),
          'align'     =>'left',
          'index'     => 'fullname',
      ));
		
	$this->addColumn('emailaddress', array(
          'header'    => Mage::helper('bulkorderform')->__('Email Address'),
          'align'     =>'left',
          'index'     => 'emailaddress',
      ));	
	  
	$this->addColumn('mobilenumber', array(
          'header'    => Mage::helper('bulkorderform')->__('Mobile'),
          'align'     =>'left',
          'index'     => 'mobilenumber',
      ));
	  
	$this->addColumn('productname', array(
          'header'    => Mage::helper('bulkorderform')->__('Product Name'),
          'align'     =>'left',
          'index'     => 'productname',
      )); 
	  
	$this->addColumn('quantity', array(
          'header'    => Mage::helper('bulkorderform')->__('Qty'),
          'align'     =>'left',
          'index'     => 'quantity',
      ));  
	  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('bulkorderform')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */
	  $this->addColumn('comments', array(
			'header'    => Mage::helper('bulkorderform')->__('comments'),
			'width'     => '150px',
			'index'     => 'comments',
      ));
	  $this->addColumn('form', array(
          'header'    => Mage::helper('bulkorderform')->__('Form'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'form',
          'type'      => 'options',
          'options'   => array(
              0 => 'Bulkorder',
              1 => 'Enquiryform',
          ),
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('bulkorderform')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Acitvate',
              2 => 'Deacitvate',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('bulkorderform')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('bulkorderform')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('bulkorderform')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('bulkorderform')->__('XML'));
	  
      return parent::_prepareColumns();
  }
  /*
   * Bulkorderform mass action 
   * Delete and change status
   *
   * @return boolean
   */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('bulkorderform_id');
        $this->getMassactionBlock()->setFormFieldName('bulkorderform');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('bulkorderform')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('bulkorderform')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('bulkorderform/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('bulkorderform')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('bulkorderform')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

	/*
	 * URL for bulkorderform grid
	 *
	 * @return link
	 */
	public function getRowUrl($row)
	{
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}