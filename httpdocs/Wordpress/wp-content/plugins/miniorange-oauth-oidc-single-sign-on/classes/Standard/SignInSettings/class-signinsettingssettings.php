<?php


namespace MoOauthClient\Standard;

use MoOauthClient\Config;
class SignInSettingsSettings
{
    private $plugin_config;
    public function __construct()
    {
        $gu = $this->get_config_option();
        if ($gu && isset($gu)) {
            goto PA6;
        }
        $this->plugin_config = new Config();
        $this->save_config_option($this->plugin_config);
        goto zwm;
        PA6:
        $this->save_config_option($gu);
        $this->plugin_config = $gu;
        zwm:
    }
    public function save_config_option($n2 = array())
    {
        global $mx;
        if (!(isset($n2) && !empty($n2))) {
            goto Mxo;
        }
        return $mx->mo_oauth_client_update_option("\155\x6f\137\157\x61\x75\x74\150\137\x63\x6c\x69\x65\156\164\137\143\157\x6e\146\151\x67", $n2);
        Mxo:
        return false;
    }
    public function get_config_option()
    {
        global $mx;
        return $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\157\141\165\x74\150\137\143\x6c\151\x65\x6e\164\137\x63\x6f\x6e\x66\151\x67");
    }
    public function get_sane_config()
    {
        $n2 = $this->plugin_config;
        if ($n2 && isset($n2)) {
            goto GbE;
        }
        $n2 = $this->get_config_option();
        GbE:
        if (!(!$n2 || !isset($n2))) {
            goto WM4;
        }
        $n2 = new Config();
        WM4:
        return $n2;
    }
    public function mo_oauth_save_settings()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\151\163\141\142\154\x65\144";
        if (empty($n2["\x6d\x6f\x5f\144\164\x65\137\x73\164\x61\164\145"])) {
            goto URv;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\157\137\144\x74\x65\x5f\163\164\x61\x74\x65"]);
        URv:
        if (!($Wz == "\x64\x69\163\141\x62\x6c\145\x64")) {
            goto vy2;
        }
        $n2 = $this->get_sane_config();
        if (!(isset($_POST["\x6d\157\137\163\x69\147\156\x69\156\x73\x65\x74\x74\x69\156\x67\163\137\x6e\157\156\x63\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\157\137\x73\151\147\156\151\156\x73\145\164\x74\151\156\x67\163\x5f\156\x6f\x6e\x63\145"])), "\155\x6f\x5f\157\141\165\x74\x68\137\x63\154\151\145\156\164\137\x73\x69\x67\156\137\151\156\137\163\145\164\x74\151\156\147\163") && (isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\137\x6f\141\165\x74\150\x5f\143\x6c\151\145\156\164\137\141\x64\x76\x61\x6e\143\145\144\x5f\x73\x65\x74\164\x69\x6e\x67\x73" === $_POST[\MoOAuthConstants::OPTION]))) {
            goto Kcz;
        }
        $n2 = $this->change_current_config($_POST, $n2);
        $n2->save_settings($n2->get_current_config());
        $this->save_config_option($n2);
        Kcz:
        vy2:
    }
    public function change_current_config($post, $n2)
    {
        $n2->add_config("\141\x66\164\145\162\x5f\154\x6f\x67\151\156\x5f\165\x72\x6c", isset($post["\143\x75\x73\x74\x6f\x6d\x5f\141\x66\x74\x65\x72\x5f\154\x6f\147\x69\156\137\x75\x72\x6c"]) ? stripslashes(wp_unslash($post["\143\165\x73\x74\157\155\x5f\141\x66\x74\x65\162\137\x6c\157\147\151\156\x5f\x75\162\154"])) : '');
        $n2->add_config("\x61\x66\x74\x65\x72\137\x6c\157\x67\157\165\x74\137\165\x72\x6c", isset($post["\143\x75\x73\x74\x6f\155\x5f\141\x66\x74\145\x72\137\x6c\x6f\x67\x6f\x75\164\x5f\x75\162\154"]) ? stripslashes(wp_unslash($post["\x63\165\x73\164\157\155\x5f\141\146\164\145\162\137\154\157\x67\x6f\165\164\x5f\165\162\154"])) : '');
        $n2->add_config("\x70\x6f\160\x75\x70\137\x6c\157\x67\151\x6e", isset($post["\160\157\160\x75\160\x5f\154\x6f\147\x69\156"]) ? stripslashes(wp_unslash($post["\160\157\160\165\160\137\154\157\147\x69\x6e"])) : 0);
        $n2->add_config("\141\x75\x74\157\x5f\162\x65\147\151\x73\164\x65\x72", isset($post["\x61\x75\164\x6f\137\x72\145\x67\151\x73\x74\x65\x72"]) ? stripslashes(wp_unslash($post["\141\x75\164\157\x5f\162\145\x67\151\163\164\x65\162"])) : 0);
        $n2->add_config("\143\157\x6e\146\151\x72\155\137\x6c\157\x67\157\x75\164", isset($post["\x63\x6f\x6e\x66\x69\162\155\x5f\x6c\x6f\x67\157\x75\164"]) ? stripslashes(wp_unslash($post["\143\157\156\146\x69\x72\x6d\x5f\x6c\x6f\x67\157\x75\x74"])) : 0);
        return $n2;
    }
}
