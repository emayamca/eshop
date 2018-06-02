/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Validation.add('validate-store-url', 'Please enter a valid Store URL. Enter lowercase characters. For example emipro,emipro-tech,emipro_tech,emipro123.', function(v) {
    return Validation.get('IsEmpty').test(v) || /^[a-z0-9_\/-]*$/.test(v);
});

Validation.add('validate-custom-web-url', 'Please enter a valid Website URL. For example http://www.emipro.com,http://www.emipro-tech.com,http://www.emipro_tech.com,http://www.emipro123.com', function(v) {
       v = (v || '').replace(/^\s+/, '').replace(/\s+$/, ''); 
       return Validation.get('IsEmpty').test(v) || /^(http(s?):\/\/)?(www\.)+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,3})+(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/.test(v);   
});
     
