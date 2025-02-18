<style>
    .autologin-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
        margin-top: -92px;
    }

    .login_main_message {
        text-align: center;
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
    }

    .login_main_message h2 {
        font-size: 24px;
        color: #333333;
        margin-bottom: 15px;
    }

    .login_main_message p {
        font-size: 16px;
        color: #666666;
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .login_main_message p span {
        font-size: 14px;
        color: #888888;
    }

    .confirmation-link {
        display: inline-block;
        margin-top: 15px;
        color: #008474;
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
    }

    .confirmation-link:hover {
        text-decoration: underline;
    }

</style>
<?php
$jwt_approve = get_user_meta(get_current_user_id(),"jwt_approve", true);
?>
<div class="autologin-container">
    <div class="login_main_message">
        <h2>Gibbs snarvei</h2>
        <?php if(isset($_GET["admin"]) && $_GET["admin"] == true){
            $jwt_token = get_user_meta(get_current_user_id(),"jwt_token", true);

            $current_url = home_url(remove_query_arg(array_keys($_GET), $_SERVER['REQUEST_URI']));

            $jwt_link = $current_url."?jwt_login=true&jwt_token=".$jwt_token."&success=true"; ?>
            <p>
               Gibbs rask login har nå blitt aktivert 🥳
            </p>

            <a href="<?php echo $jwt_link;?>" 
            target="_blank" 
            style="display: inline-block; background-color: #008474; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; text-align: center; text-decoration: none;">
            Lagre gibbs som en snarvei
            </a>

            <br>
        <?php }else if($jwt_approve == "true"){ 
            $jwt_token = get_user_meta(get_current_user_id(),"jwt_token", true);

            $current_url = home_url(remove_query_arg(array_keys($_GET), $_SERVER['REQUEST_URI']));

            $jwt_link = $current_url."?jwt_login=true&jwt_token=".$jwt_token."&success=true";
            ?>

          <!--   <a href="/dashbord" style="display: inline-block; background-color: #008474; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; text-align: center; text-decoration: none;">
                Gå til dashbord
            </a> -->

            <br>


            <!-- Lenke med farge #008474 -->
            <p style="font-weight: bold;">Har du lagret Gibbs som snarvei?</p>
<a href="https://support.gibbs.no/index.php/knowledge-base/automatisk-innlogging/" 
   style="color: #008474; text-decoration: none; font-size: 16px;" 
   target="_blank" 
   rel="noopener noreferrer">
    Se hvordan her
</a>




           <!--  <p>Kopier login link:</p> -->
            <!-- <p style="word-wrap: break-word;"><?php echo $jwt_link;?></p> -->

        <?php }else{ ?>
        <p>
       Nå kan du lagre Gibbs som en snarvei på din enhet og du slipper å måtte logge inn hver gang. 
       Aktiver Gibbs snarvei ved å godkjenne lenken du får på e-post. 🚀
        </p>
    
        <a href="<?php echo home_url(); ?>?auto_login=true&send_email=true" 
        class="confirmation-link" 
        id="activate-link">Send aktiveringslink på e-post</a>
        <?php if(isset($_GET["send_email"]) && $_GET["send_email"] == "true"){ ?>
        <p id="confirmation-message" style="color: green; margin-top: 10px;">
            E-post er sendt! Sjekk også spam-mappen.
        </p>
        <?php } ?>



        
        
        <?php } ?>
    </div>
</div>
