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
if (!in_array('New invoice custom template', $code)) {
    $template = Mage::getModel('adminhtml/email_template');
    $template->setTemplateSubject("{{var store.getFrontendName()}}: Invoice # {{var invoice.increment_id}} for Order # {{var order.increment_id}}")
            ->setTemplateCode('New invoice custom template')
            ->setTemplateText('<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="email-heading">
                        <h1>Thank you for your order from {{var store.getFrontendName()}}.</h1>
                        <p>You can check the status of your order by <a href="{{store url="customer/account/"}}">logging into your account</a>.</p>
                    </td>
                    <td class="store-info">
                        <h4>Order Questions?</h4>
                        <p>
                            {{depend store_phone}}
                            <b>Call Us:</b>
                            <a href="tel:{{var phone}}">{{var store_phone}}</a><br>
                            {{/depend}}
                            {{depend store_hours}}
                            <span class="no-link">{{var store_hours}}</span><br>
                            {{/depend}}
                            {{depend store_email}}
                            <b>Email:</b> <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                            {{/depend}}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="order-details">
             <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
                             <p style="margin:10px;"><img src="{{config path="web/unsecure/base_url"}}media/{{var seller_info.company_logo}}" style="max-height:50px;"/></p>
                    </td>
                    <td>
                            <p style="margin:10px;">
                            <h4 style="font-weight:bold;margin:2px 0px;">Seller : {{var seller_info.store_name}}</h4>
                            <p style="margin:0px;">
                                    GST Number : {{var seller_info.vat_number}}
                            </p>
                            <p style="margin:0px;">
                                    Address : {{var seller_info.street1}}  {{var seller_info.street2}} {{var seller_info.city}}  Postcode :  {{var seller_info.postcode}}  Telephone: {{var seller_info.telephone}}
                            </p>
                           </p>

                    </td>
                </tr>
             </table>
        </td>
    </tr>

    <tr>
        <td class="order-details">
            <h3>Your Invoice <span class="no-link">#{{var invoice.increment_id}}</span></h3>
            <p>Order <span class="no-link">#{{var order.increment_id}}</span></p>
        </td>
    </tr>
    <tr class="order-information">
        <td>
            {{if comment}}
            <table cellspacing="0" cellpadding="0" class="message-container">
                <tr>
                    <td>{{var comment}}</td>
                </tr>
            </table>
            {{/if}}
            {{layout area="frontend" handle="sales_email_order_invoice_items" invoice=$invoice order=$order}}
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="address-details">
                        <h6>Bill to:</h6>
                        <p><span class="no-link">{{var order.billing_address.format("html")}}</span></p>
                    </td>
                    {{depend order.getIsNotVirtual()}}
                    <td class="address-details">
                        <h6>Ship to:</h6>
                        <p><span class="no-link">{{var order.shipping_address.format("html")}}</span></p>
                    </td>
                    {{/depend}}
                </tr>
                <tr>
                    {{depend order.getIsNotVirtual()}}
                    <td class="method-info">
                        <h6>Shipping method:</h6>
                        <p>{{var order.shipping_description}}</p>
                    </td>
                    {{/depend}}
                    <td class="method-info">
                        <h6>Payment method:</h6>
                        {{var payment_html}}
                    </td>
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
            ->setOrigTemplateCode('New invoice custom template')
            ->setOrigTemplateVariables('');

    $template->save();
}
if($template->getId()){
    Mage::getModel('core/config')->saveConfig('sales_email/invoice/template', $template->getId());
    Mage::getModel('core/config')->saveConfig('sales_email/invoice/guest_template', $template->getId());
}