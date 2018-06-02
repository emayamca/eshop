/*
 * Date : 2/16/2017 to 2/21/2017
 */
flag1=0;
fl = 0;
jQuery(document).ready(function(){
    jQuery(".form-language").addClass("form-language-normal");
    jQuery(window).scroll(function(){
        var scroll = jQuery(window).scrollTop();
        if(scroll>40){
            jQuery(".top-links-container").css({"position":"fixed","top":"-45px","width":"100%"});    
            jQuery(".form-language").addClass("form-language-onscroll");
            jQuery(".header-wrapper").hide();    
            jQuery(".header").addClass("scrollheader"); 
			jQuery(".main-nav").addClass("scrollmain-nav"); 
            jQuery(".mini-cart").css("width","45px"); 
            jQuery("#search_mini_form").css("width","370px"); 
            jQuery(".main-container").css("padding", "0px 0px 50px"); 
        }
        else{
            jQuery(".top-links-container").css({"position":"relative","top":"0px"}); 
            jQuery(".form-language").removeClass("form-language-onscroll");
            jQuery(".header-wrapper").show();    
            jQuery(".header").removeClass("scrollheader"); 
			jQuery(".main-nav").removeClass("scrollmain-nav"); 
            jQuery("#search_mini_form").css("width","447px"); 
            jQuery(".main-container").css("padding", "20px 0px 50px");
        }
    });
    
    jQuery("#close-catpop").click(function(){
            jQuery(".catpop").css("display","none");
            jQuery(".phantom-overlay").css("display","none");
            jQuery("body").removeClass("blockScroll");
    });
    
    jQuery(document).keyup(function(e) {
          if (e.keyCode === 27){
                jQuery(".catpop").css("display","none");
                jQuery(".phantom-overlay").css("display","none");
                jQuery("body").removeClass("blockScroll");
          }
    });
    
    jQuery(".phantom-overlay").click(function(){
            jQuery(".catpop").css("display","none");
            jQuery(".phantom-overlay").css("display","none");
            jQuery("body").removeClass("blockScroll");
    });
	
	jQuery("#close-brandpop").click(function(){
            jQuery(".brandpop").css("display","none");
            jQuery(".phantom-overlay").css("display","none");
            jQuery("body").removeClass("blockScroll");
    });
	
	jQuery(".phantom-overlay").click(function(){
            jQuery(".brandpop").css("display","none");
            jQuery(".phantom-overlay").css("display","none");
            jQuery("body").removeClass("blockScroll");
    });
    
    jQuery(document).keyup(function(e) {
          if (e.keyCode === 27){
                jQuery(".brandpop").css("display","none");
                jQuery(".phantom-overlay").css("display","none");
                jQuery("body").removeClass("blockScroll");
          }
    });
   
    jQuery("#accountBlockSwitch").mouseover(function(){
        jQuery(".accountBlockOuter").css({"display":"block"});
    });
    jQuery("#accountBlockSwitch").mouseout(function(){
        jQuery(".accountBlockOuter").css({"display":"none"});
    });
    
    jQuery(".menu-icon").click(function(){
        jQuery("html").addClass("blockScroll");
    });
    jQuery(".mobile-nav-overlay").click(function(){
        jQuery("html").removeClass("blockScroll");
    });
    
    jQuery(".mini-cart").click(function(){
		 jQuery(".topCartContent").toggle();
    });

    jQuery("#bulkOrderForm-toggleBtn").click(function(){
        if(fl===0){
            jQuery(".bulkOrderForm").css({"height":"400px","overflow":"initial","padding":"45px 12px"});
            jQuery(".bulkOrderForm>h4").css({"top":"0px"});
            fl=1;
        }
        else if(fl===1){
            jQuery(".bulkOrderForm").css({"height":"40px","overflow":"hidden","padding":"16px 12px"});
            jQuery(".bulkOrderForm>h4").css({"top":"20px"});
            fl=0;
        }    
    });

		jQuery('.click-sbt-eqy').click(function() {
			jQuery('.submit-enquires-form').show();
			jQuery('.enquity-overlay').show();
		});
		
		jQuery('.enquity-closebtn').click(function() {
			jQuery('.submit-enquires-form').hide();
			jQuery('.enquity-overlay').hide();
		});
    
});
