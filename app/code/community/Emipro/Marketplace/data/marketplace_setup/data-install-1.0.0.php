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

$templatecheck = Mage::getModel('adminhtml/email_template')->getCollection();
$code = array();
if ($templatecheck->count() > 0) {
    foreach ($templatecheck->getData() as $item) {
        $code[] = $item['template_code'];
    }
}
if (!in_array('seller_account_verification', $code)) {
    $template = Mage::getModel('adminhtml/email_template');
    $template->setTemplateSubject("Seller Account Verification")
            ->setTemplateCode('seller_account_verification')
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
                           <td>
                                         <p> Thank you for registering to become a seller on  {{var store.getFrontendName()}}</p>
<p> Please click the button below to verify your email address. We will then review your application and confirm when you account is active.</p>

                             </td>
                    </tr>
                    <tr>
                           <td>
                                       <a href="{{var redirect_url}}" style=" line-height: 25px; padding: 9px; text-decoration: none;background-color: #AD0D0D;color: white;border-radius: 4px;font-size: 13px;cursor: pointer;  ">Verify Email Address</a> 
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
            ->setOrigTemplateCode('seller_account_verification')
            ->setOrigTemplateVariables('');

    $template->save();
}
if (!in_array('seller_account_success', $code)) {
    $template1 = Mage::getModel('adminhtml/email_template');
    $template1->setTemplateSubject("Seller Account Successfully Created")
            ->setTemplateCode('seller_account_success')
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
                            <p style=" font-weight:normal; line-height:22px; margin:0 0 11px 0;">Dear   {{var sellername}}</p1>
                        </td>
                    </tr>
 
                    <tr>
                           <td>
                                         <p>Your Account Successfully created.</p>
                             </td>
                    </tr>
                    <tr>
                           <td>
                                         <p>Seller Admin link  : <a href="{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/" alt="">{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/</a></p>
                                         <p>UserName  : {{var sellerusername}}</p>
                                         <p>Password    : {{var sellerpass}} </p>
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
            ->setOrigTemplateCode('seller_account_success')
            ->setOrigTemplateVariables('');
    $template1->save();
}
if (!in_array('seller_transaction_request', $code)) {
    $template3 = Mage::getModel('adminhtml/email_template');
    $template3->setTemplateSubject("Seller Withdraw Request")
            ->setTemplateCode('seller_transaction_request')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr>
                    <tr>
                           <td>
                                         <p>{{var mailmessage}}</p>
                             </td>
                    </tr>
                    <tr>
                           <td>
                                         <p>Seller Id :  {{var seller_id}} </p>
                             </td>
                    </tr>
<tr>
                           <td>
                                         <p>Seller Name :  {{var sellername}}</p>
                             </td>
                    </tr>

                    <tr>
                           <td>
                                         <p>Transaction status : {{var status}}</p>
                             </td>
                    </tr>
                    <tr>
                           <td>
                                         <p>Amount : {{var amount}}</p>
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
            ->setOrigTemplateCode('seller_transaction_request')
            ->setOrigTemplateVariables('');
    $template3->save();
}
if (!in_array('customer_question_to_seller', $code)) {
    $template4 = Mage::getModel('adminhtml/email_template');
    $template4->setTemplateSubject("Customer Question To Seller")
            ->setTemplateCode('customer_question_to_seller')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr>
                    {{if questionmail}}   
                  <tr>
                           <td>
<p>You have been asked a question about :</p>
<p>Product Name : {{var productname}}</p>
<p>Sku : {{var sku}}</p>
<p>The Subject is:{{var subject}}</p>
<p>The Question is:{{var question}}</p>
 <p>Please log into your Seller panel to answer  : <a href="{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/" alt="">{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/</a></p>
                             </td>
                    </tr>
                   {{/if}}
                    <tr>
                           <td>
                                         
                             </td>
                    </tr>
                   <tr>
                           <td>
                                        
                             </td>
                    </tr>
                    {{if answer}}
                      <tr>
                           <td>
 <p>You asked this question about :  Product Name : {{var productname}}  -  Sku :  {{var sku}}</p>

<p>Your question was :   {{var question}}</p>
<p>The answer is : {{var answer}}</p>

We hope that helps

Thank you for visting.
                             </td>
                    </tr>
                     {{/if}}
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
            ->setOrigTemplateCode('customer_question_to_seller')
            ->setOrigTemplateVariables('');
    $template4->save();
}
if (!in_array('seller_invoice_notification', $code)) {
    $template5 = Mage::getModel('adminhtml/email_template');
    $template5->setTemplateSubject("Seller Order Notification")
            ->setTemplateCode('seller_invoice_notification')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
            <!-- [ header starts here] -->
            <tr>
                <td valign="top"><a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
                <tr>
                    <td class="email-heading">
                        <h3>You have got order from store {{var frontname}}.</h3>
                        <p>You can check the status of  order by <a href="{{config path="web/unsecure/base_url"}}marketplace/sellerlogin/">logging into your account</a>.</p>
                    </td>
                </tr>
                <tr>
        <td class="order-details">
            <h3>Invoice <span class="no-link">#{{var invoice.increment_id}}</span></h3>
            <p>Order <span class="no-link">#{{var order.increment_id}}</span></p>
        </td>
    </tr>
    <tr class="order-information">
        <td>
            {{var prodhtml}}
            <table cellpadding="0" cellspacing="2" border="0">
                <tr>
                    <td class="address-details">
                        <div style="font-size:14px;font-weight:bold;">Bill to:</div>
                       <div class="no-link">{{var order.billing_address.format("html")}}</div>
                    </td>
                    {{depend order.getIsNotVirtual()}}
                    <td class="address-details">
                        <div style="font-size:14px;font-weight:bold;">Ship to:</div>
                       <div class="no-link">{{var order.shipping_address.format("html")}}</div>
                    </td>
                    {{/depend}}
                </tr>
                <tr>
                    {{depend order.getIsNotVirtual()}}
                    <td class="method-info">
                        <div style="font-size:14px;font-weight:bold;">Shipping method:</div>
                        <div>{{var order.shipping_description}}</div>
                    </td>
                    {{/depend}}
                    <td class="method-info">
                        <div style="font-size:14px;font-weight:bold;">Payment method:</div>
                        {{var payment_html}}
                    </td>
                </tr>
              
            </table>
        </td>
             </tr>
              <tr>
                <td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">Thank you, <strong>{{var store.getFrontendName()}}</strong></p></center></td>
                   </tr>

</table>
</div>
</body>')
            ->setTemplateStyles('')
            ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
            ->setAddedAt(Mage::getSingleton('core/date')->gmtDate())
            ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
            ->setOrigTemplateCode('seller_invoice_notification')
            ->setOrigTemplateVariables('');
    $template5->save();
}
if (!in_array('seller_product_approved', $code)) {
    $template6 = Mage::getModel('adminhtml/email_template');
    $template6->setTemplateSubject("Seller Product Status Change")
            ->setTemplateCode('seller_product_approved')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
            <!-- [ header starts here] -->
            <tr>
                <td valign="top"><a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="email-heading">
                        <h1>Your Product has been {{var status}}</h1>
                        <p>You can check following products  {{var status}}  by login in admin panel {{config path="web/unsecure/base_url"}}admin</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
       <tr>
             <td>
                    <br/>
                     <p>
                     Your product sku :
                      </p>
                     <p> 
                            {{var sku}}    
                     </p>
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
            ->setOrigTemplateCode('seller_product_approved')
            ->setOrigTemplateVariables('');
    $template6->save();
}
if (!in_array('seller_status_change', $code)) {
    $template7 = Mage::getModel('adminhtml/email_template');
    $template7->setTemplateSubject("Seller Status Changed")
            ->setTemplateCode('seller_status_change')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
            <!-- [ header starts here] -->
            <tr>
                <td valign="top"><a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                        <p>Hello {{var sellername}},</p>
                    </td>
                </tr>   
                <tr>
                    <td>
                        <p>Your Seller Account has been {{var status}}</p>

                    </td>
                </tr>
            </table>
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
            ->setOrigTemplateCode('seller_status_change')
            ->setOrigTemplateVariables('');
    $template7->save();
}
//code for new seller notification to admin

if(!in_array('seller_account_admin_notification',$code))
{
$template1 = Mage::getModel('adminhtml/email_template');
$template1->setTemplateSubject("New seller notification")
                ->setTemplateCode('seller_account_admin_notification')
                ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top" style="padding:20px 0 20px 0">
                <!-- [ header starts here] -->
                <table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
                    <tr>
                        <td valign="top">
                            <a href="{{store url=""}}"><img src="{{var logo_url}}" alt="{{var logo_alt}}" style="margin-bottom:10px;" border="0"/></a>
                        </td>
                    </tr>
                    <!-- [ middle starts here] -->
                    <tr>
                        <td valign="top">
                            <p style=" font-weight:normal; line-height:22px; margin:0 0 11px 0;">Dear   {{var name}}</p>
                        </td>
                    </tr>
 
                    <tr>
                           <td>
                                         <p>New seller  registered an account. </p>
                                        <p>Name : {{var sellername}}</p>
                                        <p>Email : {{var selleremail}}</p>
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
->setOrigTemplateCode('seller_account_admin_notification')
->setOrigTemplateVariables('');
$template1->save();
}

try {
    $staruppage = new Mage_Core_Model_Config();
    $staruppage->saveConfig('admin/startup/page', "marketplacedashboard", 'default', 0);
} catch (Exception $e) {}

//static block for seller sign up success page 
$cmsBlock = Mage::getModel('cms/block')->load("seller_signup_success");
if (!$cmsBlock->getId()) {
    $content = '<p><strong>Thank you for register as seller.</strong></p>
<p>Seller Information sent to admin. Please wait for confirmation.</p>
<p>We have sent mail for verify email address. Please check email.</p>'; 
    $cmsBlock = array(
        'title' => Mage::helper('marketplace')->__('Seller Signup Success'),
        'identifier' => 'seller_signup_success', 
        'content' => $content,
        'is_active' => 1,
        'sort_order' => 0,
        'stores' => array(0)
    );
    Mage::getModel('cms/block')->setData($cmsBlock)->save();
}

//static block for seller email verification success page 
$cmsBlock = Mage::getModel('cms/block')->load("seller_email_confirmed");
if (!$cmsBlock->getId()) {
    $content = '<p><strong><span style="font-size: large;">Thank you for confirm your email. </span></strong></p>
<p>Seller Admin Details sent to you.</p>';    
    $cmsBlock = array(
        'title' => Mage::helper('marketplace')->__('Seller Email Confirmed'),
        'identifier' => 'seller_email_confirmed',
        'content' => $content, 
        'is_active' => 1,
        'sort_order' => 0,
        'stores' => array(0)
    ); 
    Mage::getModel('cms/block')->setData($cmsBlock)->save();
}

//static block for seller sign up terms and conditions  
$cmsBlock = Mage::getModel('cms/block')->load("seller_termsconditions"); 
if (!$cmsBlock->getId()) {
    $content = '<p><strong><span style="font-size: large;">Terms & Conditions</span></strong></p>
<p>Please read carefully terms and conditions.</p>';    
    $cmsBlock = array(
        'title' => Mage::helper('marketplace')->__('Terms & Conditions'),
        'identifier' => 'seller_termsconditions',
        'content' => $content, 
        'is_active' => 1,
        'sort_order' => 0,   
        'stores' => array(0) 
    ); 
    Mage::getModel('cms/block')->setData($cmsBlock)->save();
}
 

