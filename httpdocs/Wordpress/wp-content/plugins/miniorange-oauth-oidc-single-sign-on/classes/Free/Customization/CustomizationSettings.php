<?php


namespace MoOauthClient\Free;

class CustomizationSettings
{
    public function save_customization_settings()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\x64\x69\x73\x61\142\x6c\145\x64";
        if (empty($n2["\155\x6f\137\144\x74\145\137\163\164\141\164\145"])) {
            goto qLa;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\x6f\137\x64\x74\145\137\163\x74\141\164\145"]);
        qLa:
        if (!($Wz == "\144\151\163\141\x62\154\145\144")) {
            goto See;
        }
        if (!(isset($_POST["\155\x6f\137\x6f\x61\x75\x74\150\x5f\141\160\x70\x5f\143\165\163\164\157\155\x69\172\141\x74\x69\157\156\137\156\157\x6e\143\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\x5f\157\x61\x75\164\x68\x5f\141\160\x70\137\x63\x75\163\164\x6f\x6d\x69\172\141\x74\151\157\156\137\x6e\x6f\156\x63\x65"])), "\x6d\157\137\157\x61\x75\164\x68\137\x61\x70\x70\x5f\143\x75\x73\x74\157\155\x69\172\141\164\x69\157\156") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\x6f\x5f\157\141\x75\x74\x68\137\x61\x70\160\x5f\x63\165\163\164\x6f\x6d\151\x7a\141\164\151\157\156" === $_POST[\MoOAuthConstants::OPTION])) {
            goto hXf;
        }
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\141\165\164\x68\137\151\143\157\x6e\137\164\150\145\155\145", stripslashes($_POST["\155\x6f\x5f\x6f\141\x75\164\x68\x5f\151\143\157\156\137\x74\150\145\155\145"]));
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\x61\x75\x74\150\x5f\151\x63\157\156\137\x73\150\141\160\x65", stripslashes($_POST["\x6d\x6f\137\x6f\141\165\x74\150\137\151\143\157\156\137\x73\150\x61\160\x65"]));
        isset($_POST["\155\x6f\x5f\157\x61\x75\x74\150\x5f\151\143\x6f\156\x5f\x65\146\146\x65\x63\x74\137\163\x63\x61\x6c\x65"]) ? $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\157\141\x75\x74\x68\x5f\x69\143\x6f\x6e\137\x65\x66\146\145\x63\x74\137\163\x63\x61\154\145", stripslashes($_POST["\155\157\137\157\141\x75\164\150\x5f\x69\x63\157\x6e\137\x65\x66\146\145\x63\164\137\x73\143\x61\x6c\x65"])) : $mx->mo_oauth_client_update_option("\155\x6f\137\x6f\141\x75\x74\150\x5f\151\143\157\156\137\x65\x66\x66\x65\x63\164\x5f\x73\x63\141\154\145", '');
        isset($_POST["\x6d\x6f\137\x6f\x61\x75\x74\x68\137\x69\x63\x6f\156\x5f\145\x66\x66\145\x63\x74\x5f\163\150\141\x64\157\167"]) ? $mx->mo_oauth_client_update_option("\155\157\x5f\x6f\141\165\x74\x68\x5f\151\x63\157\x6e\x5f\145\146\x66\145\143\x74\x5f\163\150\x61\x64\157\x77", stripslashes($_POST["\x6d\157\x5f\157\x61\x75\x74\x68\137\x69\x63\x6f\x6e\137\x65\146\x66\x65\143\164\x5f\163\150\x61\x64\x6f\167"])) : $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\157\141\x75\164\x68\x5f\151\x63\x6f\x6e\x5f\145\146\146\145\x63\x74\x5f\x73\150\141\x64\157\167", '');
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\x6f\141\165\x74\150\137\x69\143\x6f\x6e\137\167\x69\144\164\150", stripslashes($_POST["\155\x6f\x5f\x6f\x61\165\164\150\137\151\x63\x6f\x6e\137\167\151\x64\164\150"]));
        $mx->mo_oauth_client_update_option("\155\157\x5f\157\141\165\164\150\x5f\151\x63\157\x6e\x5f\x68\145\151\147\150\164", stripslashes($_POST["\x6d\x6f\x5f\157\141\165\x74\x68\x5f\151\x63\x6f\x6e\137\x68\x65\151\x67\x68\164"]));
        $mx->mo_oauth_client_update_option("\155\157\137\157\x61\x75\x74\x68\137\151\x63\x6f\156\x5f\x63\157\x6c\x6f\162", stripslashes($_POST["\155\157\x5f\x6f\x61\165\164\x68\x5f\151\x63\157\156\x5f\143\x6f\x6c\157\x72"]));
        $mx->mo_oauth_client_update_option("\155\x6f\137\x6f\x61\x75\164\150\x5f\x69\x63\x6f\x6e\137\143\165\x73\164\x6f\155\x5f\143\157\154\157\x72", stripslashes($_POST["\155\157\137\157\141\x75\164\x68\137\x69\143\x6f\x6e\137\x63\165\x73\x74\x6f\155\x5f\x63\x6f\x6c\x6f\162"]));
        $mx->mo_oauth_client_update_option("\155\157\137\157\141\165\x74\150\137\x69\x63\157\x6e\x5f\x73\155\x61\x72\x74\137\143\x6f\154\157\162\x5f\61", stripslashes($_POST["\x6d\157\x5f\157\x61\x75\x74\150\137\x69\x63\x6f\156\x5f\163\x6d\141\162\x74\137\143\157\x6c\x6f\x72\x5f\61"]));
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\x6f\141\x75\x74\x68\137\151\143\157\156\137\x73\155\x61\162\164\137\143\x6f\x6c\x6f\162\x5f\x32", stripslashes($_POST["\155\157\137\157\141\165\x74\150\x5f\151\143\x6f\x6e\x5f\163\155\x61\162\164\137\143\157\154\x6f\x72\x5f\62"]));
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\x61\x75\x74\x68\137\x69\x63\157\156\x5f\x63\165\162\x76\x65", stripslashes($_POST["\155\x6f\137\x6f\x61\165\164\x68\x5f\151\143\x6f\x6e\x5f\x63\x75\162\166\x65"]));
        $mx->mo_oauth_client_update_option("\155\157\x5f\157\141\x75\164\x68\x5f\x69\x63\x6f\x6e\x5f\163\151\172\145", stripslashes($_POST["\155\x6f\x5f\x6f\x61\165\164\150\x5f\x69\x63\157\156\137\x73\x69\172\145"]));
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\141\x75\x74\150\137\151\143\157\x6e\137\x6d\141\x72\147\151\x6e", stripslashes($_POST["\x6d\x6f\x5f\157\141\x75\x74\x68\137\151\143\x6f\x6e\x5f\x6d\x61\162\147\x69\156"]));
        $G9 = preg_replace("\x2f\134\156\53\57", "\12", trim($_POST["\x6d\x6f\x5f\x6f\141\165\164\x68\x5f\151\x63\157\156\137\143\157\x6e\x66\151\x67\x75\162\x65\x5f\143\163\163"]));
        $mx->mo_oauth_client_update_option("\155\x6f\137\x6f\141\165\x74\x68\x5f\x69\143\x6f\x6e\137\143\x6f\x6e\146\x69\147\165\x72\x65\x5f\x63\x73\163", $G9);
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\x61\165\164\150\137\x63\x75\163\x74\157\x6d\x5f\154\157\x67\157\x75\164\137\164\145\170\164", stripslashes($_POST["\x6d\157\x5f\x6f\x61\165\x74\150\x5f\x63\165\x73\164\157\155\137\154\157\x67\157\165\x74\137\164\145\x78\x74"]));
        $mx->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\165\162\40\163\x65\x74\164\x69\x6e\x67\163\40\167\x65\x72\145\x20\x73\x61\x76\x65\144");
        $mx->mo_oauth_show_success_message();
        hXf:
        See:
    }
    public function set_default_customize_value()
    {
        global $mx;
        $mx->mo_oauth_client_update_option("\155\x6f\137\157\x61\165\x74\150\137\151\143\x6f\x6e\x5f\x74\150\x65\155\145", "\x70\162\x65\x76\151\x6f\x75\163");
        $mx->mo_oauth_client_update_option("\x6d\157\137\157\141\x75\x74\150\137\151\143\157\x6e\137\163\x68\141\x70\x65", "\154\x6f\x6e\147\142\165\164\164\157\x6e");
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\x61\165\x74\x68\x5f\151\143\x6f\156\x5f\x65\x66\146\x65\143\x74\137\x73\x63\141\154\x65", '');
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\157\141\165\x74\150\x5f\151\x63\157\156\x5f\x65\146\x66\x65\x63\164\137\x73\x68\141\x64\x6f\167", '');
        if ($mx->mo_oauth_client_get_option("\155\x6f\137\x6f\x61\x75\x74\150\137\x69\143\x6f\x6e\137\x77\x69\x64\164\x68")) {
            goto KsX;
        }
        $mx->mo_oauth_client_update_option("\155\x6f\137\157\x61\165\164\x68\x5f\151\x63\x6f\x6e\x5f\167\151\144\164\x68", "\63\x32\65");
        KsX:
        if (!$mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\165\x74\x68\x5f\151\x63\157\x6e\137\150\x65\x69\x67\150\164")) {
            goto Vc0;
        }
        $IR = $mx->mo_oauth_client_get_option("\155\x6f\x5f\157\x61\x75\164\150\137\162\x65\143\164\x69\146\x79\x5f\151\x63\x6f\156\137\150\x65\151\147\150\x74\x5f\146\154\141\x67");
        if (!($IR == false)) {
            goto lCl;
        }
        $Vs = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\x75\164\150\x5f\x69\x63\157\x6e\x5f\150\145\x69\147\150\x74");
        $Vs = (int) $Vs;
        $Vs = $Vs * 2;
        $Vs = (string) $Vs;
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\x61\165\164\x68\x5f\x69\x63\x6f\156\137\150\145\151\147\150\x74", $Vs);
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\157\141\x75\164\150\137\162\x65\143\x74\x69\x66\171\x5f\x69\143\157\156\137\x68\145\151\147\150\164\137\146\x6c\141\147", True);
        lCl:
        goto QLB;
        Vc0:
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\157\x61\165\x74\x68\137\151\x63\x6f\x6e\x5f\150\145\x69\147\x68\x74", "\63\x35");
        $mx->mo_oauth_client_update_option("\155\157\x5f\157\x61\165\164\150\x5f\162\145\x63\164\x69\146\x79\x5f\x69\143\157\156\x5f\x68\x65\x69\x67\150\x74\x5f\x66\154\141\x67", True);
        QLB:
        $mx->mo_oauth_client_update_option("\155\157\137\157\x61\x75\x74\x68\x5f\x69\x63\157\156\x5f\143\x6f\x6c\157\x72", "\x23\60\x30\x30\x30\60\60");
        $mx->mo_oauth_client_update_option("\155\157\x5f\x6f\141\165\x74\x68\137\x69\x63\x6f\156\x5f\x63\x75\163\164\x6f\155\x5f\143\x6f\154\x6f\162", "\x23\60\x30\70\145\x63\62");
        $mx->mo_oauth_client_update_option("\155\157\x5f\x6f\141\165\164\150\x5f\151\x63\x6f\x6e\137\163\155\x61\x72\164\137\143\157\154\157\x72\x5f\61", "\x23\x46\x46\x31\x46\x34\102");
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\157\x61\165\164\150\x5f\x69\143\x6f\156\137\x73\155\141\162\164\137\143\x6f\x6c\x6f\162\x5f\x32", "\x23\62\x30\60\70\x46\106");
        $mx->mo_oauth_client_update_option("\155\x6f\137\x6f\x61\x75\x74\x68\137\151\143\157\x6e\x5f\143\165\162\166\x65", "\64");
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\x6f\x61\165\x74\150\137\x69\143\157\x6e\137\163\x69\172\x65", "\63\65");
        if ($mx->mo_oauth_client_get_option("\155\x6f\137\157\x61\165\164\150\137\151\143\157\156\x5f\x6d\141\x72\x67\151\x6e")) {
            goto I0w;
        }
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\x6f\141\x75\x74\150\137\x69\143\157\156\137\x6d\141\162\x67\x69\156", "\x34");
        I0w:
    }
    public function mo_oauth_custom_icons_intiater()
    {
        global $mx;
        $De = $mx->mo_oauth_client_get_option("\155\157\137\x6f\x61\165\164\150\x5f\151\x63\x6f\x6e\137\164\150\x65\x6d\x65");
        $D5 = $mx->mo_oauth_client_get_option("\x6d\157\x5f\157\x61\x75\164\x68\137\151\x63\157\156\x5f\x73\x68\x61\160\x65");
        if (!((!$De || empty($De)) && (!$D5 || empty($D5)))) {
            goto RES;
        }
        $hZ = $this->set_default_customize_value();
        RES:
    }
}
