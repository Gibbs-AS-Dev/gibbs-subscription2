<?php


namespace MoOauthClient\Standard;

use MoOauthClient\App;
use MoOauthClient\Free\AppSettings as FreeAppSettings;
class AppSettings extends FreeAppSettings
{
    public function __construct()
    {
        parent::__construct();
        add_action("\155\157\137\x6f\x61\165\164\150\x5f\143\154\x69\145\x6e\164\137\163\x61\x76\145\x5f\x61\x70\160\137\x73\145\x74\x74\151\156\x67\163\x5f\x69\x6e\164\x65\x72\x6e\141\154", array($this, "\x73\141\x76\x65\137\162\x6f\x6c\x65\x5f\x6d\141\160\x70\x69\156\147"));
    }
    public function change_app_settings($post, $Z6)
    {
        $Z6 = parent::change_app_settings($post, $Z6);
        $Z6["\x64\151\x73\x70\x6c\141\171\x61\160\x70\x6e\141\x6d\145"] = isset($post["\155\x6f\x5f\157\141\x75\x74\x68\x5f\x64\151\163\x70\x6c\x61\171\137\141\160\160\x5f\156\x61\155\x65"]) ? trim(stripslashes($post["\155\157\x5f\x6f\x61\x75\164\150\x5f\144\x69\x73\x70\x6c\x61\x79\137\141\160\160\137\x6e\141\155\145"])) : '';
        return $Z6;
    }
    public function change_attribute_mapping($post, $Z6)
    {
        $Z6 = parent::change_attribute_mapping($post, $Z6);
        $Z6["\145\x6d\141\151\x6c\137\x61\x74\x74\162"] = isset($post["\155\157\x5f\x6f\x61\165\x74\x68\137\145\155\141\151\x6c\137\141\x74\x74\162"]) ? stripslashes($post["\x6d\x6f\x5f\157\x61\x75\164\150\137\145\x6d\x61\x69\154\137\x61\164\x74\x72"]) : '';
        $Z6["\x66\x69\162\x73\x74\156\x61\x6d\145\137\x61\164\164\162"] = isset($post["\x6d\x6f\137\x6f\x61\x75\164\150\137\x66\151\162\163\164\156\141\x6d\145\x5f\x61\x74\x74\x72"]) ? trim(stripslashes($post["\155\x6f\137\157\x61\165\x74\x68\x5f\x66\x69\162\x73\164\x6e\141\x6d\145\x5f\x61\164\164\162"])) : '';
        $Z6["\x6c\141\x73\x74\x6e\141\x6d\145\137\141\x74\x74\162"] = isset($post["\155\x6f\x5f\157\x61\165\x74\150\137\x6c\141\163\x74\156\141\155\145\137\141\164\164\162"]) ? trim(stripslashes($post["\x6d\157\137\x6f\141\165\164\150\137\154\141\x73\x74\156\x61\x6d\x65\137\x61\x74\x74\162"])) : '';
        $Z6["\145\x6e\141\x62\154\x65\137\162\157\x6c\x65\137\155\141\160\x70\x69\156\147"] = isset($post["\145\x6e\x61\x62\x6c\x65\x5f\x72\157\x6c\x65\137\155\141\160\160\x69\x6e\147"]) ? sanitize_text_field(wp_unslash($_POST["\145\x6e\x61\x62\154\x65\137\x72\x6f\x6c\x65\137\x6d\x61\160\160\151\156\147"])) : false;
        $Z6["\141\x6c\154\x6f\167\x5f\x64\x75\x70\x6c\x69\143\x61\x74\x65\x5f\145\155\x61\151\154\x73"] = isset($post["\x61\154\x6c\157\x77\x5f\144\165\160\154\151\143\141\x74\x65\x5f\x65\155\141\151\x6c\x73"]) ? sanitize_text_field(wp_unslash($_POST["\141\x6c\x6c\157\x77\137\144\165\160\x6c\x69\x63\141\164\145\x5f\145\155\x61\x69\154\163"])) : false;
        $Z6["\144\x69\x73\160\x6c\x61\x79\137\x61\164\x74\x72"] = isset($post["\157\141\x75\x74\x68\137\143\154\151\145\x6e\164\x5f\x61\x6d\x5f\144\151\x73\160\154\141\171\137\156\x61\x6d\145"]) ? trim(stripslashes($post["\x6f\x61\165\x74\150\137\x63\154\151\x65\x6e\x74\x5f\141\155\137\x64\x69\x73\x70\x6c\141\x79\137\156\141\x6d\145"])) : '';
        return $Z6;
    }
    public function save_role_mapping()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\151\x73\x61\142\154\145\144";
        if (empty($n2["\x6d\x6f\x5f\144\164\145\x5f\163\164\x61\164\145"])) {
            goto yGP;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\157\137\144\x74\145\137\x73\164\141\x74\x65"]);
        yGP:
        if (!($Wz == "\144\151\163\141\x62\154\145\x64")) {
            goto mo9;
        }
        if (!(isset($_POST["\x6d\x6f\x5f\157\141\165\164\150\x5f\x63\x6c\x69\145\x6e\164\x5f\163\x61\x76\x65\137\x72\x6f\154\145\x5f\x6d\141\160\x70\x69\x6e\147\x5f\x6e\157\156\143\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\x5f\x6f\x61\x75\x74\150\137\x63\x6c\151\145\x6e\x74\137\x73\x61\x76\x65\x5f\162\157\x6c\x65\x5f\x6d\141\x70\x70\151\156\147\x5f\x6e\x6f\156\x63\145"])), "\155\157\137\157\141\x75\164\150\137\143\154\x69\x65\x6e\x74\x5f\163\x61\166\145\x5f\x72\157\x6c\x65\137\155\141\160\x70\151\x6e\147") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\157\x5f\157\141\x75\164\150\x5f\143\x6c\151\x65\x6e\164\x5f\163\x61\166\145\137\162\x6f\x6c\x65\137\155\x61\x70\x70\151\156\147" === $_POST[\MoOAuthConstants::OPTION])) {
            goto gh5;
        }
        $bj = sanitize_text_field(wp_unslash(isset($_POST[\MoOAuthConstants::POST_APP_NAME]) ? $_POST[\MoOAuthConstants::POST_APP_NAME] : ''));
        $Zy = $mx->get_app_by_name($bj);
        $xA = $Zy->get_app_config('', false);
        $xA["\137\x6d\x61\160\160\x69\156\x67\137\x76\141\154\x75\145\x5f\144\145\x66\141\x75\x6c\x74"] = isset($_POST["\155\x61\x70\160\x69\156\147\x5f\x76\x61\154\165\145\137\x64\145\x66\x61\165\154\x74"]) ? sanitize_text_field(wp_unslash($_POST["\155\141\x70\160\151\x6e\x67\x5f\166\x61\154\165\x65\137\144\x65\x66\x61\x75\154\x74"])) : false;
        $xA["\x6b\x65\145\x70\137\x65\x78\x69\x73\164\151\156\147\x5f\165\163\x65\162\137\162\x6f\x6c\x65\163"] = isset($_POST["\153\x65\x65\160\137\145\x78\151\163\164\x69\156\147\x5f\165\x73\145\x72\x5f\162\x6f\154\x65\163"]) ? sanitize_text_field(wp_unslash($_POST["\153\x65\x65\x70\137\x65\170\x69\163\x74\151\156\147\x5f\x75\163\x65\x72\137\x72\157\154\145\163"])) : 0;
        $k9 = $mx->set_app_by_name($bj, $xA);
        gh5:
        mo9:
    }
}
