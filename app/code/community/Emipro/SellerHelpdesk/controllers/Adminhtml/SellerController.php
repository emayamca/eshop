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

class Emipro_SellerHelpdesk_Adminhtml_SellerController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerhelpdesk');
        $this->_addContent($this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk'));
        $this->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('marketplace/sellerhelpdesk');
    }

    public function gridAction() {
        $this->loadLayout();

        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_sellerHelpdesk_grid')->toHtml()
        );
    }

    public function newAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
        $this->_setActiveMenu('marketplace/sellerhelpdesk');
        $this->_addContent($this->getLayout()->createBlock('emipro_sellerhelpdesk/adminhtml_newticket_edit'));
        $this->renderLayout();
        //$this->_forward('edit');
    }

    public function editAction() {

        $Id = $this->getRequest()->getParam('id');
        $helpdeskModel = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->load($Id);

        if ($helpdeskModel->getId() || $Id == 0) {
            Mage::register('sellerhelpdesk', $helpdeskModel);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('My Support Tickets') . ' - ' . Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore()->getId()));
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
            Mage::getSingleton('adminhtml/session')->addError('Ticket does not exist');
            $this->_redirect('*/*/');
        }
    }

    public function newticketAction() {
        $data = $this->getRequest()->getPost();
        $file_id = "";
        $maxfile_size = 1024 * 1024 * 4; // 4 MB filesize;
        $admin_id = Mage::getModel('admin/session')->getUser()->getUserId();
        $currentDate = Mage::getModel('core/date')->date('Y-m-d H:i');
        $admin_user_id = Mage::getStoreConfig('sellersupport/emipro_group/ticket_admin', Mage::app()->getStore());
        $admin_email = Mage::getModel('admin/user')->load($admin_user_id)->getEmail();
        $user = Mage::getSingleton('admin/session')->getUser();
        $userUsername = $user->getUsername();

        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk');
                $model->setData("admin_user_id", $admin_user_id);
                $model->setData("status_id", 1);
                $model->setData("seller_id", $data["seller_id"]);
                $model->setData("subject", $data["subject"]);
                $model->setData("date", $currentDate);
                $model->save();


                $TicketId = $model->save()->getId();
                if ($TicketId != "") {
                    $con_model = Mage::getModel('emipro_sellerhelpdesk/sellerconversation');
                    $con_model->setData("id", $TicketId);
                    $con_model->setData("message", $data["message"]);
                    $con_model->setData("status_id", 1);
                    $con_model->setData("name", $data["name"]);
                    $con_model->setData("date", $currentDate);
                    $con_model->save();


                    $con_id = $con_model->save()->getId();
                }

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
                }
                $ticket_template = Mage::getStoreConfig('sellersupport/emipro_template/ticket_create', Mage::app()->getStore());
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault($ticket_template);
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $emailTemplateVariables = array();
                $emailTemplateVariables['seller_name'] = "";
                $emailTemplateVariables['message'] = $con_model->getMessage();
                $emailTemplateVariables['ticket_id'] = $TicketId;
                $emailTemplateVariables['seller_name'] = $userUsername;
                $emailTemplateVariables['sender_name'] = Mage::getStoreConfig('trans_email/ident_general/name');
                $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
                $emailTemplate->setSenderEmail($sender_email);
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject("Support ticket has been created with Ticket Id " . $TicketId);

                if ($file_id != "") {
                    $file_info = Mage::getModel('emipro_sellerhelpdesk/sellerattachment')->load($file_id);
                    $file_name = $file_info->getFile();
                    $file_path = Mage::getBaseDir('media') . DS . 'sellerhelpdesk' . DS . 'attachment' . DS . $file_name;

                    $attachment = file_get_contents($file_path);
                    $emailTemplate->getMail()->createAttachment($attachment, Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($_FILES['file']['name']));
                }

                $emailTemplate->send($admin_email, $admin_email, $emailTemplateVariables);


                // Ticket created successfully email for seller 
                $template = Mage::getStoreConfig('sellersupport/emipro_template/new_sellerticket_create', Mage::app()->getStore());
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template);
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $emailTemplateVariables = array();
                $emailTemplateVariables['seller_name'] = $userUsername;
                $emailTemplateVariables['message'] = $model->getMessage();
                $emailTemplateVariables['ticket_id'] = $TicketId;
                $emailTemplateVariables['sender_name'] = Mage::getStoreConfig('trans_email/ident_general/name');
                $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
                $emailTemplate->setSenderEmail($sender_email);
                $emailTemplate->setType('html');
                $emailTemplate->setTemplateSubject("Support ticket has been created with Ticket Id " . $TicketId);

                $emailTemplate->send($user->getEmail(), $user->getEmail(), $emailTemplateVariables);
                $this->_redirect('*/*/');
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('emipro_sellerhelpdesk')->__('Your support ticket has been created successfully with Ticket Id  ' . $TicketId));
            } catch (Exception $e) {
                echo $e->getMessage();
                Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Unable to submit your request. Please, try again later.'));
            }
        }
    }

    public function saveAction() {

        $data = Mage::app()->getRequest()->getPost();

        if ($data["message"] == "") {
            Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Please enter valid message.'));
            $this->_redirect('*/*/edit', array("id" => $data["id"]));
            return;
        }
        $file_id = "";
        $status = "";
        $maxfile_size = 1024 * 1024 * 4; // 4 MB filesize;
        $data = Mage::app()->getRequest()->getPost();
        $help_desk = Mage::getModel('emipro_sellerhelpdesk/sellerhelpdesk')->load($data["id"], "id");
        $admin_user_id = $help_desk->getAdminUserId();
        $admin_email = Mage::getModel('admin/user')->load($admin_user_id)->getEmail();
        $user = Mage::getSingleton('admin/session');
        $userUsername = $user->getUser()->getUsername();

        try {
            if ($_FILES['file']['size'] > $maxfile_size) {
                Mage::getSingleton('core/session')->setTicketmessage($data["message"]);
                Mage::getSingleton('core/session')->addError(Mage::helper('emipro_sellerhelpdesk')->__('Unable to submit your request. Please, try again later.'));
                $this->_redirect('*/*/edit', array("id" => $data["id"]));
                return;
            }
            if ($data["status"] == 2) {
                $current_status = 2;
            } else {
                $current_status = 4;
            }
            $status = array("status_id" => $current_status);
            $help_desk->addData($status);
            $help_desk->setId($data["id"])->save();

            $model = Mage::getModel('emipro_sellerhelpdesk/sellerconversation');
            $model->setData("id", $data["id"]);
            $model->setData("message", $data["message"]);
            $model->setData("status_id", $current_status);
            $model->setData("name", $data["name"]);
            $model->setData("date", $data["date"]);
            $model->save();
            $conversation_id = $model->save()->getId();


            if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
                $fileName = $_FILES['file']['name'];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $new_fileName = md5(uniqid(rand(), true)) . "." . $ext;
                $uploader = new Varien_File_Uploader('file');
                $path = Mage::getBaseDir('media') . DS . 'sellerhelpdesk' . DS . 'attachment';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $uploader->save($path . DS, $new_fileName);
                $file = Mage::getModel('emipro_sellerhelpdesk/sellerattachment');
                $file->setData("conversation_id", $conversation_id);
                $file->setData("file", $new_fileName);
                $file->setData("current_file_name", $fileName);
                $file->save();
                $file_id = $file->save()->getId();
                //  Mage::getSingleton('customer/session')->unsTicketmessage();
            }
            $current_ticket_status = Mage::helper('emipro_sellerhelpdesk')->getStatus($model->getStatusId());
            $ticket_template = Mage::getStoreConfig('sellersupport/emipro_template/ticket_conversation', Mage::app()->getStore());
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault($ticket_template);
            $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
            $emailTemplateVariables = array();

            $emailTemplateVariables['message'] = $model->getMessage();
            $emailTemplateVariables['ticket_id'] = $help_desk->getId();
            $emailTemplateVariables['seller_name'] = $userUsername;
            $emailTemplateVariables['ticket_status'] = $current_ticket_status;

            $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_general/name'));
            $emailTemplate->setSenderEmail($sender_email);
            $emailTemplate->setType('html');
            $emailTemplate->setTemplateSubject("Reply for support ticket " . $help_desk->getId());

            if ($file_id != "") {
                $file_info = Mage::getModel('emipro_sellerhelpdesk/sellerattachment')->load($file_id);
                $file_name = $file_info->getFile();
                $file_path = Mage::getBaseDir('media') . DS . 'sellerhelpdesk' . DS . 'attachment' . DS . $file_name;

                $attachment = file_get_contents($file_path);
                $emailTemplate->getMail()->createAttachment($attachment, Zend_Mime::TYPE_OCTETSTREAM, Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($_FILES['file']['name']));
            }

            $emailTemplate->send($admin_email, $admin_email, $emailTemplateVariables);
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

}
