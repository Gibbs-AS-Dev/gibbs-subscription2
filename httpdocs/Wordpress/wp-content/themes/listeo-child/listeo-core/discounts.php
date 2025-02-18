<?php
$template_loader = new Listeo_Core_Template_Loader;

if (!defined('ABSPATH')) {
    exit;
}

get_header(get_option('header_bar_style', 'standard'));
$post_info = $post;
$post_meta = get_post_meta($post_info->ID);
$gallery_style = get_post_meta( $post->ID, '_gallery_style', true );
$template_loader->get_template_part( 'single-partials/single-listing','gallery' );
?>

<style>
table {
  font-family: arial, Roboto;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<div class="container dfdfdf">
    <h2 style="text-align: center; padding: 30px 0px 25px 0px;"><?php the_title(); ?></h2>
    <div class="row" style="padding: 0px 0px 20px 0px;">
        <div class="col-md-12 asdf">
            <input class="selected-user" list="brow">
            <datalist id="brow">
                <?php
                    $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
                    foreach($users as $user){
                        $userdata = get_userdata($user->ID);?>
                        <option data-id="<?php echo $user;?>" value="<?php echo $user;?>">
                    <?php }
                ?>
            </datalist>  
        </div>
        <div class="col-md-6">
            <input id="discount-percentage" type="number" placeholder="%" min="0" max="100">
        </div>
        <div class="col-md-6">
            <input class="button submit-discount" type="button" placeholder="%" min="0" max="100" value="Submit">
        </div>
    </div>

    <table>
        <tr>
            <th>Målgruppe</th>
            <th>Rabatt %</th>
        </tr>
     
        <?php
            $users = ['Barn', 'Funksjonshemmede', 'Senior', 'Idrettslag', 'Ungdom', 'Medlem', 'Lag og foreninger', 'Trening (for organiserte)', 'Kamp (for organiserte)', 'Private', 'Bedrifter', 'Ansatte'];
            foreach($users as $user){
                $userdata = get_userdata($user->ID);
                $val = get_post_meta($post->ID,$user);
                if(isset($val[0])){?>
                <tr>
                    <td><?php echo $user;?></td>
                    <td><?php echo $val[0];?></td>
                </tr>
                <?php
                }
            }
        ?>
        
    </table>
</div>

<?php get_footer();?>
<script>
    jQuery('.submit-discount').on('click', function(){
        if(jQuery('.selected-user').val()){
            
            if(jQuery('#discount-percentage').val()){
                var user = jQuery('.selected-user').val();
                var discount = jQuery('#discount-percentage').val();
                var id ='<?php echo $post->ID;?>';
                var ajax_data = {
					'action': 'get_user_for_discount',
					'user': user,
					'discount': discount,
                    'id': id
				};

				jQuery.ajax({
					type: "POST",
					url: listeo.ajaxurl,
					data: ajax_data,
					success: function () {
                        console.log(';success ',user,id,discount);
                        location.reload();
                    }
				});
            }else{
                alert('Please set discount percentage!');
                return false;
            }
        }else{
            alert('Please select user!');
            return false;
        }
    })  
</script>