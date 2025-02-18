<script type="text/javascript">
	jQuery(".repeated_check").click(function(){
		var that = this;

		setTimeout(function(){

			if(that.checked){
				jQuery(".repeating_div").show();
			}else{
				jQuery(".repeating_div").hide();
			}

		},100)
	})

	function selectClassAdd(thisDiv){

		if(jQuery(thisDiv).hasClass("selected")){
           jQuery(thisDiv).removeClass("selected")
		}else{
			jQuery(thisDiv).addClass("selected")
		}

		

	}
</script>