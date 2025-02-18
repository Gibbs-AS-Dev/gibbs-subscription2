<?php

/*
 * Template Name: BankID-Verification
 *
 * This is the template that displays if the current user is already verified by Criipto login [BankID] or not?
 *
 * @package WPVoyager
 */

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.
$url .= $_SERVER['HTTP_HOST'];

$current_user = wp_get_current_user();
$userid = $current_user->ID;

if (  get_user_meta( intval($userid),  $verified,   true ) ) {
    echo do_shortcode('[criipto acr_values="urn:grn:authn:no:bankid" domain="gibbs-as-test.criipto.id" client_id="urn:auth0:dev-mvj1-12l" implicit="id_token" authority="https" port="port" afterLogOutRedirect=" $url . /min-gibbs/"]', true);
    update_user_meta ( intval($userid),  $verified,  true);
    /*?>
<script type="text/javascript">
    window.location.href ='" . plugins_url('../../plugins/login-by-criipto/requestAuth.php', __FILE__) . "?signout=true';
</script>
<?php */
    /*
/staging50.gibbs.no/public_html/wp-content/plugins/login-by-criipto

/staging50.gibbs.no/public_html/wp-content/themes/listeo-child


    ?>
<script type="text/javascript">
window.location.href = 'https://gibbs-as-test.criipto.id/oauth2/authorize?acr_values=urn%3Agrn%3Aauthn%3Ano%3Abankid&response_mode=query&response_type=code&redirect_uri=https%3A%2F%2Fwww.staging50.gibbs.no%2Fbankid-verification&client_id=urn%3Aauth0%3Adev-mvj1-12l&nonce=ff0907bf5083c9d3c2e2859bc43076b8&state=f137a4e35c7158f3018bc3dff7500716&scope=openid';
</script>
<?php */
}
else{

    ?>
    <p style="font-size: 2rem;"><a href=""><i class="im im-icon-Security-Check margin-right-15"></i>User is already verified</a></p>
    <?php
}

/*else
{
            ?>
    <script type="text/javascript">
    window.location.href = 'https://www.staging50.gibbs.no/min-gibbs/';
    </script>
    <?php
}
*/
/*
You have already found out that using update_user_meta() if the meta field for the user does not exist, it will be added. ie update_user_meta() can do the task of add_user_meta()

However, the difference between them is the return values

update_user_meta()
returns False if no change was made (if the new value was the same as previous value) or if the update failed, umeta_id if the value was different and the update a success.

NOTE: as of v3.4.2 it returns the umeta_id on success (instead of true) and false on failure



add_user_meta()
return Primary key id for success. No value (blank) for failure. Primary key id for success.
*/

?>