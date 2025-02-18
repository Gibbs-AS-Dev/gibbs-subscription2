<?php


namespace MoOauthClient\Premium;

use MoOauthClient\App;
use MoOauthClient\Standard\AppSettings as StandardAppSettings;
class AppSettings extends StandardAppSettings
{
    public function __construct()
    {
        parent::__construct();
        add_action("\155\x6f\x5f\157\x61\x75\164\150\137\x63\154\151\145\x6e\x74\x5f\163\141\x76\x65\x5f\x61\160\160\x5f\x73\x65\x74\164\151\x6e\147\x73\x5f\x69\x6e\164\145\162\x6e\x61\x6c", array($this, "\163\x61\166\145\137\x72\157\154\145\137\x6d\141\x70\160\x69\x6e\x67"));
    }
    public function change_app_settings($post, $Z6)
    {
        global $mx;
        $Z6 = parent::change_app_settings($post, $Z6);
        $Z6["\147\162\x6f\165\x70\144\x65\164\141\151\x6c\x73\165\x72\x6c"] = isset($post["\x6d\x6f\137\157\141\x75\x74\x68\137\x67\162\x6f\165\160\x64\145\x74\141\x69\x6c\163\x75\162\154"]) ? trim(stripslashes($post["\155\157\x5f\157\x61\165\x74\150\137\x67\x72\157\165\160\144\145\164\141\x69\x6c\x73\x75\x72\154"])) : '';
        $Z6["\x6a\167\x6b\x73\165\162\x6c"] = isset($post["\155\x6f\137\157\141\165\x74\150\137\152\x77\x6b\163\165\x72\x6c"]) ? trim(stripslashes($post["\x6d\x6f\x5f\x6f\x61\x75\x74\150\x5f\152\167\153\163\165\162\154"])) : '';
        $Z6["\x67\x72\x61\156\x74\x5f\164\x79\x70\x65"] = isset($post["\147\x72\141\x6e\164\137\x74\171\160\x65"]) ? stripslashes($post["\x67\162\141\x6e\164\137\164\171\160\x65"]) : "\x41\165\x74\x68\157\162\151\x7a\141\x74\151\157\x6e\40\103\x6f\x64\145\x20\107\x72\141\156\164";
        if (isset($post["\x65\156\141\x62\154\145\137\157\141\x75\x74\x68\137\167\160\x5f\154\x6f\x67\151\156"]) && "\x6f\156" === $post["\145\156\141\142\x6c\145\x5f\157\141\165\164\150\x5f\167\160\x5f\x6c\157\147\151\x6e"]) {
            goto TRw;
        }
        $mx->mo_oauth_client_delete_option("\155\157\137\157\141\165\164\x68\x5f\x65\x6e\141\142\x6c\145\x5f\x6f\141\165\164\150\137\x77\x70\137\x6c\x6f\x67\151\156");
        goto L9d;
        TRw:
        $mx->mo_oauth_client_update_option("\155\157\x5f\x6f\x61\165\x74\x68\137\145\156\x61\x62\154\x65\137\157\x61\x75\164\150\137\x77\160\x5f\x6c\x6f\x67\151\156", $_GET["\141\160\x70"]);
        L9d:
        return $Z6;
    }
    public function save_advanced_grant_settings()
    {
        if (!(!isset($_POST["\155\157\137\157\x61\165\164\150\137\147\162\141\156\x74\137\163\x65\164\x74\x69\x6e\x67\x73\x5f\156\x6f\x6e\x63\x65"]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\137\157\141\x75\x74\x68\137\x67\162\141\156\x74\x5f\163\x65\164\x74\x69\156\x67\x73\x5f\156\157\x6e\143\x65"])), "\x6d\x6f\137\x6f\141\x75\x74\x68\x5f\x67\x72\x61\156\164\x5f\163\x65\x74\164\151\x6e\147\163"))) {
            goto Xiw;
        }
        return;
        Xiw:
        $post = $_POST;
        if (!(!isset($post[\MoOAuthConstants::OPTION]) || "\x6d\x6f\x5f\x6f\141\165\x74\x68\x5f\147\162\x61\x6e\x74\x5f\x73\145\x74\x74\x69\156\147\x73" !== $post[\MoOAuthConstants::OPTION])) {
            goto S3K;
        }
        return;
        S3K:
        if (!(!isset($post[\MoOAuthConstants::POST_APP_NAME]) || empty($post[\MoOAuthConstants::POST_APP_NAME]))) {
            goto OgY;
        }
        return;
        OgY:
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\x69\163\x61\142\154\145\x64";
        if (empty($n2["\155\x6f\137\144\164\x65\x5f\163\164\141\164\x65"])) {
            goto nKk;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\x6f\137\144\x74\145\137\x73\x74\x61\164\145"]);
        nKk:
        if (!($Wz == "\x64\x69\163\141\x62\x6c\x65\x64")) {
            goto amT;
        }
        $pY = $post[\MoOAuthConstants::POST_APP_NAME];
        $Z6 = $mx->get_app_by_name($pY);
        $Z6 = $Z6->get_app_config('', false);
        $Z6 = $this->save_grant_settings($post, $Z6);
        $mx->set_app_by_name($pY, $Z6);
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\157\165\x72\40\x53\145\x74\x74\151\156\x67\163\40\150\x61\166\145\x20\x62\145\x65\x6e\40\163\x61\x76\x65\144\x20\163\165\143\143\145\163\x73\146\165\x6c\x6c\x79\x2e");
        $mx->mo_oauth_show_success_message();
        wp_safe_redirect("\x61\144\155\x69\156\56\x70\x68\160\x3f\160\x61\147\x65\75\155\157\x5f\157\x61\x75\164\150\x5f\x73\x65\164\164\x69\156\x67\x73\46\x61\143\x74\151\157\156\x3d\165\x70\144\x61\164\145\46\141\x70\160\x3d" . rawurlencode($pY));
        amT:
    }
    public function save_grant_settings($post, $Z6)
    {
        global $mx;
        $Z6["\155\157\x5f\157\x61\165\x74\x68\137\162\x65\x73\x70\157\156\163\145\137\x74\171\x70\145"] = isset($post["\155\x6f\x5f\x6f\141\x75\164\150\137\162\x65\163\160\157\x6e\163\x65\x5f\x74\171\160\x65"]) ? stripslashes($post["\x6d\x6f\x5f\x6f\x61\165\x74\150\x5f\162\145\163\x70\x6f\156\163\x65\137\164\x79\x70\x65"]) : '';
        $Z6["\x6a\x77\164\137\163\x75\x70\160\157\x72\x74"] = isset($post["\152\x77\x74\137\163\165\x70\160\157\162\x74"]) ? 1 : 0;
        $Z6["\x6a\167\164\x5f\141\x6c\147\157"] = isset($post["\x6a\x77\164\137\141\154\147\x6f"]) ? stripslashes($post["\x6a\x77\x74\137\x61\x6c\147\157"]) : "\x48\123\x41";
        if ("\122\x53\x41" === $Z6["\152\x77\164\137\x61\x6c\147\157"]) {
            goto IL9;
        }
        if (!isset($Z6["\x78\x35\x30\71\137\x63\x65\162\x74"])) {
            goto MtN;
        }
        unset($Z6["\170\65\x30\x39\137\x63\145\162\x74"]);
        MtN:
        goto BRN;
        IL9:
        $Z6["\x78\65\60\x39\137\143\145\162\164"] = isset($post["\x6d\157\x5f\157\x61\x75\x74\150\x5f\170\65\60\71\x5f\143\145\162\x74"]) ? stripslashes($post["\155\157\137\157\x61\165\x74\150\137\x78\65\60\x39\x5f\143\145\x72\x74"]) : '';
        BRN:
        return $Z6;
    }
    public function change_attribute_mapping($post, $Z6)
    {
        $Z6 = parent::change_attribute_mapping($post, $Z6);
        $v_ = array();
        $wz = 0;
        foreach ($post as $NZ => $mB) {
            if (!(strpos($NZ, "\155\157\x5f\x6f\141\165\x74\150\137\143\154\151\x65\x6e\x74\137\x63\x75\163\x74\157\x6d\x5f\141\164\164\x72\151\142\x75\164\x65\137\153\145\x79") !== false && !empty($post[$NZ]))) {
                goto Cah;
            }
            $ce = strrpos($NZ, "\x5f", -1);
            $wz = substr($NZ, $ce + 1);
            $uJ = "\155\157\137\157\x61\165\x74\x68\x5f\x63\x6c\151\145\x6e\164\x5f\143\x75\x73\x74\157\155\x5f\141\x74\x74\x72\x69\x62\x75\x74\145\137\166\141\x6c\165\145\137" . $wz;
            if (!($post[$uJ] == '')) {
                goto W4Y;
            }
            goto WEp;
            W4Y:
            $v_[$mB] = $post[$uJ];
            Cah:
            WEp:
        }
        uUx:
        $Z6["\143\165\163\164\x6f\x6d\x5f\141\x74\x74\x72\x73\137\x6d\x61\160\x70\x69\156\x67"] = $v_;
        return $Z6;
    }
    public function save_role_mapping()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\x69\x73\x61\x62\154\145\x64";
        if (empty($n2["\155\157\x5f\144\x74\x65\137\163\x74\x61\164\x65"])) {
            goto zqd;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\x6f\137\144\164\x65\137\x73\164\141\x74\145"]);
        zqd:
        if (!($Wz == "\144\151\x73\x61\142\x6c\x65\x64")) {
            goto MUC;
        }
        if (!(isset($_POST["\155\x6f\x5f\157\x61\165\164\x68\137\x63\x6c\x69\x65\x6e\x74\137\163\x61\166\145\137\x72\x6f\x6c\145\137\155\141\x70\160\151\156\x67\x5f\156\157\x6e\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\157\x61\165\164\x68\x5f\x63\x6c\151\145\156\x74\137\163\141\x76\145\137\x72\157\x6c\145\x5f\x6d\141\x70\x70\x69\156\x67\x5f\156\x6f\x6e\143\x65"])), "\155\157\137\157\141\165\x74\150\x5f\143\154\x69\x65\156\x74\137\163\141\166\x65\x5f\x72\x6f\x6c\x65\137\x6d\141\160\160\151\156\x67") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\157\137\157\x61\165\164\x68\137\x63\154\151\145\x6e\x74\137\163\x61\x76\145\137\162\157\x6c\145\137\x6d\141\x70\160\151\x6e\x67" === $_POST[\MoOAuthConstants::OPTION])) {
            goto Os0;
        }
        $bj = sanitize_text_field(wp_unslash(isset($_POST[\MoOAuthConstants::POST_APP_NAME]) ? $_POST[\MoOAuthConstants::POST_APP_NAME] : ''));
        $Zy = $mx->get_app_by_name($bj);
        $xA = $Zy->get_app_config('', false);
        $xA["\x72\x65\x73\164\x72\151\x63\164\137\154\x6f\147\151\x6e\137\146\x6f\162\137\x6d\141\160\160\145\x64\x5f\162\157\154\x65\163"] = isset($_POST["\162\145\x73\x74\162\x69\143\164\137\154\x6f\147\151\156\137\x66\157\x72\x5f\155\141\x70\x70\145\x64\137\x72\x6f\x6c\145\x73"]) ? sanitize_text_field(wp_unslash($_POST["\162\145\163\x74\162\x69\x63\164\137\x6c\x6f\147\x69\156\137\146\x6f\x72\137\155\x61\x70\x70\x65\x64\137\162\157\x6c\x65\x73"])) : false;
        $xA["\145\x78\164\x72\141\143\164\x5f\x65\x6d\x61\x69\x6c\137\144\x6f\155\141\151\x6e\137\146\157\162\137\x72\x6f\154\145\155\x61\160\x70\151\156\147"] = isset($_POST["\x65\170\164\x72\141\x63\x74\x5f\145\x6d\141\151\154\137\144\x6f\155\x61\x69\x6e\x5f\x66\x6f\x72\x5f\162\157\154\145\x6d\x61\x70\160\x69\x6e\147"]) ? sanitize_text_field(wp_unslash($_POST["\145\170\x74\162\141\143\x74\137\x65\x6d\141\x69\x6c\x5f\144\x6f\x6d\141\151\156\x5f\x66\x6f\162\137\162\157\x6c\145\155\x61\x70\x70\151\x6e\147"])) : false;
        $xA["\147\x72\157\165\x70\x6e\x61\155\x65\x5f\x61\x74\164\x72\x69\142\x75\164\x65"] = isset($_POST["\155\x61\x70\x70\151\x6e\147\x5f\147\162\x6f\165\160\x6e\141\x6d\145\137\141\x74\164\x72\x69\142\x75\164\x65"]) ? trim(stripslashes($_POST["\x6d\141\x70\160\151\x6e\147\137\x67\162\157\x75\160\x6e\141\x6d\145\137\x61\164\164\162\151\142\165\164\x65"])) : '';
        $tb = 100;
        $bi = 0;
        $RX = [];
        if (!isset($_POST["\155\141\x70\x70\x69\156\x67\137\153\145\x79\x5f"])) {
            goto XBS;
        }
        $RX = array_map("\x73\141\156\151\x74\151\172\x65\x5f\164\x65\170\x74\137\x66\151\x65\154\144", wp_unslash($_POST["\155\x61\160\160\x69\156\x67\137\x6b\145\171\137"]));
        XBS:
        $vz = count($RX);
        $el = 1;
        $lB = 1;
        zIy:
        if (!($lB <= $vz)) {
            goto izs;
        }
        if (isset($_POST["\155\141\x70\160\151\x6e\x67\x5f\x6b\x65\x79\x5f"][$el])) {
            goto Wyj;
        }
        YZF:
        if (!($el < 100)) {
            goto oOl;
        }
        if (!isset($_POST["\x6d\x61\160\x70\x69\x6e\147\137\153\145\x79\x5f"][$el])) {
            goto YOn;
        }
        if (!('' === $_POST["\x6d\x61\x70\x70\x69\156\147\137\x6b\145\171\x5f"][$el]["\x76\141\x6c\165\145"])) {
            goto DBC;
        }
        $el++;
        goto YZF;
        DBC:
        $xA["\x5f\x6d\x61\x70\160\x69\156\147\137\153\x65\171\137" . $lB] = sanitize_text_field(wp_unslash(isset($_POST["\x6d\141\x70\160\151\156\147\x5f\x6b\145\171\137"][$el]) ? $_POST["\155\x61\x70\160\x69\156\x67\x5f\x6b\x65\x79\x5f"][$el]["\166\x61\x6c\x75\145"] : ''));
        $xA["\137\155\x61\x70\160\x69\x6e\147\x5f\x76\x61\154\165\145\137" . $lB] = sanitize_text_field(wp_unslash(isset($_POST["\x6d\x61\x70\160\151\x6e\147\x5f\153\145\x79\137"][$el]) ? $_POST["\x6d\141\160\160\x69\156\147\x5f\153\145\x79\x5f"][$el]["\162\157\x6c\x65"] : ''));
        $bi++;
        $el++;
        goto oOl;
        YOn:
        $el++;
        goto YZF;
        oOl:
        goto ZEH;
        Wyj:
        if (!('' === $_POST["\155\x61\160\160\x69\156\x67\x5f\153\145\x79\137"][$el]["\166\x61\154\x75\x65"])) {
            goto Uld;
        }
        $el++;
        goto n6p;
        Uld:
        $xA["\x5f\x6d\141\160\160\151\x6e\x67\x5f\x6b\145\x79\137" . $lB] = sanitize_text_field(wp_unslash(isset($_POST["\x6d\141\x70\160\151\156\x67\x5f\153\145\x79\137"][$el]) ? $_POST["\x6d\141\x70\160\x69\156\147\x5f\153\x65\171\x5f"][$el]["\x76\x61\x6c\x75\145"] : ''));
        $xA["\x5f\155\x61\x70\x70\151\x6e\x67\x5f\166\141\154\165\145\x5f" . $lB] = sanitize_text_field(wp_unslash(isset($_POST["\155\x61\x70\x70\151\x6e\x67\137\x6b\145\x79\137"][$el]) ? $_POST["\155\x61\160\x70\151\x6e\x67\137\153\145\x79\137"][$el]["\x72\157\154\145"] : ''));
        $el++;
        $bi++;
        ZEH:
        n6p:
        $lB++;
        goto zIy;
        izs:
        $xA["\162\157\154\x65\x5f\x6d\141\x70\x70\151\x6e\x67\x5f\x63\x6f\x75\156\x74"] = $bi;
        $k9 = $mx->set_app_by_name($bj, $xA);
        if (!$k9) {
            goto kff;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\157\165\x72\x20\x73\x65\164\x74\151\156\147\163\x20\141\162\x65\x20\163\141\166\145\144\x20\x73\165\x63\143\x65\163\x73\146\165\154\x6c\171\x2e");
        $mx->mo_oauth_show_success_message();
        goto O7m;
        kff:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x54\150\145\162\145\x20\x77\141\x73\x20\x61\x6e\40\x65\x72\x72\x6f\162\40\x73\x61\x76\151\x6e\x67\x20\x73\145\x74\x74\151\x6e\147\x73\x2e");
        $mx->mo_oauth_show_error_message();
        O7m:
        wp_safe_redirect("\x61\144\x6d\151\156\56\160\x68\x70\x3f\x70\141\147\x65\x3d\155\157\137\157\141\165\x74\x68\137\163\145\x74\x74\151\x6e\x67\x73\x26\x74\x61\x62\75\143\157\156\146\151\x67\x26\x61\x63\164\x69\157\x6e\x3d\165\x70\144\x61\x74\x65\x26\x61\160\x70\x3d" . rawurlencode($bj));
        Os0:
        MUC:
    }
}
