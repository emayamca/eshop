    jQuery(document).ready(function(){
        jQuery('ul.tabs li').click(function(){
            var tab_id = jQuery(this).attr('data-tab');

            jQuery('ul.tabs li').removeClass('current');
            jQuery('.tab-content').removeClass('current');

            jQuery(this).addClass('current');
            jQuery("#"+tab_id).addClass('current');
        });
        
        jQuery("#prevTab").css("display","none");
        jQuery(".submit-btn").css("display","none");
        
        jQuery("#proceedTab").click(function(){
            proceedAltius();
        });
        
        jQuery("#2ndtab").click(function(){
            proceedAltius();
        });
        
        jQuery("#1sttab").click(function(){
            prevAltius();
        });

        jQuery("#prevTab").click(function(){
            prevAltius();
        });
        
        jQuery("#bg_fade").click(function(){
            jQuery("html").css("overflow-y","auto");
            prevAltius();
        });
    });
    
    function killscroll(){
        jQuery("html").css("overflow-y","hidden");
        prevAltius();
    }        
    function livescroll(){
        jQuery("html").css("overflow-y","auto");
        prevAltius();
    }  
    jQuery(document).keyup(function(e) {
          if (e.keyCode === 27){
                jQuery("html").css("overflow-y","auto");
                prevAltius();
          }
    });
        
    function prevAltius(){
        jQuery("#tab-2").removeClass('current');
        jQuery("#tab-1").addClass('current');
        jQuery("#1sttab").addClass('current');
        jQuery("#2ndtab").removeClass('current');
        jQuery("#prevTab").css("display","none");
        jQuery("#proceedTab").css("display","block");
        jQuery(".submit-btn").css("display","none");
    }
    function proceedAltius(){
        jQuery('.modal').focus();
        jQuery("#tab-1").removeClass('current');
        jQuery("#tab-2").addClass('current');
        jQuery("#2ndtab").addClass('current');
        jQuery("#1sttab").removeClass('current');
        jQuery("#proceedTab").css("display","none");
        jQuery("#prevTab").css("display","block");
        jQuery(".submit-btn").css("display","block");
    }