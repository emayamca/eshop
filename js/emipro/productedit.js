function marketplacecategory(reloadurl){
	if($('m_categories').value!='' && $('m_categories').value !='-1')
	{
		new Ajax.Request(reloadurl, {
						method: 'post',
						parameters: {categoryid:$('m_categories').value},						
						onComplete: function(transport) {							
							result = transport.responseText;
							dropdownvalue = result.trim();
							if(dropdownvalue!='')
							{ 
								$('attribute_set_id').value= parseInt(dropdownvalue);  
							}else{ 
								$('attribute_set_id').selectedIndex = 0;
							}
						} 
					});
		document.getElementById("continue_button").style.display = "block";	
	}
	else{
		document.getElementById("continue_button").style.display = "none";	
	}
}


	
