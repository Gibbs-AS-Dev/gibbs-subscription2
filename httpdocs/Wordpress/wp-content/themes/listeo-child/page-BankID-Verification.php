<?php

/*
 * Template Name: BankID-Verification
 *
 * This is the template that displays if the current user is already verified by Criipto login [BankID] or not?
 *
 * @package WPVoyager
 */
 


	  //echo do_shortcode('[criipto acr_values="urn:grn:authn:no:bankid" domain="gibbs-as-test.criipto.id" client_id="urn:auth0:dev-mvj1-12l" implicit="id_token" authority="https" //port="port" afterLogOutRedirect="https://www.staging50.gibbs.no/min-gibbs/"]', true);
echo do_shortcode('[criipto acr_values="urn:grn:authn:no:bankid" domain="gibbs.criipto.id" client_id="urn:my:application:identifier:8382" implicit="id_token" authority="https" port="port" afterLogOutRedirect="https://www.gibbs.no/min-gibbs/"]', true);

		/*?>
		<script type="text/javascript">
			 window.location.href ='" . plugins_url('../../plugins/login-by-criipto/requestAuth.php', __FILE__) . "?signout=true';
		</script>
		<?php
	
			?>
		<script type="text/javascript">
		window.location.href = 'https://gibbs-as-test.criipto.id/oauth2/authorize?acr_values=urn%3Agrn%3Aauthn%3Ano%3Abankid&response_mode=query&response_type=code&redirect_uri=https%3A%2F%2Fwww.staging50.gibbs.no%2Fbankid-verification&client_id=urn%3Aauth0%3Adev-mvj1-12l&nonce=ff0907bf5083c9d3c2e2859bc43076b8&state=f137a4e35c7158f3018bc3dff7500716&scope=openid';
		</script>
		<?php 
   	*/

	
?>	