<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt gavekort</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f0f0; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background-color: #f1f1f1; padding: 15px;  text-align: center;">
            <h2 style="margin: 0; color: #333; font-size: 1.6em;">Her er ditt gavekort</h2>
        </div>

        <!-- Gift Card Section -->
        <div style="background-color: #3c8c6c; color: #ffffff; padding: 30px; text-align: center; border-radius: 8px; margin: 20px;">
            <div style="display: flex; justify-content: left; align-items: left; margin-bottom: 15px;">
                
            </div>
            <h3 style="margin: 0; font-size: 1.4em;">Gavekort</h3>
            <p style="font-size: 1.2em; margin: 15px 0; background: #2e6f59; padding: 10px 15px; border-radius: 4px; display: inline-block;">
                <?php echo esc_html($gift_data['code']); ?>
            </p>
            <p style="font-size: 0.9em; margin: 0;text-align:left;padding-top: 30px;">Utsendt av <?php echo $group_name;?></p>
        </div>

        <!-- Amount Display -->
        <div style="text-align: center; padding: 0px;">
            <p style="font-size: 2em; color: #3c8c6c; margin: 10;">Kr <?php echo esc_html($gift_data['purchased_amount']); ?>,-</p>
        </div>

        <!-- Expiry Date -->
        <p style="text-align: center; font-size: 0.9em; color: #777; margin: 0px;">
            Gyldig til: <?php echo esc_html($gift_data['expire_date']); ?>
        </p>


           <!-- Message -->
           <p style="text-align: center; font-size: 1em; color: #333; padding: 30px; margin: 30px;">
          <?php echo esc_html($gift_data['giftcard_description']); ?>
        </p>

        <hr style="border: none; border-top: 1px solid #e3e3e3; margin-bottom: 30px 0;">

        <!-- Footer -->
        <p style="text-align: center; font-size: 0.8em; color: #999; padding: 0 20px;">
            Dette gavekortet er ikke refunderbart og kan ikke byttes inn i kontanter. <br>  
        </p>
        <p>
             
        </p>
    </div>
    <div class='download_pdf_div'>
        <form method="post" action="<?php echo admin_url('admin-ajax.php');?>" style="display: flex;justify-content: center;">
            <input type="hidden" name="action" value="downloadGiftPDF">
            <input type="hidden" name="giftcode" value="<?php echo $giftcode;?>">
            <button type="submit" class="btn btn-primary" style="background-color: #3c8c6c;color: #ffffff;padding: 10px;border: none;border-radius: 5px;margin-top: 40px;cursor: pointer;">Last ned gavekort</button>
        </form>
    </div>
</body>
</html>
