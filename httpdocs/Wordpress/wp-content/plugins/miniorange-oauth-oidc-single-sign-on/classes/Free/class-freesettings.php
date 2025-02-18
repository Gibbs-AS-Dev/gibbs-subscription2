<?php


namespace MoOauthClient\Free;

use MoOauthClient\Settings;
use MoOauthClient\Free\CustomizationSettings;
use MoOauthClient\Free\RequestfordemoSettings;
use MoOauthClient\Free\AppSettings;
use MoOauthClient\Customer;
class FreeSettings
{
    private $common_settings;
    public function __construct()
    {
        $this->common_settings = new Settings();
        add_action("\x61\144\155\x69\156\x5f\x69\x6e\151\x74", array($this, "\155\x6f\x5f\x6f\141\x75\164\150\x5f\x63\x6c\x69\145\x6e\164\137\x66\162\145\x65\x5f\x73\x65\164\x74\151\x6e\147\163"));
        add_action("\x61\x64\x6d\151\156\x5f\x66\157\157\x74\x65\162", array($this, "\x6d\157\137\x6f\x61\x75\164\x68\x5f\x63\154\151\145\156\164\137\146\145\x65\144\142\x61\x63\x6b\x5f\x72\x65\161\x75\145\163\164"));
    }
    public function mo_oauth_client_free_settings()
    {
        global $mx;
        $X6 = new CustomizationSettings();
        $Rr = new RequestfordemoSettings();
        $X6->save_customization_settings();
        $Rr->save_requestdemo_settings();
        $tN = new AppSettings();
        $tN->save_app_settings();
        if (!(isset($_POST["\x6d\157\x5f\157\x61\165\164\150\137\x63\x6c\151\145\x6e\164\x5f\x66\145\x65\144\x62\x61\143\x6b\137\x6e\157\x6e\x63\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\137\x6f\x61\165\164\150\x5f\x63\x6c\x69\x65\156\x74\x5f\x66\145\x65\144\142\x61\143\153\x5f\x6e\x6f\x6e\x63\145"])), "\155\157\x5f\x6f\141\x75\164\150\137\x63\x6c\151\145\x6e\x74\137\146\x65\145\x64\x62\x61\x63\153") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\157\137\157\141\165\164\150\x5f\143\154\151\145\156\164\x5f\146\145\x65\144\x62\x61\143\153" === $_POST[\MoOAuthConstants::OPTION])) {
            goto GUz;
        }
        $user = wp_get_current_user();
        $Kv = "\x50\x6c\165\147\x69\156\40\104\x65\141\143\164\x69\166\x61\164\x65\x64\72";
        $r5 = isset($_POST["\x64\145\x61\x63\164\x69\x76\x61\164\x65\x5f\x72\145\x61\163\157\x6e\x5f\x72\141\x64\x69\x6f"]) ? sanitize_text_field(wp_unslash($_POST["\x64\145\x61\143\164\x69\166\x61\x74\145\137\x72\145\x61\x73\157\156\137\x72\x61\144\151\157"])) : false;
        $C7 = isset($_POST["\x71\165\x65\x72\x79\x5f\x66\x65\145\144\142\x61\x63\x6b"]) ? sanitize_text_field(wp_unslash($_POST["\x71\165\145\x72\171\137\146\x65\145\x64\142\x61\143\153"])) : false;
        if ($r5) {
            goto h3L;
        }
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\120\154\x65\x61\163\x65\40\123\145\154\145\143\164\40\x6f\156\145\x20\x6f\146\x20\164\150\145\40\162\x65\x61\x73\157\x6e\x73\x20\54\x69\x66\40\171\x6f\165\x72\x20\162\145\141\163\157\156\x20\151\x73\x20\x6e\157\x74\40\155\145\x6e\x74\x69\157\156\145\x64\x20\160\154\x65\141\x73\x65\x20\x73\x65\x6c\145\x63\164\40\117\164\x68\145\162\40\x52\145\141\163\x6f\156\x73");
        $mx->mo_oauth_show_error_message();
        h3L:
        $Kv .= $r5;
        if (!isset($C7)) {
            goto TfP;
        }
        $Kv .= "\x3a" . $C7;
        TfP:
        $UU = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\x75\164\150\x5f\141\144\155\151\156\x5f\145\x6d\141\x69\154");
        if (!($UU == '')) {
            goto tE5;
        }
        $UU = $user->user_email;
        tE5:
        $rH = $mx->mo_oauth_client_get_option("\155\x6f\137\157\141\x75\x74\x68\137\x61\x64\x6d\151\156\137\160\150\x6f\156\145");
        $Bo = new Customer();
        $nX = json_decode($Bo->mo_oauth_send_email_alert($UU, $rH, $Kv), true);
        deactivate_plugins(MOC_DIR . "\x6d\x6f\137\x6f\x61\165\164\150\x5f\x73\x65\164\164\151\156\147\163\56\x70\x68\x70");
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\124\150\141\x6e\153\40\x79\x6f\x75\40\x66\157\x72\40\x74\150\145\40\146\145\145\144\x62\141\x63\x6b\56");
        $mx->mo_oauth_show_success_message();
        GUz:
        if (!(isset($_POST["\155\157\x5f\157\141\165\x74\150\137\143\154\x69\x65\156\x74\137\163\x6b\x69\x70\x5f\x66\145\x65\x64\x62\141\x63\x6b\137\x6e\157\156\x63\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\x6f\x61\x75\x74\150\x5f\143\x6c\x69\x65\x6e\x74\x5f\x73\x6b\x69\160\137\146\145\x65\144\x62\141\x63\153\x5f\156\157\156\x63\x65"])), "\155\x6f\137\157\x61\165\164\150\137\x63\154\x69\145\156\x74\137\163\x6b\x69\x70\x5f\146\x65\x65\144\142\141\x63\153") && isset($_POST["\x6f\160\164\x69\x6f\156"]) && "\x6d\x6f\137\157\x61\165\164\150\137\x63\x6c\x69\145\156\x74\x5f\x73\153\x69\x70\x5f\x66\145\x65\x64\x62\141\x63\x6b" === $_POST["\157\160\x74\x69\x6f\156"])) {
            goto rho;
        }
        deactivate_plugins(MOC_DIR . "\155\x6f\137\x6f\x61\165\x74\150\x5f\163\x65\x74\164\151\x6e\147\163\56\x70\150\160");
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\154\x75\147\x69\156\40\x44\145\x61\143\164\151\x76\141\x74\x65\x64\x2e");
        $mx->mo_oauth_show_success_message();
        rho:
    }
    public function mo_oauth_client_feedback_request()
    {
        $wd = new \MoOauthClient\Free\Feedback();
        $wd->show_form();
    }
}
