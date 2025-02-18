<?php


namespace MoOauthClient\Free;

use MoOauthClient\AppUI;
use MoOauthClient\App\UpdateAppUI;
use MoOauthClient\AppGuider;
class ClientAppUI
{
    private $common_app_ui;
    public function __construct()
    {
        $this->common_app_ui = new AppUI();
    }
    public function render_free_ui()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\x64\151\163\x61\142\154\x65\x64";
        if (empty($n2["\155\157\x5f\x64\x74\x65\137\x73\164\141\x74\x65"])) {
            goto o1I;
        }
        $Wz = $mx->mooauthdecrypt($n2["\155\x6f\x5f\144\164\145\137\163\164\141\164\145"]);
        o1I:
        $W2 = $this->common_app_ui->get_apps_list();
        if (!($Wz == "\x64\151\163\x61\x62\154\145\x64")) {
            goto Agc;
        }
        if (!(isset($_GET["\141\x63\x74\x69\157\x6e"]) && "\x64\x65\154\x65\164\145" === $_GET["\x61\x63\x74\x69\x6f\x6e"])) {
            goto VkO;
        }
        if (!(isset($_GET["\141\x70\x70"]) && check_admin_referer("\155\157\137\x6f\141\165\164\x68\x5f\144\145\154\145\x74\x65\137" . sanitize_text_field(wp_unslash($_GET["\141\x70\x70"]))))) {
            goto a_n;
        }
        $this->common_app_ui->delete_app($_GET["\x61\160\x70"]);
        return;
        a_n:
        VkO:
        Agc:
        if (!(isset($_GET["\x61\143\x74\x69\157\x6e"]) && "\x69\x6e\163\164\162\165\x63\164\151\157\x6e\163" === $_GET["\141\x63\x74\151\157\x6e"] || isset($_GET["\x73\x68\157\167"]) && "\x69\x6e\x73\x74\x72\165\x63\x74\151\x6f\156\x73" === $_GET["\x73\150\x6f\167"])) {
            goto Aac;
        }
        if (!(isset($_GET["\141\160\x70\x49\x64"]) && isset($_GET["\x66\x6f\162"]))) {
            goto peY;
        }
        $fR = new AppGuider($_GET["\x61\x70\x70\x49\144"], $_GET["\x66\x6f\162"]);
        $fR->show_guide();
        peY:
        if (!(isset($_GET["\163\x68\x6f\x77"]) && "\x69\156\x73\x74\x72\x75\143\164\151\157\156\163" === $_GET["\x73\x68\157\x77"])) {
            goto xYW;
        }
        $fR = new AppGuider($_GET["\141\160\160\x49\x64"]);
        $fR->show_guide();
        $this->common_app_ui->add_app_ui();
        return;
        xYW:
        Aac:
        if (!(isset($_GET["\x61\143\164\151\x6f\x6e"]) && "\141\144\x64" === $_GET["\x61\x63\x74\151\x6f\156"])) {
            goto M1B;
        }
        $this->common_app_ui->add_app_ui();
        return;
        M1B:
        if (!(isset($_GET["\141\x63\x74\x69\157\156"]) && "\x75\160\x64\x61\x74\x65" === $_GET["\141\x63\x74\x69\x6f\x6e"])) {
            goto d_d;
        }
        if (!isset($_GET["\141\x70\x70"])) {
            goto Un3;
        }
        $Zy = $this->common_app_ui->get_app_by_name($_GET["\x61\160\x70"]);
        new UpdateAppUI($_GET["\x61\x70\160"], $Zy);
        return;
        Un3:
        d_d:
        if (!(isset($_GET["\141\143\x74\151\157\156"]) && "\141\144\x64\x5f\156\145\x77" === $_GET["\141\143\x74\x69\157\156"])) {
            goto SWt;
        }
        $this->common_app_ui->add_app_ui();
        return;
        SWt:
        if (!(is_array($W2) && count($W2) > 0)) {
            goto Dty;
        }
        $this->common_app_ui->show_apps_list_page();
        return;
        Dty:
        $this->common_app_ui->add_app_ui();
    }
}
