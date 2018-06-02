<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Marketplace 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

class Emipro_SellerHelpdesk_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerhelpdesk');
        $this->_addContent($this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk'));
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();

        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerhelpdesk');
        $this->_addContent($this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_newticket_edit'));
        $this->renderLayout();
    }

    public function editAction() {

        $Id = $this->getRequest()->getParam('id');
        $helpdeskModel = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->load($Id);

        if ($helpdeskModel->getId() || $Id == 0) {
            Mage::register('sellerhelpdesk', $helpdeskModel);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Seller Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
            $this->_setActiveMenu('marketplace/sellerhelpdesk');
            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()
                            ->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk_edit'))
                    ->_addLeft($this->getLayout()
                            ->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk_edit_tabs')
            );
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Ticket does not exist.');
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {
        $file_id = "";
        $status = "";
        $maxfile_size = 1024 * 1024 * 4; // 4 MB filesize;

        if ($this->getRequest()->getPost()) {
            try {
                $data = $this->getRequest()->getPost();
                if ($data["message"] == "") {
                    Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Please enter valid message.'));
                    $this->_redirect('*/*/edit', array("id" => $data["id"]));
                    return;
                }
                $helpDesk = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->load($data["id"], "id");
                $seller_info = Mage::getModel('marketplace/seller')->load($helpDesk->getsellerId(), "seller_id");
                if ($_FILES['file']['size'] > $maxfile_size) {
                    Mage::getSingleton('core/session')->setTicketmessage($data["message"]);
                    Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Ticket attachment was not uploaded. Please,try again later.'));
                    $this->_redirect('*/*/edit', array("id" => $data["id"]));
                    return;
                }

                if ($data["status"] == 2) {
                    $current_status = 2;
                } else {
                    $current_status = 5;
                }

                $status = array("status_id" => $current_status);
                $helpDesk->addData($status);
                $helpDesk->setId($data["id"])->save();


                $model = Mage::getModel('emipro_sellerhelpdesk/sellerconversation');
                $model->setData($data);
                $model->setData("status_id", $current_status);

                $con_id = $model->save()->getId();
                $current_ticket_status = Mage::helper('emipro_sellerhelpdesk')->getStatus($model->getStatusId());
                if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
                    $fileName = $_FILES['file']['name'];
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $new_fileName = md5(uniqid(rand(), true)) . "." . $ext;
                    $uploader = new Varien_File_Uploader('file');
                    $path = Mage::getBaseDir('media') . DS . 'sellerhelpdesk' . DS . 'attachment';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $file = Mage::getModel('emipro_sellerhelpdesk/sellerattachment');
                    $file->setData("conversation_id", $con_id);
                    $file->setData("file", $new_fileName);
                    $file->setData("current_file_name", $fileName);
                    $file->save();
                    $file_id = $file->save()->getId();
                    $uploader->save($path . DS, $new_fileName);
                    Mage::getSingleton('core/session')->unsTicketmessage();
                }
                // Ticket conversation email for seller.

                $seller_email = $seller_info->getEmail();
                $seller_name = $seller_info->getFirstname();
                $ticket_template = Mage::getStoreConfig('sellersupport/emipro_template/ticket_conversation', Mage::app()->getStore());
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault($ticket_template);

                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $emailTemplateVariables = array();
                $emailTemplateVariables['seller'] = $seller_name;
                $emailTemplateVariables['message'] = $model->getMessage();
                $emailTemplateVariables['ticket_id'] = $helpDesk->getId();
                $emailTemplateVariables['ticket_status'] = $current_ticket_status;
                $emailTemplateVariables['seller_name'] = Mage::getStoreConfig('trans_email/ident_general/name');
                $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
                $emailTemplate->setSenderEmail($sender_email);
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject("Reply for support ticket " . $helpDesk->getId());

                if ($file_id != "") {
                    $file_info = Mage::getModel('emipro_sellerhelpdesk/sellerattachment')->load($file_id);
                    $file_name = $file_info->getFile();
                    $file_path = Mage::getBaseDir('media') . DS . 'sellerhelpdesk' . DS . 'attachment' . DS . $file_name;

                    $attachment = file_get_contents($file_path);
                    $emailTemplate->getMail()->createAttachment($attachment, Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($_FILES['file']['name']));
                }
                $emailTemplate->send($seller_email, $seller_email, $emailTemplateVariables);
                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect('*/*/edit', array("id" => $data["id"]));
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('emipro_sellerhelpdesk')->__('Ticket has been updated successfully.'));
                    return;
                }
                $this->_redirect('*/*/');
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('emipro_sellerhelpdesk')->__('Ticket information was successfully saved.'));
            } catch (Exception $e) {
                echo $e->getMessage();
                Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Unable to submit your request. Please, try again later.'));
            }
        }
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk');
                $model->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('emipro_sellerhelpdesk')->__('The rule has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                        Mage::helper('emipro_sellerhelpdesk')->__('An error occurred while deleting the rule. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('emipro_sellerhelpdesk')->__('Unable to find a rule to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $entity_ids = $this->getRequest()->getParam('id');
        if (!is_array($entity_ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select ticket.'));
        } else {
            try {

                foreach ($entity_ids as $entity_id) {
                    $ticketModel = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->setId($entity_id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($entity_ids))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/adminsupport');
    }

}
