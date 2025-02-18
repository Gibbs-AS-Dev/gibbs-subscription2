<?php


namespace MoOauthClient\Standard;

use MoOauthClient\Free\FreeSettings;
use MoOauthClient\Free\CustomizationSettings;
use MoOauthClient\Standard\AppSettings;
use MoOauthClient\Standard\SignInSettingsSettings;
use MoOauthClient\Standard\Customer;
use MoOauthClient\App;
use MoOauthClient\Config;
use MoOauthClient\Widget\MOUtils;
class StandardSettings
{
    private $free_settings;
    public function __construct()
    {
        add_filter("\143\x72\157\156\x5f\163\143\x68\x65\144\165\x6c\145\163", array($this, "\155\x6f\x5f\x6f\141\165\x74\x68\x5f\x73\x63\150\145\144\x75\x6c\145"));
        if (wp_next_scheduled("\155\x6f\137\157\141\x75\164\150\x5f\163\x63\x68\145\x64\x75\x6c\145")) {
            goto lYD;
        }
        wp_schedule_event(time(), "\x65\166\145\x72\x79\x5f\x6e\137\x6d\x69\x6e\165\164\x65\163", "\x6d\157\137\x6f\141\165\164\x68\x5f\x73\x63\150\145\x64\x75\x6c\x65");
        lYD:
        add_action("\x6d\157\137\x6f\141\x75\x74\150\137\x73\143\150\145\144\165\154\145", array($this, "\145\x76\x65\x72\171\137\163\x65\x76\145\x6e\137\144\141\x79\163\137\145\166\x65\x6e\x74\x5f\x66\x75\x6e\143"));
        $this->free_settings = new FreeSettings();
        add_action("\x61\144\x6d\151\x6e\x5f\151\156\x69\x74", array($this, "\x6d\x6f\x5f\x6f\x61\x75\x74\150\x5f\143\154\x69\x65\x6e\164\x5f\x73\164\x61\x6e\144\x61\162\144\137\163\145\164\164\x69\x6e\x67\163"));
        add_action("\144\x6f\x5f\x6d\141\151\x6e\x5f\x73\145\x74\164\151\156\147\x73\137\x69\x6e\x74\145\x72\156\x61\x6c", array($this, "\144\x6f\x5f\x69\156\x74\145\162\x6e\141\154\x5f\163\145\164\x74\x69\156\x67\163"), 1, 10);
    }
    public function mo_oauth_schedule($DD)
    {
        $DD["\x65\x76\x65\x72\171\x5f\x6e\x5f\155\151\156\x75\164\145\x73"] = array("\151\x6e\164\145\x72\166\141\x6c" => 60 * 60 * 24 * 7, "\144\x69\163\x70\x6c\141\171" => __("\105\x76\145\162\x79\x20\156\x20\x4d\x69\156\165\164\145\163", "\164\145\170\164\144\157\155\141\x69\x6e"));
        return $DD;
    }
    public function every_seven_days_event_func()
    {
        global $mx;
        $pZ = new Customer();
        $Bn = $pZ->check_customer_ln();
        $Bn = json_decode($Bn, true);
        $this->mo_oauth_initiate_expiration($Bn);
    }
    public function mo_oauth_initiate_expiration($Bn)
    {
        global $mx;
        $GP = "\x64\x69\x73\141\x62\154\x65\x64";
        $YD = new SignInSettingsSettings();
        $n2 = $YD->get_config_option();
        if (!isset($Bn["\154\151\143\x65\x6e\x73\145\105\x78\x70\x69\162\x79"])) {
            goto vjb;
        }
        $Q6 = $Bn["\x6c\x69\x63\145\156\163\145\105\x78\x70\151\162\171"];
        $vg = false;
        $RE = date("\131\55\x6d\x2d\x64\x20\x48\72\x69\x3a\x73");
        $Q6 <= $RE ? $vg = "\x65\156\141\x62\x6c\x65\x64" : ($vg = "\144\x69\x73\x61\142\154\x65\144");
        $n2->add_config("\155\157\x5f\144\164\x65\137\x73\x74\x61\x74\145", $mx->mooauthencrypt($vg));
        $n2->add_config("\155\157\x5f\x64\164\145\x5f\x64\141\x74\x61", $mx->mooauthencrypt($Q6));
        $YD->save_config_option($n2);
        vjb:
    }
    public function mo_oauth_client_standard_settings()
    {
        $X6 = new CustomizationSettings();
        $YD = new SignInSettingsSettings();
        $tN = new AppSettings();
        $X6->save_customization_settings();
        $tN->save_app_settings();
        $YD->mo_oauth_save_settings();
    }
    public function do_internal_settings($post)
    {
        global $mx;
        if (!(isset($_POST["\155\x6f\x5f\x6f\x61\165\x74\150\137\x63\154\x69\145\x6e\164\x5f\x76\x65\162\151\146\171\137\154\x69\x63\x65\156\163\145\137\x6e\157\x6e\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\157\x61\165\x74\150\137\143\154\x69\x65\x6e\x74\x5f\166\145\162\151\146\171\137\154\151\143\145\x6e\x73\145\137\x6e\157\156\143\145"])), "\x6d\157\x5f\157\141\x75\164\150\x5f\x63\x6c\151\x65\x6e\164\137\166\x65\x72\151\146\171\137\x6c\x69\x63\x65\156\163\145") && isset($post[\MoOAuthConstants::OPTION]) && "\x6d\157\x5f\157\141\x75\164\x68\x5f\143\x6c\151\145\156\x74\137\166\145\x72\151\146\x79\137\x6c\151\x63\145\x6e\x73\145" === $post[\MoOAuthConstants::OPTION])) {
            goto Bhz;
        }
        if (!(!isset($post["\155\157\137\x6f\141\165\164\x68\137\x63\154\x69\145\x6e\164\x5f\x6c\151\143\145\156\x73\145\x5f\153\x65\x79"]) || empty($post["\x6d\x6f\x5f\157\x61\x75\x74\x68\137\143\x6c\151\x65\156\164\137\x6c\151\143\145\x6e\163\x65\x5f\153\x65\x79"]))) {
            goto Ihd;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\x6c\145\x61\163\145\x20\145\x6e\164\x65\162\40\166\x61\154\151\144\x20\154\x69\143\x65\x6e\x73\145\x20\x6b\x65\171\56");
        $this->mo_oauth_show_error_message();
        return;
        Ihd:
        $It = trim($post["\155\157\137\x6f\141\x75\x74\150\137\143\x6c\151\145\x6e\x74\137\x6c\151\x63\145\156\163\145\x5f\x6b\x65\171"]);
        $pZ = new Customer();
        $Bn = json_decode($pZ->check_customer_ln(), true);
        $Uk = false;
        if (!(isset($Bn["\x69\x73\x4d\x75\154\164\151\x53\x69\164\x65\120\154\165\x67\151\156\x52\145\x71\165\145\163\x74\145\144"]) && boolval($Bn["\151\163\115\165\154\164\x69\123\x69\x74\x65\120\x6c\x75\147\x69\x6e\x52\x65\x71\x75\145\163\x74\x65\x64"]) && is_multisite())) {
            goto Cjz;
        }
        $Uk = boolval($Bn["\x69\163\115\165\154\164\x69\123\151\164\145\120\x6c\x75\x67\x69\156\122\x65\x71\x75\x65\x73\164\x65\144"]);
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\x61\165\164\x68\x5f\151\x73\x4d\165\154\x74\151\123\151\164\145\x50\x6c\x75\x67\x69\x6e\x52\145\161\x75\x65\163\164\145\x64", $Uk);
        $mx->mo_oauth_client_update_option("\156\157\x4f\x66\x53\165\x62\123\151\164\145\x73", intval($Bn["\x6e\x6f\117\146\x53\x75\142\x53\x69\164\145\x73"]));
        Cjz:
        $Zc = 0;
        if (!is_multisite()) {
            goto OXD;
        }
        if (!function_exists("\147\x65\x74\x5f\163\x69\164\x65\x73")) {
            goto YkY;
        }
        $Zc = count(get_sites(["\x6e\x75\x6d\142\145\x72" => 1000])) - 1;
        YkY:
        OXD:
        if (!(is_multisite() && $Uk && ($Uk && (!array_key_exists("\156\157\117\146\x53\x75\x62\x53\151\x74\145\163", $Bn) && $mx->is_multisite_versi())))) {
            goto Tem;
        }
        $sR = $mx->mo_oauth_client_get_option("\x68\157\x73\x74\x5f\x6e\141\x6d\145");
        $sR .= "\x2f\155\157\x61\x73\57\154\x6f\x67\x69\156\77\162\145\x64\x69\162\x65\143\x74\x55\x72\154\75";
        $sR .= $mx->mo_oauth_client_get_option("\150\157\163\164\137\156\x61\155\x65");
        $sR .= "\57\x6d\x6f\141\x73\x2f\151\x6e\151\x74\x69\141\x6c\151\x7a\145\160\141\x79\155\145\x6e\x74\x3f\162\x65\x71\x75\x65\163\x74\x4f\x72\x69\x67\x69\156\x3d";
        $sR .= "\x77\160\137\x6f\x61\165\x74\x68\x5f\x63\x6c\151\x65\x6e\164\x5f" . strtolower($mx->get_versi_str()) . "\x5f\160\x6c\x61\156";
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\x6f\165\x20\x68\141\166\x65\40\x6e\157\164\x20\165\x70\x67\162\141\x64\x65\144\x20\164\157\40\x74\x68\x65\40\x63\x6f\162\x72\x65\x63\x74\40\x6c\151\x63\145\156\x73\x65\40\160\154\141\x6e\x2e\40\105\x69\164\x68\x65\162\40\171\x6f\165\x20\x68\x61\166\145\x20\x70\x75\x72\x63\x68\x61\x73\145\x64\40\x66\157\x72\40\x69\x6e\x63\x6f\162\x72\145\143\x74\40\x6e\157\56\x20\157\146\40\x73\151\164\145\163\40\x6f\x72\40\171\157\165\x20\150\x61\x76\x65\x20\x6e\157\164\x20\x73\x65\154\x65\x63\x74\x65\144\x20\155\x75\x6c\x74\x69\x73\151\x74\145\40\157\160\x74\151\157\x6e\40\x77\150\151\x6c\145\40\160\x75\162\143\x68\x61\163\151\156\147\x2e\40\x3c\141\x20\x74\x61\162\x67\x65\164\x3d\42\137\142\154\141\x6e\x6b\x22\x20\x68\162\x65\146\x3d\x22" . $sR . "\42\40\x3e\x43\154\x69\x63\x6b\40\150\x65\162\x65\74\57\x61\x3e\40\x74\157\x20\x75\x70\147\162\141\144\145\40\164\x6f\40\x70\162\145\x6d\151\x75\155\x20\x76\145\x72\x73\151\157\156\56");
        $mx->mo_oauth_show_error_message();
        return;
        Tem:
        if (strcasecmp($Bn["\x73\164\x61\x74\x75\163"], "\x53\125\x43\103\105\123\x53") === 0) {
            goto hY8;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\x6f\165\x20\x68\141\166\145\156\47\x74\x20\165\160\147\162\x61\x64\x65\x64\40\164\x6f\40\x74\150\151\x73\x20\160\x6c\141\156\x20\x79\x65\164\56");
        $mx->mo_oauth_show_error_message();
        goto rV4;
        hY8:
        $n5 = $Bn;
        $Bn = json_decode($pZ->XfskodsfhHJ($It), true);
        if (isset($Bn)) {
            goto pQR;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\154\145\x61\163\145\x20\143\x68\145\x63\x6b\40\151\x66\40\x79\x6f\165\x20\150\x61\x76\145\x20\x65\x6e\164\x65\x72\x65\144\40\x61\40\x76\141\x6c\x69\x64\40\154\151\x63\x65\x6e\x73\145\x20\153\x65\171");
        $mx->mo_oauth_show_error_message();
        goto Lg8;
        pQR:
        if (strcasecmp($Bn["\x73\x74\141\x74\x75\163"], "\123\125\103\103\x45\x53\x53") === 0) {
            goto c1Z;
        }
        if (strcasecmp($Bn["\x73\164\141\164\165\163"], "\x46\x41\111\x4c\105\104") === 0) {
            goto Cst;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x41\x6e\40\x65\x72\162\157\x72\40\157\x63\x63\x75\162\x65\144\40\x77\x68\x69\154\x65\x20\160\162\157\x63\145\x73\163\151\156\147\x20\171\x6f\165\x72\40\x72\145\161\x75\145\163\164\56\x20\x50\x6c\145\x61\x73\x65\40\124\162\171\x20\x61\x67\x61\x69\x6e\x2e");
        $mx->mo_oauth_show_error_message();
        goto l3M;
        c1Z:
        $mx->mo_oauth_client_update_option("\155\157\137\x6f\x61\165\x74\150\137\154\153", $mx->mooauthencrypt($It));
        $mx->mo_oauth_client_update_option("\155\x6f\137\x6f\141\165\x74\x68\x5f\x6c\x76", $mx->mooauthencrypt("\x74\x72\165\145"));
        $this->mo_oauth_initiate_expiration($n5);
        $FO = $mx->get_app_list();
        if (!(!empty($FO) && is_array($FO))) {
            goto v2E;
        }
        foreach ($FO as $bj => $t4) {
            if (is_array($t4) && !empty($t4)) {
                goto Tzg;
            }
            if (boolval($t4->get_app_config("\x63\x6c\151\145\x6e\x74\x5f\x63\162\145\x64\x73\137\145\156\143\162\x70\x79\164\x65\144"))) {
                goto ZFw;
            }
            $Sk = $t4->get_app_config("\143\x6c\x69\x65\156\x74\x5f\151\x64");
            !empty($Sk) ? $t4->update_app_config("\x63\x6c\151\145\x6e\164\x5f\x69\x64", $mx->mooauthencrypt($Sk)) : '';
            $xR = $t4->get_app_config("\x63\x6c\151\145\156\x74\x5f\x73\145\x63\162\145\164");
            !empty($xR) ? $t4->update_app_config("\143\154\151\x65\156\x74\x5f\x73\145\x63\x72\145\164", $mx->mooauthencrypt($xR)) : '';
            $t4->update_app_config("\x63\154\x69\x65\x6e\x74\x5f\x63\x72\145\144\163\137\145\x6e\x63\162\160\x79\x74\x65\144", true);
            ZFw:
            $W2[$bj] = $t4;
            goto gMC;
            Tzg:
            if (!(!isset($t4["\143\154\x69\145\x6e\x74\x5f\x69\144"]) || empty($t4["\143\154\151\145\156\x74\x5f\151\144"]))) {
                goto QI4;
            }
            $t4["\x63\154\151\x65\156\x74\x5f\x69\x64"] = isset($t4["\x63\154\x69\x65\x6e\164\x69\x64"]) ? $t4["\143\x6c\x69\145\156\164\x69\x64"] : '';
            QI4:
            if (!(!isset($t4["\x63\154\x69\145\156\164\137\163\x65\x63\x72\x65\x74"]) || empty($t4["\x63\154\x69\145\156\x74\x5f\163\145\x63\162\145\x74"]))) {
                goto Ig5;
            }
            $t4["\143\154\151\145\x6e\x74\137\163\x65\143\x72\145\x74"] = isset($t4["\143\154\x69\145\x6e\x74\163\145\x63\162\145\x74"]) ? $t4["\143\154\x69\145\156\164\163\x65\x63\162\145\x74"] : '';
            Ig5:
            unset($t4["\143\154\151\145\156\164\x69\x64"]);
            unset($t4["\x63\x6c\x69\145\156\x74\163\145\x63\x72\145\164"]);
            if (!(!isset($t4["\143\154\x69\145\x6e\x74\137\143\162\x65\144\163\137\145\x6e\x63\x72\160\x79\x74\x65\x64"]) || !boolval($t4["\143\154\151\x65\x6e\164\x5f\143\x72\145\144\x73\137\x65\x6e\x63\162\x70\171\x74\145\x64"]))) {
                goto XX1;
            }
            isset($t4["\x63\x6c\x69\x65\x6e\x74\x5f\x69\144"]) ? $t4["\143\154\151\x65\156\164\137\151\x64"] = $mx->mooauthencrypt($t4["\x63\154\151\x65\x6e\x74\x5f\x69\144"]) : '';
            isset($t4["\x63\x6c\151\x65\x6e\x74\x5f\x73\145\x63\162\145\164"]) ? $t4["\143\154\x69\145\156\x74\x5f\x73\145\x63\162\145\x74"] = $mx->mooauthencrypt($t4["\x63\154\x69\145\156\x74\137\163\x65\x63\x72\x65\x74"]) : '';
            $t4["\x63\154\151\145\x6e\164\x5f\143\162\x65\x64\x73\x5f\145\x6e\x63\x72\x70\171\x74\x65\x64"] = true;
            XX1:
            $Zy = new App();
            $Zy->migrate_app($t4, $bj);
            $W2[$bj] = $Zy;
            gMC:
            Vqr:
        }
        bu3:
        v2E:
        !empty($FO) ? $mx->mo_oauth_client_update_option("\x6d\157\x5f\157\x61\165\x74\150\x5f\141\x70\x70\163\137\x6c\151\x73\x74", $W2) : '';
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\x6f\x75\x72\40\x6c\151\x63\x65\x6e\163\145\40\x69\x73\40\166\x65\162\x69\x66\151\145\144\56\40\131\x6f\165\x20\x63\141\x6e\x20\x6e\x6f\x77\40\163\145\164\x75\x70\x20\164\x68\145\40\160\154\165\147\151\156\x2e");
        $mx->mo_oauth_show_success_message();
        goto l3M;
        Cst:
        if (strcasecmp($Bn["\x6d\145\x73\x73\x61\147\x65"], "\103\157\x64\145\40\150\141\x73\40\x45\170\x70\151\x72\145\144") === 0) {
            goto msq;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\x75\40\x68\141\x76\x65\x20\145\x6e\164\x65\162\145\x64\x20\141\156\40\151\156\x76\x61\154\x69\x64\40\154\x69\x63\145\x6e\x73\145\x20\x6b\x65\171\x2e\40\x50\x6c\145\141\x73\x65\x20\145\156\164\145\162\x20\141\x20\166\141\154\x69\x64\40\x6c\151\x63\x65\156\x73\145\x20\x6b\x65\x79\x2e");
        $mx->mo_oauth_show_error_message();
        goto N53;
        msq:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\114\x69\143\145\156\x73\x65\x20\x6b\145\171\40\171\157\x75\40\150\x61\x76\x65\x20\x65\x6e\164\x65\162\145\x64\40\x68\x61\x73\x20\x61\x6c\x72\x65\x61\x64\171\x20\x62\145\x65\156\40\x75\x73\x65\x64\x2e\40\x50\x6c\145\141\163\145\x20\145\x6e\164\x65\162\40\141\x20\153\145\171\40\x77\150\151\x63\150\x20\150\141\163\x20\156\157\x74\40\142\x65\145\x6e\x20\165\163\x65\x64\x20\x62\145\x66\157\162\x65\40\x6f\156\40\x61\156\x79\40\157\x74\150\x65\x72\40\x69\x6e\x73\x74\141\x6e\143\145\x20\x6f\162\x20\x69\x66\40\x79\157\165\x20\150\141\x76\x65\x20\x65\170\141\165\x73\164\x65\144\40\x61\x6c\154\x20\171\x6f\x75\162\40\153\x65\x79\x73\x20\x74\150\x65\x6e\40\142\x75\x79\40\155\x6f\x72\145\56");
        $mx->mo_oauth_show_error_message();
        N53:
        l3M:
        Lg8:
        rV4:
        Bhz:
    }
}
