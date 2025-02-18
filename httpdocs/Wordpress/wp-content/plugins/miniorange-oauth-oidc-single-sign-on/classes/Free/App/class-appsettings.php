<?php


namespace MoOauthClient\Free;

use MoOauthClient\App;
class AppSettings
{
    private $app_config;
    public function __construct()
    {
        $this->app_config = array("\x63\x6c\151\x65\156\164\137\151\x64", "\143\154\x69\145\x6e\x74\137\163\x65\143\x72\x65\x74", "\163\143\157\x70\x65", "\162\x65\144\151\162\x65\143\x74\137\x75\x72\x69", "\141\160\160\x5f\164\x79\160\145", "\x61\165\x74\x68\x6f\162\x69\172\145\165\x72\x6c", "\141\143\143\145\163\x73\x74\157\x6b\x65\156\165\x72\x6c", "\162\145\163\x6f\165\162\143\145\x6f\167\156\145\162\x64\x65\x74\141\x69\x6c\x73\165\x72\x6c", "\147\162\x6f\x75\x70\144\145\164\x61\x69\x6c\163\165\162\154", "\152\167\x6b\x73\x5f\x75\x72\151", "\144\x69\163\160\154\x61\171\x61\x70\x70\x6e\x61\x6d\x65", "\x61\160\x70\x49\x64", "\155\157\x5f\157\141\x75\164\150\137\162\145\163\x70\x6f\x6e\x73\145\137\x74\x79\x70\x65");
    }
    public function save_app_settings()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\151\x73\x61\x62\x6c\145\144";
        if (empty($n2["\155\x6f\x5f\x64\x74\x65\x5f\x73\164\x61\164\145"])) {
            goto YW;
        }
        $Wz = $mx->mooauthdecrypt($n2["\x6d\x6f\x5f\x64\164\x65\137\163\164\141\x74\145"]);
        YW:
        if (!($Wz == "\x64\151\163\x61\x62\154\145\x64")) {
            goto B0;
        }
        if (!(isset($_POST["\x6d\157\x5f\157\141\165\164\150\137\141\x64\144\x5f\141\160\160\x5f\x6e\157\x6e\143\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\x5f\x6f\141\165\x74\x68\137\141\144\x64\x5f\x61\160\160\x5f\156\157\x6e\x63\145"])), "\155\x6f\137\x6f\x61\x75\164\150\x5f\141\144\x64\137\141\x70\160") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\137\157\x61\165\164\x68\137\x61\x64\x64\x5f\141\x70\x70" === $_POST[\MoOAuthConstants::OPTION])) {
            goto Gp;
        }
        if (!($mx->mo_oauth_check_empty_or_null($_POST["\x6d\x6f\x5f\x6f\141\x75\164\150\x5f\x63\x6c\x69\x65\156\x74\x5f\151\x64"]) || $mx->mo_oauth_check_empty_or_null($_POST["\x6d\157\x5f\157\141\x75\x74\x68\137\143\154\x69\x65\156\x74\x5f\163\145\x63\x72\145\164"]))) {
            goto LL;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\120\154\145\x61\x73\145\40\x65\x6e\164\x65\162\40\x76\141\154\x69\x64\x20\103\x6c\x69\x65\x6e\x74\x20\111\x44\40\x61\156\x64\40\x43\154\151\145\x6e\164\x20\x53\x65\143\162\145\x74\x2e");
        $mx->mo_oauth_show_error_message();
        return;
        LL:
        $bj = isset($_POST["\155\x6f\x5f\157\x61\165\x74\150\x5f\x63\165\x73\164\x6f\155\137\141\160\x70\x5f\x6e\x61\x6d\x65"]) ? sanitize_text_field(wp_unslash($_POST["\x6d\157\137\x6f\x61\x75\x74\x68\x5f\x63\x75\163\x74\x6f\x6d\137\141\x70\x70\137\156\x61\x6d\x65"])) : false;
        $Z6 = $mx->get_app_by_name($bj);
        $Z6 = false !== $Z6 ? $Z6->get_app_config() : [];
        $Q7 = false !== $Z6;
        $FO = $mx->get_app_list();
        if (!(!$Q7 && is_array($FO) && count($FO) > 0 && !$mx->check_versi(4))) {
            goto LC;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\x6f\x75\40\x63\141\156\40\x6f\156\x6c\x79\x20\x61\144\x64\40\61\x20\141\160\x70\x6c\151\x63\141\164\x69\157\156\x20\167\151\x74\150\x20\x66\162\145\145\x20\x76\x65\162\x73\x69\x6f\x6e\x2e\x20\125\160\147\162\141\x64\145\40\164\x6f\40\145\156\164\145\x72\160\x72\x69\x73\145\x20\166\145\x72\x73\151\x6f\x6e\x20\x69\146\40\171\157\165\40\167\141\x6e\164\x20\x74\157\40\x61\x64\x64\x20\x6d\157\162\145\40\141\x70\x70\154\x69\x63\x61\164\x69\x6f\156\163\56");
        $mx->mo_oauth_show_error_message();
        return;
        LC:
        $Z6 = !is_array($Z6) || empty($Z6) ? array() : $Z6;
        $Z6 = $this->change_app_settings($_POST, $Z6);
        $A5 = isset($_POST["\155\157\x5f\157\141\165\164\150\x5f\144\151\x73\x63\x6f\166\x65\x72\171"]) && isset($Z6["\x69\163\137\x64\151\163\x63\157\x76\145\x72\171\x5f\166\x61\x6c\x69\x64"]) && $Z6["\151\163\137\x64\x69\x73\143\157\x76\x65\162\x79\137\x76\141\x6c\x69\x64"] == "\x74\162\x75\x65";
        if (!$A5) {
            goto AG;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\165\x72\x20\163\145\164\164\x69\x6e\x67\163\40\x61\x72\145\x20\x73\141\166\145\x64\40\163\165\143\x63\145\163\x73\x66\165\x6c\x6c\x79\x2e");
        $Z6["\x6d\157\x5f\144\x69\163\x63\157\166\x65\x72\x79\137\166\141\x6c\151\x64\141\x74\151\157\x6e"] = "\x76\141\x6c\x69\144";
        $mx->mo_oauth_show_success_message();
        goto kl;
        AG:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x3c\x73\164\162\x6f\x6e\147\76\105\x72\x72\x6f\162\72\x20\74\57\163\x74\162\157\x6e\x67\76\x20\x49\x6e\x63\x6f\x72\162\145\x63\x74\40\104\157\x6d\x61\151\156\x2f\x54\145\x6e\x61\156\x74\57\x50\x6f\x6c\x69\143\x79\x2f\x52\145\x61\154\155\56\x20\x50\154\145\141\163\145\x20\143\x6f\x6e\x66\x69\147\x75\162\145\x20\x77\x69\164\x68\x20\143\x6f\x72\162\x65\143\164\40\166\x61\154\x75\145\x73\40\x61\156\144\x20\x74\x72\x79\40\x61\x67\x61\x69\156\56");
        $Z6["\x6d\157\137\x64\151\x73\143\157\166\x65\162\x79\x5f\x76\x61\x6c\x69\x64\141\x74\x69\157\156"] = "\151\156\166\x61\x6c\x69\144";
        $mx->mo_oauth_show_error_message();
        kl:
        $FO[$bj] = new App($Z6);
        $FO[$bj]->set_app_name($bj);
        $FO = apply_filters("\155\x6f\x5f\157\x61\165\x74\x68\x5f\143\x6c\x69\x65\156\164\137\163\x61\166\x65\137\x61\144\x64\x69\x74\151\x6f\x6e\x61\154\137\146\151\145\x6c\x64\137\163\x65\x74\x74\x69\x6e\x67\x73\x5f\151\156\164\145\162\156\x61\x6c", $FO);
        $mx->mo_oauth_client_update_option("\155\157\137\x6f\x61\x75\x74\150\137\141\x70\160\x73\x5f\x6c\x69\163\x74", $FO);
        wp_redirect("\x61\144\155\151\x6e\x2e\160\x68\x70\x3f\160\x61\x67\x65\x3d\155\x6f\x5f\157\x61\165\164\150\137\x73\145\x74\164\151\156\147\x73\46\x74\x61\x62\x3d\143\x6f\x6e\146\151\147\46\x61\143\164\151\157\156\x3d\165\160\144\x61\164\x65\46\141\160\x70\75" . urlencode($bj));
        Gp:
        if (!(isset($_POST["\x6d\x6f\137\157\x61\165\164\x68\137\141\164\164\162\151\142\x75\x74\x65\x5f\x6d\141\160\160\x69\x6e\x67\x5f\156\x6f\x6e\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\157\141\x75\164\x68\x5f\x61\164\164\x72\151\x62\165\164\145\x5f\x6d\141\160\160\x69\156\x67\x5f\156\157\156\x63\x65"])), "\155\157\137\x6f\141\165\x74\150\137\141\164\x74\162\x69\x62\165\164\x65\137\x6d\141\x70\x70\151\156\x67") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\x6f\x5f\x6f\141\165\164\x68\x5f\x61\164\164\162\x69\142\x75\x74\145\137\155\141\160\160\x69\156\x67" === $_POST[\MoOAuthConstants::OPTION])) {
            goto bS;
        }
        $bj = sanitize_text_field(wp_unslash(isset($_POST[\MoOAuthConstants::POST_APP_NAME]) ? $_POST[\MoOAuthConstants::POST_APP_NAME] : ''));
        $Zy = $mx->get_app_by_name($bj);
        $xA = $Zy->get_app_config('', false);
        $post = array_map("\x65\x73\x63\x5f\x61\x74\164\162", $_POST);
        $xA = $this->change_attribute_mapping($post, $xA);
        $xA = apply_filters("\x6d\x6f\x5f\x6f\x61\165\x74\150\x5f\143\x6c\x69\x65\156\x74\137\163\141\x76\x65\x5f\x61\x64\x64\151\164\151\x6f\x6e\141\154\137\x61\x74\164\162\x5f\155\141\x70\160\x69\x6e\147\137\163\x65\x74\164\x69\x6e\x67\163\137\151\156\164\x65\x72\156\x61\154", $xA);
        $k9 = $mx->set_app_by_name($bj, $xA);
        if (!$k9) {
            goto r8;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\157\x75\x72\40\163\145\x74\164\151\156\147\x73\40\141\x72\145\x20\x73\x61\166\145\144\x20\x73\x75\x63\x63\x65\163\x73\146\x75\x6c\x6c\x79\56");
        $mx->mo_oauth_show_success_message();
        goto jL;
        r8:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x54\x68\x65\162\x65\x20\x77\x61\163\40\141\x6e\40\145\162\x72\157\x72\x20\163\x61\166\151\x6e\x67\x20\163\x65\164\164\x69\x6e\147\163\x2e");
        $mx->mo_oauth_show_error_message();
        jL:
        wp_safe_redirect("\x61\x64\x6d\x69\x6e\56\x70\x68\x70\x3f\x70\x61\x67\x65\75\x6d\157\x5f\x6f\x61\165\x74\150\137\x73\145\x74\164\x69\156\147\x73\46\164\141\142\75\x63\157\156\x66\151\x67\x26\x61\143\164\x69\x6f\156\75\x75\160\x64\141\x74\x65\46\x61\x70\160\75" . rawurlencode($bj));
        bS:
        B0:
        do_action("\x6d\157\137\157\141\165\x74\x68\x5f\143\x6c\151\x65\156\x74\x5f\x73\x61\x76\x65\137\141\x70\x70\137\x73\x65\164\x74\x69\156\147\163\x5f\151\156\164\x65\162\156\x61\154");
    }
    public function change_app_settings($post, $Z6)
    {
        global $mx;
        $A0 = '';
        $rv = '';
        $yZ = '';
        $bj = sanitize_text_field(wp_unslash(isset($post[\MoOAuthConstants::POST_APP_NAME]) ? $post[\MoOAuthConstants::POST_APP_NAME] : ''));
        if ("\145\x76\145\157\x6e\154\x69\156\145" === $bj) {
            goto s9F;
        }
        $BQ = isset($post["\155\x6f\x5f\x6f\141\x75\x74\x68\x5f\x64\151\x73\143\x6f\x76\x65\x72\171"]) ? $post["\155\157\137\157\x61\x75\164\150\x5f\144\x69\x73\x63\157\x76\x65\162\x79"] : null;
        if (!empty($BQ)) {
            goto bEO;
        }
        $A0 = isset($post["\155\157\137\x6f\141\x75\164\x68\137\141\x75\164\150\x6f\x72\151\x7a\x65\x75\162\x6c"]) ? stripslashes($post["\155\x6f\137\157\x61\165\x74\150\x5f\x61\x75\x74\x68\157\162\x69\x7a\x65\165\162\154"]) : '';
        $rv = isset($post["\x6d\157\x5f\x6f\141\x75\164\x68\x5f\x61\x63\143\145\163\x73\x74\x6f\x6b\145\x6e\x75\x72\x6c"]) ? stripslashes($post["\155\x6f\x5f\x6f\141\x75\x74\x68\x5f\x61\x63\143\x65\163\163\x74\157\x6b\145\156\x75\x72\x6c"]) : '';
        $yZ = isset($post["\x6d\x6f\137\x6f\141\165\164\x68\x5f\x72\145\163\x6f\165\162\x63\x65\x6f\x77\156\x65\x72\144\x65\164\141\x69\154\x73\x75\x72\x6c"]) ? stripslashes($post["\x6d\x6f\137\x6f\141\165\x74\x68\x5f\162\x65\163\157\165\x72\x63\x65\157\x77\x6e\x65\162\x64\145\164\x61\x69\x6c\163\165\162\154"]) : '';
        goto Pur;
        bEO:
        $Z6["\145\x78\x69\x73\x74\x69\156\x67\x5f\141\160\160\137\x66\x6c\157\x77"] = true;
        if (isset($post["\155\157\x5f\x6f\x61\x75\164\x68\x5f\160\162\x6f\166\x69\144\x65\162\x5f\x64\157\155\x61\151\156"])) {
            goto nv;
        }
        if (!isset($post["\x6d\157\137\157\x61\165\164\x68\x5f\x70\162\x6f\166\151\144\145\x72\137\x74\x65\x6e\141\156\164"])) {
            goto Lh;
        }
        $zc = stripslashes(trim($post["\x6d\x6f\137\157\x61\x75\164\x68\x5f\x70\162\157\166\x69\x64\x65\x72\x5f\x74\145\x6e\x61\x6e\164"]));
        $BQ = str_replace("\x74\x65\156\x61\156\164", $zc, $BQ);
        $Z6["\x74\x65\156\141\156\x74"] = $zc;
        Lh:
        goto RV;
        nv:
        $Vm = stripslashes(rtrim($post["\x6d\157\137\157\141\x75\x74\150\137\x70\162\x6f\x76\x69\x64\x65\162\137\144\x6f\155\141\x69\156"], "\x2f"));
        $BQ = str_replace("\144\157\155\x61\x69\156", $Vm, $BQ);
        $Z6["\x64\x6f\155\x61\151\x6e"] = $Vm;
        RV:
        if (isset($post["\x6d\157\137\x6f\141\165\x74\150\137\x70\x72\157\166\x69\x64\145\162\137\160\x6f\154\x69\143\x79"])) {
            goto DU;
        }
        if (!isset($post["\155\x6f\x5f\x6f\x61\x75\164\150\x5f\x70\162\x6f\x76\x69\144\x65\x72\137\162\x65\x61\x6c\155"])) {
            goto Ot;
        }
        $TM = stripslashes(trim($post["\155\157\x5f\157\x61\x75\164\x68\x5f\160\162\157\x76\x69\144\x65\162\x5f\162\145\141\x6c\x6d"]));
        $BQ = str_replace("\x72\x65\141\x6c\155\x6e\x61\155\145", $TM, $BQ);
        $Z6["\162\x65\x61\154\x6d"] = $TM;
        Ot:
        goto LAv;
        DU:
        $dJ = stripslashes(trim($post["\155\157\137\157\141\x75\164\150\137\x70\162\x6f\x76\151\144\145\162\137\x70\x6f\154\151\x63\171"]));
        $BQ = str_replace("\x70\x6f\x6c\x69\x63\171", $dJ, $BQ);
        $Z6["\160\x6f\154\x69\143\x79"] = $dJ;
        LAv:
        $vX = null;
        if (filter_var($BQ, FILTER_VALIDATE_URL)) {
            goto yln;
        }
        $Z6["\151\x73\x5f\144\x69\x73\x63\157\166\x65\x72\171\137\x76\x61\x6c\151\144"] = "\146\x61\154\x73\x65";
        goto PAW;
        yln:
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\143\x5f\166\x61\154\151\144\137\144\x69\x73\143\157\x76\x65\x72\x79\137\x65\160", true);
        $Kg = array("\163\x73\x6c" => array("\x76\145\x72\x69\146\171\x5f\x70\145\145\162" => false, "\166\x65\x72\x69\x66\x79\137\160\x65\x65\162\x5f\x6e\141\x6d\x65" => false));
        $Bn = @file_get_contents($BQ, false, stream_context_create($Kg));
        $vX = array();
        if ($Bn) {
            goto Fdk;
        }
        $Z6["\x69\x73\x5f\x64\151\163\143\157\166\x65\x72\171\x5f\166\141\154\x69\x64"] = "\x66\141\x6c\163\145";
        goto pha;
        Fdk:
        $vX = json_decode($Bn);
        $Z6["\x69\163\x5f\144\151\163\143\157\x76\145\162\x79\x5f\x76\x61\x6c\151\144"] = "\x74\162\x75\x65";
        pha:
        $cR = isset($vX->scopes_supported[0]) ? $vX->scopes_supported[0] : '';
        $I3 = isset($vX->scopes_supported[1]) ? $vX->scopes_supported[1] : '';
        $OA = stripslashes($cR) . "\40" . stripslashes($I3);
        $Z6["\144\151\x73\x63\x6f\x76\x65\162\171"] = $BQ;
        $Z6["\163\x63\157\x70\x65"] = isset($Y1) && !empty($Y1) ? $Y1 : $OA;
        $A0 = isset($vX->authorization_endpoint) ? stripslashes($vX->authorization_endpoint) : '';
        $rv = isset($vX->token_endpoint) ? stripslashes($vX->token_endpoint) : '';
        $yZ = isset($vX->userinfo_endpoint) ? stripslashes($vX->userinfo_endpoint) : '';
        PAW:
        Pur:
        goto nz5;
        s9F:
        $mx->mo_oauth_client_update_option("\155\157\137\x6f\141\165\x74\x68\x5f\145\166\x65\157\156\x6c\x69\156\x65\137\x65\x6e\141\142\154\145", 1);
        $mx->mo_oauth_client_update_option("\155\157\x5f\157\141\x75\x74\x68\x5f\145\166\145\157\x6e\154\x69\156\145\x5f\x63\x6c\x69\x65\x6e\164\137\x69\144", $GA);
        $mx->mo_oauth_client_update_option("\155\157\137\157\141\165\x74\x68\x5f\x65\x76\x65\x6f\156\x6c\151\x6e\145\137\143\154\151\145\156\x74\x5f\163\x65\143\x72\x65\164", $AW);
        if (!($mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\x61\x75\x74\150\137\x65\166\145\157\156\x6c\x69\x6e\145\137\143\154\151\145\x6e\x74\137\x69\144") && $mx->mo_oauth_client_get_option("\155\157\x5f\x6f\x61\x75\x74\x68\137\145\166\x65\x6f\156\x6c\151\156\x65\x5f\x63\x6c\x69\145\156\164\137\x73\145\143\x72\x65\x74"))) {
            goto tJ;
        }
        $pZ = new Customer();
        $Kv = $pZ->add_oauth_application("\145\166\145\x6f\156\x6c\x69\x6e\145", "\105\126\x45\40\117\x6e\x6c\x69\x6e\145\40\117\x41\165\x74\x68");
        if ("\101\x70\x70\x6c\x69\x63\x61\164\151\x6f\x6e\x20\103\162\145\x61\164\145\144" === $Kv) {
            goto mg;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, $Kv);
        $this->mo_oauth_show_error_message();
        goto b0;
        mg:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\x75\162\40\x73\x65\164\164\x69\x6e\x67\x73\x20\x77\x65\162\x65\x20\163\141\166\145\x64\x2e\x20\107\157\x20\164\x6f\x20\x41\144\x76\141\156\143\x65\144\40\x45\126\105\40\x4f\156\154\151\156\x65\x20\x53\x65\x74\x74\x69\156\x67\163\40\146\157\x72\x20\x63\157\x6e\146\151\147\165\162\151\x6e\147\x20\x72\x65\163\164\x72\x69\143\164\x69\x6f\x6e\163\40\x6f\156\x20\x75\163\145\x72\x20\x73\x69\147\156\40\x69\x6e\56");
        $this->mo_oauth_show_success_message();
        b0:
        tJ:
        nz5:
        isset($post["\155\157\x5f\x6f\x61\165\164\x68\137\x73\143\x6f\x70\145"]) && !empty($post["\x6d\x6f\x5f\157\141\x75\164\x68\x5f\163\143\x6f\x70\x65"]) ? $Z6["\163\x63\157\x70\145"] = sanitize_text_field(wp_unslash($post["\x6d\157\x5f\157\141\x75\x74\150\x5f\163\x63\157\160\145"])) : '';
        $Z6["\165\x6e\151\161\165\x65\137\141\160\160\151\144"] = isset($post["\x6d\157\x5f\157\141\165\164\x68\x5f\x61\x70\x70\x5f\x6e\x61\x6d\145"]) ? stripslashes($post["\155\x6f\x5f\157\141\x75\164\150\137\x61\x70\x70\x5f\156\141\x6d\145"]) : '';
        $Z6["\143\154\151\x65\156\x74\137\x69\x64"] = $mx->mooauthencrypt(sanitize_text_field(wp_unslash(isset($post["\x6d\157\137\x6f\141\x75\164\150\137\x63\154\x69\x65\x6e\x74\137\x69\144"]) ? $post["\x6d\157\137\x6f\x61\x75\x74\x68\x5f\143\x6c\x69\145\x6e\164\x5f\151\144"] : '')));
        $Z6["\x63\x6c\151\145\x6e\164\137\163\x65\143\x72\x65\164"] = $mx->mooauthencrypt(wp_unslash(isset($post["\x6d\x6f\137\157\141\x75\x74\150\x5f\x63\x6c\151\x65\156\164\x5f\x73\145\143\162\145\x74"]) ? stripslashes(trim($post["\155\x6f\137\x6f\141\165\164\x68\137\x63\x6c\x69\145\x6e\x74\137\163\x65\x63\x72\x65\164"])) : ''));
        $Z6["\x63\154\x69\x65\156\164\137\x63\162\145\144\163\x5f\145\156\143\162\x70\x79\x74\x65\144"] = true;
        $Z6["\x73\x65\156\144\137\150\x65\x61\x64\x65\162\x73"] = isset($post["\x6d\157\x5f\157\141\165\x74\x68\137\141\165\x74\150\x6f\x72\x69\x7a\141\164\151\x6f\156\x5f\x68\x65\141\x64\x65\x72"]) ? (int) filter_var($post["\x6d\x6f\x5f\157\x61\x75\x74\x68\137\141\x75\164\150\157\x72\x69\172\141\x74\x69\157\x6e\x5f\x68\x65\141\x64\x65\x72"], FILTER_SANITIZE_NUMBER_INT) : 0;
        $Z6["\163\145\156\x64\137\x62\157\144\171"] = isset($post["\155\x6f\137\x6f\141\x75\164\150\137\142\157\x64\171"]) ? (int) filter_var($post["\x6d\x6f\137\157\141\x75\164\150\x5f\x62\157\144\x79"], FILTER_SANITIZE_NUMBER_INT) : 0;
        $Z6["\163\x65\156\144\137\x73\x74\141\164\x65"] = isset($_POST["\x6d\157\x5f\157\x61\x75\164\150\137\163\x74\x61\164\145"]) ? (int) filter_var($_POST["\155\x6f\x5f\x6f\141\x75\x74\150\137\163\164\x61\164\x65"], FILTER_SANITIZE_NUMBER_INT) : 0;
        $Z6["\163\145\x6e\x64\137\x6e\x6f\x6e\143\x65"] = isset($_POST["\x6d\157\137\157\x61\x75\x74\150\137\x6e\157\156\x63\x65"]) ? (int) filter_var($_POST["\x6d\157\137\157\x61\x75\x74\150\137\156\x6f\x6e\143\x65"], FILTER_SANITIZE_NUMBER_INT) : 0;
        $Z6["\x73\150\x6f\x77\137\157\156\137\x6c\x6f\147\151\x6e\137\160\x61\147\x65"] = isset($post["\155\x6f\137\x6f\x61\x75\164\x68\137\163\150\x6f\167\137\x6f\156\137\x6c\x6f\x67\151\x6e\x5f\x70\141\147\145"]) ? (int) filter_var($post["\x6d\x6f\137\x6f\141\165\164\x68\137\163\x68\157\x77\137\157\156\x5f\154\157\x67\x69\156\x5f\x70\x61\147\x65"], FILTER_SANITIZE_NUMBER_INT) : 0;
        if (!(!empty($Z6["\141\x70\x70\x5f\x74\x79\x70\145"]) && $Z6["\141\160\x70\137\x74\x79\160\145"] === "\x6f\x61\x75\164\x68\x31")) {
            goto AqK;
        }
        $Z6["\162\145\x71\x75\x65\163\164\165\x72\x6c"] = isset($post["\155\x6f\137\157\141\165\164\150\137\x72\145\161\165\145\163\164\x75\162\x6c"]) ? stripslashes($post["\x6d\157\x5f\x6f\x61\x75\x74\x68\137\162\145\x71\x75\145\x73\x74\165\162\154"]) : '';
        AqK:
        if (isset($Z6["\141\160\160\x49\x64"])) {
            goto QTM;
        }
        $Z6["\141\x70\160\111\x64"] = $bj;
        QTM:
        $Z6["\162\x65\x64\x69\162\x65\143\164\137\165\162\x69"] = sanitize_text_field(wp_unslash(isset($post["\155\157\x5f\165\x70\144\x61\x74\145\137\x75\x72\x6c"]) ? $post["\x6d\x6f\x5f\x75\160\144\x61\164\145\x5f\165\162\x6c"] : site_url()));
        $Z6["\x61\x75\x74\150\x6f\x72\151\172\x65\165\x72\154"] = $A0;
        $Z6["\x61\143\143\145\163\163\x74\x6f\x6b\145\x6e\x75\162\154"] = $rv;
        $Z6["\x61\160\x70\137\164\x79\x70\145"] = isset($post["\x6d\157\137\157\141\165\164\150\137\141\160\160\x5f\164\171\160\145"]) ? stripslashes($post["\155\x6f\137\x6f\141\165\x74\150\137\141\160\160\x5f\x74\x79\160\x65"]) : stripslashes("\157\x61\165\x74\x68");
        if (!($Z6["\x61\x70\x70\x5f\x74\171\160\145"] == "\157\x61\165\164\x68" || $Z6["\141\x70\160\137\164\171\160\x65"] == "\x6f\x61\165\164\150\x31" || isset($post["\155\157\x5f\157\x61\165\164\x68\x5f\162\145\163\157\165\x72\143\145\x6f\167\156\145\x72\144\x65\x74\141\x69\x6c\x73\x75\162\x6c"]))) {
            goto zuJ;
        }
        $Z6["\162\x65\163\x6f\165\162\x63\x65\157\167\156\145\162\x64\145\164\x61\151\x6c\163\165\x72\154"] = $yZ;
        zuJ:
        return $Z6;
    }
    public function change_attribute_mapping($post, $Z6)
    {
        $Ui = stripslashes($post["\x6d\x6f\137\157\141\165\x74\150\137\x75\163\145\162\x6e\141\155\x65\x5f\141\164\164\162"]);
        $Z6["\165\x73\145\x72\156\141\155\x65\137\x61\164\164\x72"] = $Ui;
        return $Z6;
    }
}
