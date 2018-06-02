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

class Emipro_Marketplace_Block_Adminhtml_System_Account_Edit_Form extends Mage_Adminhtml_Block_System_Account_Edit_Form {

    protected function _prepareForm() {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $user = Mage::getModel('admin/user')
                ->load($userId);
        $user->unsetData('password');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('adminhtml')->__('Account Information')));

        $fieldset->addField('username', 'text', array(
            'name' => 'username',
            'label' => Mage::helper('adminhtml')->__('User Name'),
            'title' => Mage::helper('adminhtml')->__('User Name'),
            'required' => true,
                )
        );

        $fieldset->addField('firstname', 'text', array(
            'name' => 'firstname',
            'label' => Mage::helper('adminhtml')->__('First Name'),
            'title' => Mage::helper('adminhtml')->__('First Name'),
            'required' => true,
                )
        );

        $fieldset->addField('lastname', 'text', array(
            'name' => 'lastname',
            'label' => Mage::helper('adminhtml')->__('Last Name'),
            'title' => Mage::helper('adminhtml')->__('Last Name'),
            'required' => true,
                )
        );

        $fieldset->addField('user_id', 'hidden', array(
            'name' => 'user_id',
                )
        );

        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $fieldset->addField('email', 'text', array(
                'name' => 'email',
                'label' => Mage::helper('adminhtml')->__('Email'),
                'title' => Mage::helper('adminhtml')->__('User Email'),
                'required' => true,
                    )
            );
        } else {
            $fieldset->addField('emailnote', 'note', array(
                'label' => Mage::helper('adminhtml')->__('Email'),
                'text' => $user->getEmail(),
                    )
            );
            $fieldset->addField('email', 'hidden', array(
                'name' => 'email',
                'label' => Mage::helper('adminhtml')->__('Email'),
                'title' => Mage::helper('adminhtml')->__('User Email'),
                    )
            );
        }
        if (!Mage::helper('marketplace')->getSellerIdfromLoginUser()) {
            $magentoVersion = Mage::getVersion();
            if (version_compare($magentoVersion, '1.9.1.0', '>=')) {
                $fieldset->addField('current_password', 'obscure', array(
                    'name' => 'current_password',
                    'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
                    'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
                    'required' => true,
                        )
                );
            }
        }

        $fieldset->addField('password', 'password', array(
            'name' => 'new_password',
            'label' => Mage::helper('adminhtml')->__('New Password'),
            'title' => Mage::helper('adminhtml')->__('New Password'),
            'class' => 'input-text validate-admin-password',
                )
        );

        $fieldset->addField('confirmation', 'password', array(
            'name' => 'password_confirmation',
            'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
            'class' => 'input-text validate-cpassword',
                )
        );
        $form->setValues($user->getData());
        $form->setAction($this->getUrl('*/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $this->setForm($form);
        //return parent::_prepareForm();
    }

}
