jQuery(document).on('click', '[class*="gibbs_popup-"]', function(e) {
	
	e.preventDefault();

	let select_cl = "";
	this.classList.forEach(function(cl,ind){
		
		if(cl.includes("gibbs_popup-") == true){
			select_cl = cl;
		}

	})

	if(select_cl != ""){
		var page_id = select_cl.split("-");

		page_id = page_id[1];

		let formdata = {
			action : "get_popup",
			page_id : page_id
		}
        
        jQuery.ajax({
          type: "POST",
          url: my_ajax_object.ajax_url,
          data: formdata,
          success: function (data) {
          	 jQuery(".gibbs_modal").remove();
             jQuery("body").append(data)
			 jQuery(".gibbs_modal").show();
          }
        });
	}
})