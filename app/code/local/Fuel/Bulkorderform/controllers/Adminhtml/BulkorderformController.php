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
 * Bulkorder adminhtml controller
 */
class Fuel_Bulkorderform_Adminhtml_BulkorderformController extends Mage_Adminhtml_Controller_action
{
	/*
	 * Initialiation
	 */
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('bulkorderform/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Bulkorder Manager'), Mage::helper('adminhtml')->__('Bulkorder Manager'));
		
		return $this;
	}   
 	/*
	 * Adminhtml index method
	 */
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	/*
	 * Edit method
	 */
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bulkorderform/bulkorderform')->load($id);

		//checking bulkorderform_id is not empty or equal to 0
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('bulkorderform_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('bulkorderform/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Bulkorder Manager'), Mage::helper('adminhtml')->__('Bulkorder Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Bulkorder News'), Mage::helper('adminhtml')->__('Bulkorder News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('bulkorderform/adminhtml_bulkorderform_edit'))
				->_addLeft($this->getLayout()->createBlock('bulkorderform/adminhtml_bulkorderform_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bulkorderform')->__('Bulkorder does not exist'));
			$this->_redirect('*/*/');
		}
	}
 	/*
	 * Add new Bulkorder method
	 */
	public function newAction() {
		$this->_forward('edit');
	}
 	/*
	 * Save Bulkorder order add new / edit
	 *
	 * @return string
	 */
	public function saveAction() {
		//Checking bulkorderform data post is not empty
		if ($data = $this->getRequest()->getPost()) {	
			//Save form post values
			$model = Mage::getModel('bulkorderform/bulkorderform');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				// Add / Update created and updated time
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				// Save into database successfully
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bulkorderform')->__('Bulkorder was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				// Based on save / save and continue redirection
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bulkorderform')->__('Unable to find Bulkorder request to save'));
        $this->_redirect('*/*/');
	}
  	/*
	 * Delete Bulkorder method
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bulkorderform/bulkorderform');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Bulkorder was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
  	/*
	 * Mass delete Bulkorder method
	 */
    public function massDeleteAction() {
        $bulkorderformIds = $this->getRequest()->getParam('bulkorderform');
		//Checking bulkorderform_id is not empty
        if(!is_array($bulkorderformIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Bulkorder(s)'));
        } else {
            try {
                foreach ($bulkorderformIds as $bulkorderformId) {
                    $bulkorderform = Mage::getModel('bulkorderform/bulkorderform')->load($bulkorderformId);
                    $bulkorderform->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bulkorderformIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	/*
	 * Mass Bulkorder status change method
	 */
    public function massStatusAction()
    {
        $bulkorderformIds = $this->getRequest()->getParam('bulkorderform');
        if(!is_array($bulkorderformIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Bulkorder(s)'));
        } else {
            try {
                foreach ($bulkorderformIds as $bulkorderformId) {
                    $bulkorderform = Mage::getSingleton('bulkorderform/bulkorderform')
                        ->load($bulkorderformId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bulkorderformIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  	/*
	 * Export Bulkorder data into csv file
	 */
    public function exportCsvAction()
    {
        $fileName   = 'bulkorderform.csv';
        $content    = $this->getLayout()->createBlock('bulkorderform/adminhtml_bulkorderform_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
  	/*
	 * Export Bulkorder data into xml file
	 */
    public function exportXmlAction()
    {
        $fileName   = 'bulkorderform.xml';
        $content    = $this->getLayout()->createBlock('bulkorderform/adminhtml_bulkorderform_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
  	/*
	 * Define sendupload method
	 */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}