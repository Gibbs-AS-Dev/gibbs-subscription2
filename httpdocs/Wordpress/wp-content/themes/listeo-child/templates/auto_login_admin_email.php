<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gibbs Snarvei</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f9f9f9;" bgcolor="#f9f9f9">
  
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px;">
          
          <!-- Header -->
          <tr>
            <td align="center" style="background-color: #008474; color: #ffffff; padding: 15px; font-size: 18px; font-weight: bold;">
              Gibbs Snarvei
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td style="padding: 20px; color: #333333; font-size: 16px; line-height: 1.6;">
              <p style="margin: 0 0 15px;">Trykk på linken for å aktivere.</p>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center">
                    <a href="<?php echo home_url();?>/dashbord?accept_auto_login=true&jwt_token=<?php echo urlencode($jwt);?>&success=true&show_message=true"
                      target="_blank"
                      style="display: inline-block; padding: 12px 24px; background-color: #008474; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; text-align: center;">
                      Aktiver Gibbs Snarvei
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding: 15px; font-size: 14px; color: #888888; background-color: #f5f5f5;">
              Dette er en automatisk e-post. Vennligst ikke svar direkte på denne meldingen.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
