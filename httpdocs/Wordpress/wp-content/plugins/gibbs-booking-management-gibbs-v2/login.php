<div id="sign-in-dialog-bk">
                    
    <?php 
    $urll = home_url()."/".$_SERVER['REQUEST_URI'];
		
    echo do_shortcode("[gibbs_register_login redirect=$urll]");
    ?>
</div>