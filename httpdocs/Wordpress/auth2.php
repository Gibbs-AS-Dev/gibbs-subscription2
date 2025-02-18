<a href="https://gibbs.criipto.id/oauth2/authorize?response_type=id_token&client_id=urn:my:application:identifier:8382&redirect_uri=https://staging5.dev.gibbs.no/auth.php&scope=openid&state=etats">
  Sign in with Norwegian BankID
</a>
<?php 


require  '/var/www/vhosts/dev.gibbs.no/staging5.dev.gibbs.no/Wordpress/wp-content/themes/listeo-child/criipto/vendor/autoload.php';
use Jumbojett\OpenIDConnectClient;
$oidc = new OpenIDConnectClient('https://gibbs.criipto.id',
                                'urn:my:application:identifier:8382',
                                '/yzeiTsOzJZyrsOo7+Apv43amf3bDEltW5jUixNnbNM=');
                                
$oidc->authenticate();
// Or any other available claim based on your used authentication method: https://docs.criipto.com/verify/e-ids/
$email = $oidc->getVerifiedClaims('email');
echo "<pre>"; print_r($oidc);
die("dlkjdlkjd");
echo "<pre>"; print_r($_GET);
echo "<pre>"; print_r(file_get_contents('php://input')); 
if(isset($_GET["code"])){
   // $code = $_GET["code"];
   $client_id = 'urn:my:application:identifier:8382';
   $redirect_uri = 'https://staging5.dev.gibbs.no/auth.php';
   $client_secret = '/yzeiTsOzJZyrsOo7+Apv43amf3bDEltW5jUixNnbNM=';
   $authorization_code = $_GET['code'];
   $state = $_GET['state'];
   
   $base_url = 'https://gibbs.criipto.id/oauth2/token';


// Prepare cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $base_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Insecure, use for testing only

// Prepare the Authorization header
$auth_header = base64_encode(urlencode($client_id) . ':' . urlencode($client_secret));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Basic ' . $auth_header
));

// Prepare POST data
$post_data = http_build_query(array(
    'grant_type' => 'authorization_code',
    'code' => $authorization_code,
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri
));
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

// Execute cURL request
$response = curl_exec($ch);
$error = curl_error($ch);

    if ($err) {
    echo "cURL Error #:" . $err;
    } else {
        echo "<pre>"; print_r($response); die;
    }
}
echo "<pre>"; print_r($_GET);
echo "<pre>"; print_r(file_get_contents('php://input')); die;