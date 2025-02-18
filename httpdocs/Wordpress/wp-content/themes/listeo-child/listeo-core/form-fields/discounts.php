<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
	

<div class="row" style="padding: 00px 0px 20px 0px;">

    <div class="col-md-4 asdf">
        <input  type="text" class="selected-user" placeholder="Målgruppe">
        <!-- <input placeholder="Målgruppe" class="selected-user" list="brow">
        <datalist id="brow">
            <?php
                $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
                foreach($users as $user){
                    $userdata = get_userdata($user->ID);?>
                    <option data-id="<?php echo $user;?>" value="<?php echo $user;?>">
                <?php }
            ?>
        </datalist>   -->
    </div>
    <div class="col-md-4">
        <input id="discount-percentage" type="number" placeholder="Rabatt %" min="0" max="100" step="any">
    </div>
    <div class="col-md-4">
        <input class="button submit-discount" type="button" placeholder="%" min="0" max="100" value="Legg til">
    </div>
</div>

     
        <?php
            $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
            $discountss = get_post_meta($_REQUEST['listing_id'],"_discounts_user",true); 

            if($discountss && is_array($discountss) && count($discountss) > 0){


                foreach($discountss as $user){
                    ?>
    					<table class="<?php echo str_replace(' ', '', $user['discount_name']);?>-table" style="width:100%;">
    						<tr>
                                <th>Målgruppe</th>
                                <th>Rabatt %</th>
    						</tr>
    					
    						<tr>
    							<td style="padding:0px 5px 0px 5px;"><input  type="text" name="discount_user[]" value="<?php echo $user["discount_name"];?>" placeholder="Målgruppe" required></td>
    							<td style="padding:0px 5px 0px 5px;"><input   name="discount_user_percentage[]" value="<?php echo $user["discount_value"];?>" type="number" placeholder="Rabatt %" min="0" max="100" step="any" required></td>
    							<td style="padding:0px 5px 0px 5px;"><div class="fm-close <?php echo str_replace(' ', '', $user['discount_name']);?>" data-name="<?php echo str_replace(' ', '', $user['discount_name']);?>" ><a class="delete" ><i class="fa fa-remove" aria-hidden="true"></i></a></div></td>

    						</tr>
    					</table>

                    <?php
                }
            }    
        ?>
        

<script>
    jQuery(document).on('click','.submit-discount', function(){

        if(jQuery('.selected-user').val()){
            if(jQuery('#discount-percentage').val()){


                var user = jQuery('.selected-user').val();
                var discount = jQuery('#discount-percentage').val();
                var id ='<?php echo $_REQUEST['listing_id']?>';
                var ajax_data = {
					'action': 'get_user_for_discount',
					'user': user,
					'discount': discount,
                    'id': id
				};

                var user_r = user.replace(" ","");
                user_r = user_r.replace(/\s/g, '');
                user_r = user_r.replace(/\s+/g, '');;
                var newDiscountElement = 
                    `<table class="${user_r}-table" style="width:100%;">`
						+'<tr>'
                            +'<th>Målgruppe</th>'
                            +'<th>Rabatt %</th>'
						+'</tr>'
						+'<tr>'
							+`<td style="padding:0px 5px 0px 5px;"><input  type="text" name="discount_user[]" value="${user}" placeholder="${user}" required></td>`
							+`<td style="padding:0px 5px 0px 5px;"><input   name="discount_user_percentage[]" value="${discount}" placeholder="${discount}" type="number" min="0" max="100" step="any" required></td>`
							+`<td style="padding:0px 5px 0px 5px;"><div class="fm-close ${user_r}" data-name="${user_r}"user_r ><a class="delete" ><i class="fa fa-remove" aria-hidden="true"></i></a></div></td>`

						+'</tr>'
                    +'</table>';

                if(jQuery(`.${user_r}-table`).length == 1){
                    jQuery(`.${user_r}-table`).remove();
                    jQuery('.form-field-_discount-container').append(newDiscountElement);
                }else{
                    jQuery('.form-field-_discount-container').append(newDiscountElement);
                }    
                    
				/*jQuery.ajax({
					type: "POST",
					url: listeo.ajaxurl,
					data: ajax_data,
					success: function () {
                        if(jQuery(`.${user}-table`).length == 1){
                            jQuery(`.${user}-table`).remove();
                            jQuery('.form-field-_discount-container').append(newDiscountElement);
                        }else{
                            jQuery('.form-field-_discount-container').append(newDiscountElement);
                        }
                        
                    }
				});*/
            }else{
                alert('Please set discount percentage!');
                return false;
            }
        }else{
            alert('Please select user!');
            return false;
        }
    });

	jQuery(document).on('click','.discounts .fm-close', function(){
		
		var user = jQuery(this).attr('data-name');
        var id ='<?php echo $_REQUEST['listing_id']?>';
        var ajax_data = {
			'action': 'remove_discount_edit_listing',
			'user': user,
            'id': id
		};
        jQuery(`.${user}-table`).remove();

		/*jQuery.ajax({
			type: "POST",
			url: listeo.ajaxurl,
			data: ajax_data,
			success: function () {
                jQuery(`.${user}-table`).remove();

            }
		});*/
	});
</script>