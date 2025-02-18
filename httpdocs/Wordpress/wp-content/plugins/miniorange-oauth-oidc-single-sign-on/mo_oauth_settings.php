<?php
/**
 * Plugin Name: OAuth Single Sign On - SSO (OAuth client)
 * Plugin URI: http://miniorange.com
 * Description: This plugin enables login to your WordPress site using OAuth apps like Google, Facebook, EVE Online and other.
 * Version: 3.0.01
 * Author: miniOrange
 * Author URI: https://www.miniorange.com
 * License: miniOrange
 */


require "\137\x61\x75\164\x6f\154\157\x61\x64\x2e\160\150\160";
require_once "\x6d\157\55\x6f\x61\165\164\x68\x2d\143\x6c\x69\x65\156\x74\55\x70\x6c\x75\x67\151\x6e\55\x76\145\x72\163\151\157\156\x2d\165\160\x64\x61\x74\145\x2e\x70\150\x70";
use MoOauthClient\Base\BaseStructure;
use MoOauthClient\MOUtils;
use MoOauthClient\GrantTypes\JWTUtils;
use MoOauthClient\Base\InstanceHelper;
use MoOauthClient\MoOauthClientWidget;
use MoOauthClient\Free\MOCVisualTour;
use MoOauthClient\Free\CustomizationSettings;
global $mx;
$sr = new InstanceHelper();
$mx = $sr->get_utils_instance();
$n2 = $mx->get_plugin_config()->get_current_config();
$LN = $sr->get_settings_instance();
$Oo = new BaseStructure();
$TF = $sr->get_login_handler_instance();
$Fj = new CustomizationSettings();
$Fj = $Fj->mo_oauth_custom_icons_intiater();
function register_mo_oauth_widget()
{
    register_widget("\134\x4d\x6f\x4f\141\x75\164\x68\x43\x6c\151\145\156\164\x5c\x4d\157\117\141\165\x74\x68\x43\x6c\151\x65\x6e\164\127\151\144\147\145\x74");
}
function mo_oauth_shortcode_login($W8)
{
    global $mx;
    $YZ = new MoOauthClientWidget();
    if ($mx->check_versi(4) && $mx->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\165\x74\150\137\141\x63\164\151\166\x61\164\x65\x5f\x73\151\156\147\154\145\x5f\154\157\x67\151\x6e\x5f\146\x6c\157\167")) {
        goto bBi;
    }
    if (empty($W8["\162\145\x64\151\162\x65\143\164\137\165\162\154"])) {
        goto jkn;
    }
    return $W8 && isset($W8["\x61\160\x70\x6e\x61\x6d\x65"]) && !empty($W8["\x61\x70\x70\x6e\x61\x6d\x65"]) ? $YZ->mo_oauth_login_form($QZ = true, $Wa = $W8["\x61\x70\x70\156\x61\155\145"], $a7 = $W8["\x72\x65\144\x69\x72\x65\143\164\137\165\162\154"]) : $YZ->mo_oauth_login_form($QZ = false, $Wa = '', $a7 = $W8["\x72\x65\144\x69\162\x65\143\164\x5f\x75\162\x6c"]);
    jkn:
    return $W8 && isset($W8["\141\160\x70\156\141\x6d\145"]) && !empty($W8["\141\160\x70\156\141\x6d\145"]) ? $YZ->mo_oauth_login_form($QZ = true, $Wa = $W8["\141\160\160\156\x61\155\145"]) : $YZ->mo_oauth_login_form(false);
    goto M1R;
    bBi:
    return $YZ->mo_activate_single_login_flow_form();
    M1R:
}
add_action("\167\x69\x64\x67\145\x74\163\x5f\x69\156\151\164", "\162\145\147\151\163\164\145\162\137\x6d\x6f\137\x6f\x61\x75\x74\150\x5f\167\151\x64\x67\145\x74");
add_shortcode("\155\x6f\x5f\x6f\x61\x75\164\150\x5f\x6c\x6f\x67\151\156", "\x6d\x6f\137\157\141\x75\164\150\x5f\x73\x68\x6f\162\x74\x63\157\x64\145\137\x6c\x6f\147\151\156");
add_action("\x69\156\151\164", "\x6d\157\x5f\x67\145\x74\x5f\166\x65\162\163\x69\x6f\156\137\x6e\165\155\142\x65\x72");
add_action("\151\x6e\151\x74", "\x6d\x6f\137\157\141\x75\164\150\x5f\x66\x72\157\156\164\x73\x6c\157");
add_action("\x69\x6e\151\164", "\155\x6f\137\157\x61\165\x74\150\137\x62\141\x63\x6b\163\154\157");
function mo_get_version_number()
{
    if (!(isset($_GET["\141\x63\164\151\x6f\156"]) && $_GET["\141\x63\164\x69\157\x6e"] === "\x6d\157\137\x76\145\x72\x73\x69\x6f\156\x5f\x6e\x75\x6d\142\x65\162" && isset($_GET["\141\160\x69\113\x65\x79"]) && $_GET["\141\x70\151\113\x65\x79"] === "\143\62\60\141\67\x64\x66\70\66\142\x33\144\64\144\x31\x61\142\145\62\144\64\67\144\x30\x65\61\142\x31\x66\x38\64\67")) {
        goto Pvv;
    }
    echo mo_oauth_client_options_plugin_constants::Version;
    exit;
    Pvv:
}
function mo_oauth_frontslo()
{
    $mx = new MOUtils();
    if (!($mx->check_versi(4) && isset($_SERVER["\122\105\121\x55\105\x53\x54\x5f\125\122\111"]) && sanitize_url($_SERVER["\122\x45\x51\125\105\123\124\137\125\x52\111"]) != NULL && strpos(sanitize_url($_SERVER["\x52\x45\x51\125\105\x53\124\x5f\x55\x52\111"]), "\146\162\157\x6e\x74\x63\150\x61\x6e\x6e\x65\x6c\137\154\157\147\157\165\164") != false)) {
        goto yo5;
    }
    $b2 = get_current_user_id();
    $JX = get_user_meta($b2, "\155\157\137\157\x61\165\164\150\x5f\x63\154\x69\x65\156\x74\x5f\154\x61\x73\x74\x5f\x69\x64\137\164\x6f\153\x65\156", true);
    $CS = new JWTUtils($JX);
    $Yh = $CS->get_decoded_payload();
    $QR = sanitize_url($_SERVER["\122\105\x51\125\x45\x53\124\x5f\125\x52\111"]);
    $AM = parse_url($QR);
    parse_str($AM["\161\165\145\x72\171"], $Hz);
    $V4 = $Yh["\x73\x69\x64"];
    $J3 = $Hz["\163\x69\x64"];
    if ($V4 === $J3) {
        goto Daa;
    }
    $zF = array("\x63\x6f\x64\145" => 400, "\144\145\163\143\x72\x69\x70\164\x69\157\156" => "\125\x73\145\162\40\x49\144\x20\156\x6f\x74\40\x66\x6f\165\x6e\x64");
    wp_send_json($zF, 400);
    goto C5_;
    Daa:
    $Fh = '';
    if (!isset($Yh["\x69\x61\x74"])) {
        goto iX2;
    }
    $Fh = $Yh["\x69\x61\x74"];
    iX2:
    if (!is_user_logged_in()) {
        goto vNm;
    }
    mo_slo_logout_user($b2);
    vNm:
    C5_:
    yo5:
}
function mo_oauth_backslo()
{
    $mx = new MOUtils();
    if (!($mx->check_versi(4) && isset($_SERVER["\x52\x45\121\125\x45\123\x54\x5f\x55\122\x49"]) && sanitize_url($_SERVER["\122\105\121\x55\105\123\x54\137\125\x52\x49"]) != NULL && strpos(sanitize_url($_SERVER["\x52\x45\121\x55\105\x53\124\x5f\125\122\111"]), "\x62\141\143\x6b\x63\150\x61\156\x6e\145\154\137\154\x6f\147\157\165\x74") != false)) {
        goto k69;
    }
    $IU = file_get_contents("\160\x68\x70\72\57\x2f\x69\x6e\160\165\164");
    $w2 = explode("\75", $IU);
    if (!(json_last_error() !== JSON_ERROR_NONE)) {
        goto OBY;
    }
    $IU = array_map("\x65\163\x63\x5f\x61\164\164\162", sanitize_post($_POST));
    OBY:
    if ($w2[0] == "\x6c\x6f\x67\157\165\x74\137\x74\157\153\145\156") {
        goto FTb;
    }
    $zF = array("\143\x6f\x64\x65" => 400, "\x64\x65\163\x63\162\x69\x70\x74\151\x6f\x6e" => "\x54\x68\145\x20\114\x6f\x67\157\165\x74\40\x74\157\x6b\145\156\x20\x69\163\x20\x65\x69\x74\x68\145\162\x20\x6e\x6f\164\x20\163\x65\156\x74\40\x6f\x72\40\163\145\x6e\164\x20\151\156\143\x6f\162\162\145\143\164\154\171\56");
    wp_send_json($zF, 400);
    goto O9Z;
    FTb:
    $Z0 = $w2[1];
    $CS = new JWTUtils($Z0);
    $bj = isset($_REQUEST["\x61\x70\x70\x6e\x61\155\x65"]) && sanitize_text_field($_REQUEST["\x61\160\x70\156\x61\155\145"]) != NULL ? sanitize_text_field($_REQUEST["\x61\x70\x70\156\141\x6d\x65"]) : '';
    $H0 = false;
    $Zy = $mx->get_app_by_name($bj);
    $H0 = $Zy->get_app_config("\152\x77\x6b\x73\x75\x72\154");
    $Ui = $Zy->get_app_config("\x75\163\x65\162\156\x61\155\145\x5f\141\x74\x74\162");
    $Yh = $CS->get_decoded_payload();
    $YG = '';
    $V4 = '';
    if (!isset($Yh["\163\165\x62"])) {
        goto xOq;
    }
    $YG = $Yh["\x73\165\142"];
    xOq:
    if (!isset($Yh["\x73\x69\x64"])) {
        goto WQN;
    }
    $V4 = $Yh["\163\151\x64"];
    WQN:
    $Fh = '';
    if (!isset($Yh["\151\141\x74"])) {
        goto LM3;
    }
    $Fh = $Yh["\151\x61\164"];
    LM3:
    global $wpdb;
    if (isset($Yh[$Ui])) {
        goto Bc1;
    }
    if ($YG) {
        goto jlJ;
    }
    if ($V4) {
        goto eOu;
    }
    $zF = array("\143\x6f\144\145" => 400, "\x64\x65\x73\x63\162\151\x70\x74\151\157\156" => "\x54\150\x65\40\x6c\157\x67\x6f\165\164\40\164\x6f\x6b\x65\x6e\40\151\163\40\x76\141\x6c\151\x64\x20\142\165\164\x20\165\163\145\x72\x20\x6e\157\x74\40\x69\144\x65\156\x74\151\x66\x69\145\x64\56");
    wp_send_json($zF, 400);
    goto y_s;
    eOu:
    $mO = "\123\105\114\x45\x43\124\40\x75\163\145\x72\x5f\x69\144\40\106\122\117\115\40\x60\x77\160\137\165\163\x65\162\155\x65\x74\141\140\40\x57\110\x45\122\x45\40\x6d\x65\x74\x61\x5f\x76\141\x6c\165\145\75\47{$V4}\47\40\x61\156\144\x20\x6d\145\x74\141\x5f\153\145\171\x3d\x27\x6d\157\x5f\x62\x61\x63\153\143\x68\x61\x6e\x6e\x65\x6c\x5f\141\164\164\x72\137\163\151\144\x27\x3b";
    $NU = $wpdb->get_results($mO);
    $b2 = $NU[0]->{"\x75\x73\145\x72\137\151\x64"};
    y_s:
    goto y2w;
    jlJ:
    $mO = "\x53\105\x4c\105\103\124\40\x75\163\x65\162\x5f\151\x64\x20\106\x52\117\x4d\x20\x60\167\x70\x5f\x75\163\145\162\x6d\145\x74\141\140\x20\127\110\105\x52\105\x20\155\x65\x74\x61\x5f\166\141\154\x75\145\75\x27{$YG}\x27\x20\x61\x6e\x64\x20\155\145\x74\x61\137\153\145\x79\x3d\47\x6d\x6f\x5f\142\141\x63\x6b\143\150\141\x6e\x6e\x65\x6c\137\141\164\164\162\x5f\163\x75\142\47\73";
    $NU = $wpdb->get_results($mO);
    $b2 = $NU[0]->{"\x75\x73\145\x72\137\151\x64"};
    y2w:
    goto Ph4;
    Bc1:
    $b2 = get_user_by("\x6c\157\147\x69\156", $Lj)->ID;
    Ph4:
    if ($b2) {
        goto s9o;
    }
    $zF = array("\x63\157\x64\x65" => 400, "\144\x65\163\143\162\151\160\164\x69\157\156" => "\124\150\145\40\154\x6f\147\x6f\165\x74\40\x74\157\x6b\145\156\x20\x69\x73\40\166\141\154\151\x64\40\142\165\164\x20\165\163\145\x72\40\156\x6f\164\x20\x69\144\x65\x6e\x74\151\146\x69\145\x64\56");
    wp_send_json($zF, 400);
    goto TsK;
    s9o:
    mo_slo_logout_user($b2);
    TsK:
    O9Z:
    k69:
}
function mo_slo_logout_user($b2)
{
    $ew = WP_Session_Tokens::get_instance($b2);
    $ew->destroy_all();
    $zF = array("\143\x6f\144\145" => 200, "\144\x65\x73\143\x72\x69\160\x74\x69\157\156" => "\124\x68\x65\x20\125\x73\145\162\40\x68\x61\x73\x20\142\x65\145\156\40\154\x6f\147\147\145\144\x20\x6f\x75\164\x20\163\x75\x63\x63\145\x73\x73\146\x75\154\x79\56");
    wp_send_json($zF, 200);
}
function miniorange_oauth_visual_tour()
{
    $Vp = new MOCVisualTour();
}
if (!($mx->get_versi() === 0)) {
    goto ARm;
}
add_action("\141\x64\x6d\x69\x6e\137\x69\x6e\x69\x74", "\x6d\151\x6e\x69\157\162\141\x6e\x67\x65\137\157\141\165\x74\x68\x5f\x76\151\x73\x75\x61\x6c\137\x74\157\165\162");
ARm:
function mo_oauth_deactivate()
{
    global $mx;
    do_action("\155\x6f\x5f\143\154\x65\141\x72\137\160\x6c\165\147\x5f\x63\x61\x63\150\x65");
    $mx->deactivate_plugin();
}
register_deactivation_hook(__FILE__, "\x6d\157\137\x6f\x61\165\x74\x68\x5f\x64\145\x61\x63\x74\151\x76\141\x74\x65");
