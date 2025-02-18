<?php 
if(isset($_GET["iframe"]) && $_GET["iframe"] == "true"){ 

    ?>
    <style>
        header {
            display: none;
        }
        .check_giftcard.saldo-check-container {
            padding: 0px;
        }
    </style>


<?php }
?>
<div class="check_giftcard saldo-check-container">
    
    <div class="card card-header">
        <h2>Sjekk saldo på gavekort</h2>
        <p>Skriv inn gavekortkoden, for å se saldo og betalingshistorikk.</p>

        <div class="card-form">
            <form id="saldo-check-form" method="post">
                <input type="text" name="giftcard_code" placeholder="Tast inn koden din her" <?php  if(isset($_POST["giftcard_code"])  && $_POST["giftcard_code"] != ""){ ?>value="<?php echo $_POST['giftcard_code'];?>" <?php } ?> required>
                <button type="submit" class="check-button">Sjekk</button>
            </form>
        </div>

        <?php if(isset($data["remaining_saldo"])){ ?>
            <div class="card-saldo">
                <p>Din saldo er <span class="saldo-amount"><?php echo wc_price($data["remaining_saldo"]);?></span></p>
            </div>
        <?php  } ?>    
    </div>
    
    
    
    <?php if(isset($data["remaining_saldo"])){ ?>
        <div class="card card-log">
            <h3>Historikk</h3>
            <?php if(isset($data["logs"]) && !empty($data["logs"])){ 

                foreach($data["logs"] as $log){ 
                ?>
                        <div class="log-entry">
                            <span class="log-icon">📄</span>
                            <span class="log-text">Brukt <strong><?php echo $log["date"];?></strong> Beløp: <strong><?php echo wc_price($log["amount_used"]);?></strong> på <span class="listing-name"><?php echo $log["listing_name"];?></span></span>
                        </div>
                <?php } ?>

            <?php  } ?> 
            <div class="log-entry">
                <span class="log-icon">📄</span>
                <span class="log-text">Kjøpt den <strong><?php echo $data["purchased_date"];?></strong> Beløp: <strong><?php echo wc_price($data["purchased_amount"]);?></strong> <br>
                Gavekortet kan brukes på: <span class="listing-name"><?php echo $data["listing_name_data"];?></span></span>
            </div>
        </div>
    <?php  } ?>   

</div>