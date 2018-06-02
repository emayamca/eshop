<?php

/*
 * ////////////////////////////////////////////////////////////////////////////////////// 
 * 
 * @author   Emipro Technologies 
 * @Category Emipro 
 * @package  Emipro_Stocknotification 
 * @license http://shop.emiprotechnologies.com/license-agreement/   
 * 
 * ////////////////////////////////////////////////////////////////////////////////////// 
 */

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `low_stock_enable` TINYINT(1)  DEFAULT 0");
$installer->run("ALTER TABLE  `" . $this->getTable('seller_detail') . "` ADD  `low_stock_quantity` SMALLINT(6)  DEFAULT 5");
$installer->endSetup();

$template = Mage::getModel('adminhtml/email_template');
$template->setTemplateSubject("Low Stock Notification")
            ->setTemplateCode('low_stock_notification')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <p style=" font-weight:normal; line-height:22px; margin:0 0 11px 0;">Dear   {{var sellername}}</p>
                        </td>
                    </tr>
 
                    <tr>
                        <td class="email-heading">
                            <p>There are some products in low stock,<a href="{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/">logging into your account</a>.</p>
                        	<p> Please check following list</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{var product_html}}   
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Thank you, <strong>{{var store.getFrontendName()}}</strong></p><br/>
                        </td>
                    </tr>
                             <tr>
                <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you, <strong>{{var store.getFrontendName()}}</strong></p></center></td>
                         </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>')
->setTemplateStyles('')
->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
->setAddedAt(Mage::getSingleton('core/date')->gmtDate())
->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
->setOrigTemplateCode('low_stock_notification')
->setOrigTemplateVariables('');
$template->save(); 
