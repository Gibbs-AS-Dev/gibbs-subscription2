<?php
get_header();

?>
<link rel='stylesheet' id='simple-line-icons-css' href='<?php echo home_url();?>/wp-content/themes/listeo/css/simple-line-icons.css?ver=3.5.75' type='text/css' media='all' />
<style>
    .card-page .col-md-12{
        display: flex;
        justify-content: center;
        margin-top: 64px;

    }
    .card-page .card{
       padding: 43px;
       width: 45%;
    }
</style>
<div class="container card-page">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">

                   
                        <div class="crad-header">
                           <h4 class="d-flex justify-content-center"><?php  echo __("Session run out","Gibbs");?></h4>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-center text-align-center w-100">
                               <p><?php  echo __("Your session has expired. Please try again or close the window","Gibbs");?></p>
                            </div>
                            
                        </div>
                        <?php if(isset($_GET["redirect"])){ ?>
                            <div class="row">
                                <div class="d-flex justify-content-center text-align-center w-100">
                                <a href="<?php echo $_GET["redirect"];?>"><button class="btn btn-primary btn-back-list">Go back to listing</button></a>
                                </div>
                                
                            </div>
                        <?php  } ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script>
    jQuery(".login_click").click(function(){
        jQuery("#lg_reg_modal").show();
    })
</script>