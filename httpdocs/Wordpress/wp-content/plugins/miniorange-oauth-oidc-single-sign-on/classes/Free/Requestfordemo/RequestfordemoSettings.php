<?php


namespace MoOauthClient\Free;

use MoOauthClient\Customer;
class RequestfordemoSettings
{
    public function save_requestdemo_settings()
    {
        global $mx;
        if (!(isset($_POST["\x6d\157\137\157\x61\165\x74\150\137\141\160\x70\x5f\x72\145\161\165\145\x73\x74\144\x65\x6d\x6f\x5f\156\x6f\156\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\x5f\x6f\141\165\x74\150\137\x61\x70\160\137\162\145\x71\x75\145\163\x74\x64\x65\x6d\x6f\137\156\x6f\156\143\145"])), "\155\157\137\x6f\x61\x75\x74\x68\x5f\141\x70\x70\137\162\x65\x71\x75\145\x73\x74\x64\145\155\x6f") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\x5f\x6f\141\x75\164\150\137\141\160\x70\137\162\x65\x71\x75\145\163\x74\144\145\155\x6f" === $_POST[\MoOAuthConstants::OPTION])) {
            goto EWP;
        }
        $UU = $_POST["\x6d\157\x5f\157\x61\x75\164\150\137\143\x6c\x69\145\x6e\x74\137\x64\145\x6d\157\137\145\155\141\151\154"];
        $IT = $_POST["\x6d\x6f\x5f\157\141\165\164\x68\137\143\154\151\x65\x6e\164\137\x64\145\x6d\157\137\x70\x6c\x61\156"];
        $mO = $_POST["\155\157\x5f\x6f\x61\x75\x74\x68\x5f\x63\x6c\x69\x65\156\x74\x5f\144\x65\155\x6f\x5f\x64\x65\163\x63\162\x69\x70\x74\x69\157\x6e"];
        $pZ = new Customer();
        if ($mx->mo_oauth_check_empty_or_null($UU) || $mx->mo_oauth_check_empty_or_null($IT)) {
            goto CYd;
        }
        $nX = json_decode($pZ->mo_oauth_send_demo_alert($UU, $IT, $mO, "\127\120\40\x4f\x41\x75\164\150\40\x53\151\156\147\154\145\x20\x53\151\x67\x6e\40\117\156\x20\x44\x65\155\x6f\x20\x52\145\x71\x75\x65\163\x74\x20\55\x20" . $UU), true);
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x54\x68\141\x6e\153\x73\40\x66\x6f\162\40\147\145\164\164\151\x6e\x67\x20\151\156\x20\164\x6f\165\x63\150\41\x20\127\x65\40\x73\x68\x61\x6c\x6c\x20\147\145\164\x20\142\x61\x63\x6b\40\164\x6f\x20\x79\x6f\x75\40\163\150\x6f\162\164\154\x79\56");
        $mx->mo_oauth_show_success_message();
        goto KKp;
        CYd:
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\154\x65\141\x73\145\x20\146\151\x6c\154\40\x75\160\x20\x45\155\x61\x69\x6c\x20\x66\x69\x65\x6c\144\40\164\157\x20\x73\x75\142\155\x69\164\x20\171\x6f\x75\x72\x20\x71\x75\x65\x72\x79\56");
        $mx->mo_oauth_show_success_message();
        KKp:
        EWP:
    }
}
