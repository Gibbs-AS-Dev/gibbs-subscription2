<?php


namespace MoOauthClient\Base;

use MoOauthClient\Licensing;
use MoOauthClient\MoAddons;
use MoOauthClient\Base\InstanceHelper;
class Loader
{
    private $instance_helper;
    public function __construct()
    {
        add_action("\141\144\155\151\156\x5f\x65\156\161\165\x65\165\x65\137\x73\x63\x72\151\x70\x74\163", array($this, "\x70\x6c\165\x67\151\x6e\137\x73\x65\x74\164\x69\x6e\147\x73\x5f\163\x74\x79\154\x65"));
        add_action("\x61\144\x6d\x69\x6e\x5f\x65\x6e\161\x75\x65\165\145\137\x73\143\x72\151\x70\164\x73", array($this, "\x70\154\165\147\151\x6e\x5f\x73\x65\x74\164\x69\x6e\x67\x73\x5f\163\143\x72\x69\x70\x74"));
        $this->instance_helper = new InstanceHelper();
    }
    public function plugin_settings_style()
    {
        wp_enqueue_style("\x6d\x6f\x5f\x6f\x61\165\164\150\137\x61\144\x6d\x69\156\137\163\x65\164\164\x69\x6e\x67\x73\137\163\164\171\154\145", MOC_URL . "\162\x65\x73\x6f\x75\x72\143\145\163\x2f\x63\163\163\57\x73\x74\x79\x6c\145\137\163\145\x74\164\x69\x6e\x67\163\56\143\163\163", array(), $LB = null, $KK = false);
        wp_enqueue_style("\155\x6f\137\157\x61\x75\164\150\x5f\x61\144\155\151\156\137\x73\x65\164\164\151\156\147\x73\x5f\x70\x68\157\156\x65\137\163\x74\171\154\145", MOC_URL . "\162\145\x73\x6f\x75\162\x63\x65\163\x2f\x63\163\x73\57\x70\150\157\156\x65\x2e\143\x73\163", array(), $LB = null, $KK = false);
        wp_enqueue_style("\155\x6f\137\x6f\141\165\x74\150\x5f\x61\144\155\151\156\x5f\163\145\164\164\x69\156\147\163\137\x64\x61\164\x61\164\141\142\154\x65", MOC_URL . "\x72\x65\x73\x6f\x75\x72\143\x65\163\57\143\x73\x73\x2f\152\161\x75\x65\x72\x79\56\144\141\x74\141\124\x61\142\x6c\x65\163\x2e\155\151\x6e\56\143\163\x73", array(), $LB = null, $KK = false);
        wp_enqueue_style("\155\x6f\55\x77\160\55\x62\x6f\x6f\164\x73\164\x72\141\160\x2d\163\x6f\143\151\141\154", MOC_URL . "\x72\x65\163\x6f\165\162\143\x65\163\57\143\163\163\57\142\157\157\x74\163\x74\x72\x61\x70\x2d\x73\157\143\x69\x61\x6c\56\143\x73\163", array(), $LB = null, $KK = false);
        wp_enqueue_style("\x6d\157\x2d\167\160\x2d\x62\157\157\x74\x73\164\x72\x61\x70\x2d\155\x61\151\156", MOC_URL . "\162\145\x73\157\x75\x72\143\145\163\x2f\143\163\x73\57\142\x6f\x6f\x74\163\x74\x72\x61\160\56\155\x69\156\x2d\x70\x72\145\166\151\x65\167\x2e\143\x73\163", array(), $LB = null, $KK = false);
        wp_enqueue_style("\155\157\x2d\167\160\55\x66\x6f\x6e\164\55\x61\x77\x65\x73\x6f\155\x65", MOC_URL . "\x72\145\163\x6f\x75\162\x63\145\x73\x2f\143\163\163\x2f\x66\157\156\x74\x2d\141\x77\x65\x73\157\155\x65\x2e\x6d\x69\156\56\143\x73\163\x3f\166\145\162\163\x69\x6f\x6e\x3d\64\56\70", array(), $LB = null, $KK = false);
        wp_enqueue_style("\155\x6f\x2d\167\160\55\146\157\x6e\164\55\x61\x77\x65\163\x6f\155\145", MOC_URL . "\x72\x65\163\x6f\x75\162\143\145\163\x2f\x63\x73\163\x2f\146\x6f\x6e\x74\55\141\167\145\x73\x6f\155\145\56\143\163\x73\x3f\x76\145\162\163\151\x6f\x6e\75\x34\56\70", array(), $LB = null, $KK = false);
        if (!(isset($_REQUEST["\x74\x61\142"]) && "\154\x69\x63\145\156\163\151\156\147" === $_REQUEST["\x74\141\x62"])) {
            goto JF;
        }
        wp_enqueue_style("\x6d\157\137\157\x61\x75\x74\150\x5f\x62\x6f\157\164\x73\x74\x72\x61\x70\x5f\143\x73\x73", MOC_URL . "\x72\145\x73\x6f\x75\x72\x63\145\163\57\x63\163\x73\x2f\x62\x6f\x6f\x74\163\164\x72\x61\160\57\142\x6f\x6f\x74\x73\x74\x72\141\160\56\155\151\x6e\x2e\x63\x73\163", array(), $LB = null, $KK = false);
        wp_enqueue_style("\x6d\157\x5f\x6f\141\165\x74\x68\x5f\x6c\x69\x63\x65\x6e\163\145\x5f\x70\141\x67\145\x5f\163\x74\x79\x6c\x65", MOC_URL . "\x72\x65\163\x6f\165\162\143\145\x73\57\x63\x73\x73\x2f\x6d\x6f\55\157\x61\165\x74\x68\55\154\151\143\x65\x6e\163\x69\x6e\147\x2e\x63\163\163");
        JF:
    }
    public function plugin_settings_script()
    {
        wp_enqueue_script("\x6d\x6f\x5f\157\141\x75\x74\x68\x5f\141\144\x6d\151\x6e\x5f\163\x65\164\x74\151\x6e\147\x73\137\163\143\162\151\160\x74", MOC_URL . "\162\x65\163\157\165\x72\x63\x65\x73\x2f\x6a\x73\x2f\163\x65\164\x74\x69\156\147\163\56\152\x73", array(), $LB = null, $KK = false);
        wp_enqueue_script("\x6d\x6f\x5f\157\141\165\164\x68\137\141\144\155\x69\156\x5f\x73\145\164\x74\x69\x6e\147\x73\x5f\160\150\157\156\145\x5f\163\x63\162\151\x70\164", MOC_URL . "\162\x65\163\157\x75\162\x63\145\163\57\152\x73\57\x70\150\x6f\156\145\56\152\x73", array(), $LB = null, $KK = false);
        wp_enqueue_script("\155\x6f\x5f\157\141\x75\164\x68\x5f\141\x64\x6d\x69\156\137\x73\x65\x74\x74\151\x6e\x67\x73\x5f\144\141\164\x61\x74\141\x62\x6c\x65", MOC_URL . "\x72\x65\x73\157\x75\162\x63\x65\163\57\x6a\163\x2f\x6a\161\165\x65\162\171\x2e\x64\141\x74\x61\124\x61\x62\154\x65\163\56\155\x69\x6e\x2e\x6a\163", array(), $LB = null, $KK = false);
        if (!(isset($_REQUEST["\x74\141\142"]) && "\x6c\151\x63\145\x6e\163\151\x6e\x67" === $_REQUEST["\164\x61\142"])) {
            goto qM;
        }
        wp_enqueue_script("\x6d\x6f\x5f\x6f\x61\165\164\150\137\x6d\157\x64\x65\162\156\x69\x7a\162\x5f\x73\143\162\x69\160\164", MOC_URL . "\x72\x65\163\157\x75\x72\x63\x65\x73\57\152\163\57\155\157\x64\145\162\156\151\x7a\162\x2e\x6a\163", array(), $LB = null, $KK = true);
        wp_enqueue_script("\x6d\157\x5f\x6f\141\165\x74\x68\x5f\160\157\x70\x6f\166\x65\x72\137\x73\143\x72\x69\x70\164", MOC_URL . "\162\x65\163\x6f\165\x72\143\145\163\x2f\152\163\x2f\142\x6f\157\164\x73\x74\x72\x61\160\x2f\x70\157\160\160\x65\162\x2e\155\151\156\56\152\x73", array(), $LB = null, $KK = true);
        wp_enqueue_script("\155\x6f\137\157\x61\x75\164\150\x5f\x62\x6f\157\x74\x73\164\162\x61\160\x5f\x73\143\162\x69\160\164", MOC_URL . "\x72\145\x73\x6f\165\x72\x63\x65\163\57\x6a\x73\57\142\x6f\x6f\x74\163\164\x72\141\160\57\x62\157\157\164\163\164\x72\x61\x70\56\x6d\x69\156\56\152\163", array(), $LB = null, $KK = true);
        qM:
    }
    public function load_current_tab($WA)
    {
        global $mx;
        $aL = 0 === $mx->get_versi();
        $Fo = false;
        if ($aL) {
            goto AW;
        }
        $Fo = $mx->mo_oauth_client_get_option("\155\157\137\x6f\141\x75\x74\150\137\143\154\x69\145\156\x74\137\x6c\x6f\141\x64\x5f\x61\156\x61\x6c\x79\164\x69\x63\x73");
        $Fo = boolval($Fo) ? boolval($Fo) : false;
        $aL = $mx->check_versi(1) && $mx->mo_oauth_is_clv();
        AW:
        if ("\x61\143\x63\x6f\165\156\164" === $WA || !$aL) {
            goto oB;
        }
        if ("\x63\165\163\164\x6f\x6d\151\x7a\x61\164\151\x6f\x6e" === $WA && $aL) {
            goto LW;
        }
        if ("\163\x69\147\x6e\x69\x6e\163\x65\164\164\151\x6e\147\163" === $WA && $aL) {
            goto Hn;
        }
        if ("\x73\165\142\163\x69\x74\x65\163\145\164\x74\151\x6e\x67\163" === $WA && $aL) {
            goto X0;
        }
        if ($Fo && "\141\x6e\x61\x6c\171\164\x69\x63\x73" === $WA && $aL) {
            goto qN;
        }
        if ("\x6c\x69\x63\145\x6e\163\151\156\x67" === $WA) {
            goto qf;
        }
        if ("\162\x65\161\165\145\x73\x74\146\157\162\x64\x65\155\157" === $WA && $aL) {
            goto I9;
        }
        if ("\141\x64\x64\x6f\x6e\163" === $WA) {
            goto Ge;
        }
        $this->instance_helper->get_clientappui_instance()->render_free_ui();
        goto UJ;
        oB:
        $O8 = $this->instance_helper->get_accounts_instance();
        if ($mx->mo_oauth_client_get_option("\x76\145\162\151\146\171\137\143\x75\x73\x74\x6f\x6d\145\x72") === "\x74\x72\x75\145") {
            goto Vg;
        }
        if (trim($mx->mo_oauth_client_get_option("\155\157\137\x6f\x61\x75\164\x68\137\x61\144\155\x69\156\x5f\x65\155\141\x69\x6c")) !== '' && trim($mx->mo_oauth_client_get_option("\155\x6f\137\157\x61\x75\164\x68\137\x61\x64\155\151\156\137\141\x70\151\137\153\145\x79")) === '' && $mx->mo_oauth_client_get_option("\x6e\x65\x77\137\162\145\147\151\x73\164\162\141\x74\x69\157\156") !== "\x74\162\x75\x65") {
            goto a3;
        }
        if (!$mx->mo_oauth_is_clv() && $mx->check_versi(1) && $mx->mo_oauth_is_customer_registered()) {
            goto xT;
        }
        $O8->register();
        goto VT;
        Vg:
        $O8->verify_password_ui();
        goto VT;
        a3:
        $O8->verify_password_ui();
        goto VT;
        xT:
        $O8->mo_oauth_lp();
        VT:
        goto UJ;
        LW:
        $this->instance_helper->get_customization_instance()->render_free_ui();
        goto UJ;
        Hn:
        $this->instance_helper->get_sign_in_settings_instance()->render_free_ui();
        goto UJ;
        X0:
        $this->instance_helper->get_subsite_settings()->render_ui();
        goto UJ;
        qN:
        $this->instance_helper->get_user_analytics()->render_ui();
        goto UJ;
        qf:
        (new Licensing())->show_licensing_page();
        goto UJ;
        I9:
        $this->instance_helper->get_requestdemo_instance()->render_free_ui();
        goto UJ;
        Ge:
        (new MoAddons())->addons_page();
        UJ:
    }
}
