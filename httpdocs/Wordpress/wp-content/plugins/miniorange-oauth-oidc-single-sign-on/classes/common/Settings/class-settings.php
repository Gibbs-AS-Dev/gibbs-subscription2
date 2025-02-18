<?php


namespace MoOauthClient;

use MoOauthClient\Backup\BackupHandler;
use MoOauthClient\mc_utils;
use MoOauthClient\Customer;
use MoOauthClient\Config;
class Settings
{
    public $config;
    public $util;
    public function __construct()
    {
        global $mx;
        $this->util = $mx;
        add_action("\x61\144\155\151\156\x5f\x69\x6e\151\x74", array($this, "\x6d\x69\x6e\x69\x6f\x72\141\156\x67\x65\x5f\157\141\165\164\150\137\163\141\x76\145\137\163\145\x74\164\x69\x6e\x67\163"));
        add_shortcode("\155\x6f\137\157\x61\165\164\150\x5f\154\157\147\151\x6e", array($this, "\x6d\x6f\137\x6f\x61\x75\x74\150\137\x73\x68\x6f\x72\164\143\x6f\144\145\137\154\x6f\x67\x69\156"));
        add_action("\141\144\155\x69\x6e\137\x69\x6e\151\x74", array($this, "\x6d\x6f\137\x6f\x61\165\164\150\x5f\144\145\x62\x75\147\x5f\154\157\x67\x5f\141\x6a\x61\170\x5f\x68\157\x6f\153"));
        $this->config = $this->util->get_plugin_config();
    }
    function mo_oauth_debug_log_ajax_hook()
    {
        add_action("\x77\x70\x5f\141\x6a\141\170\x5f\155\157\x5f\x6f\141\x75\164\x68\x5f\144\x65\x62\165\x67\x5f\x61\x6a\141\170", array($this, "\155\157\x5f\x6f\x61\x75\164\x68\137\x64\145\x62\165\x67\x5f\x6c\157\x67\x5f\141\x6a\x61\170"));
    }
    function mo_oauth_debug_log_ajax()
    {
        if (!isset($_POST["\155\157\137\157\x61\x75\164\x68\x5f\156\x6f\x6e\143\145"]) || !wp_verify_nonce(sanitize_text_field($_POST["\x6d\157\x5f\x6f\141\165\x74\150\137\x6e\x6f\x6e\x63\x65"]), "\155\157\55\157\x61\x75\164\x68\x2d\104\145\142\165\x67\x2d\x6c\x6f\x67\x73\x2d\165\x6e\151\161\x75\145\x2d\x73\x74\162\x69\156\x67\55\156\157\x6e\x63\x65")) {
            goto II;
        }
        switch (sanitize_text_field($_POST["\155\x6f\137\x6f\141\x75\164\x68\137\157\160\164\x69\157\156"])) {
            case "\x6d\157\137\x6f\141\x75\164\150\137\x72\x65\163\145\x74\x5f\x64\145\x62\165\x67":
                $this->mo_oauth_reset_debug();
                goto be;
        }
        VX:
        be:
        goto q9;
        II:
        wp_send_json("\145\x72\x72\x6f\162");
        q9:
    }
    public function mo_oauth_reset_debug()
    {
        global $mx;
        if (isset($_POST["\x6d\x6f\137\157\141\165\x74\150\137\157\x70\x74\x69\x6f\x6e"]) and sanitize_text_field(wp_unslash($_POST["\x6d\x6f\137\157\x61\x75\164\x68\137\157\160\164\x69\157\156"])) == "\x6d\x6f\x5f\x6f\141\165\164\x68\x5f\x72\x65\163\x65\164\137\x64\145\142\x75\x67" && isset($_REQUEST["\155\157\137\157\x61\x75\x74\x68\137\x6e\x6f\156\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST["\155\x6f\137\157\x61\x75\x74\150\x5f\x6e\157\156\143\145"])), "\x6d\157\55\157\141\x75\164\150\55\x44\x65\142\165\147\x2d\x6c\157\x67\163\x2d\165\156\x69\x71\x75\145\x2d\x73\x74\162\x69\x6e\147\55\x6e\157\x6e\x63\145")) {
            goto k7;
        }
        echo "\145\162\x72\157\x72";
        goto V0;
        k7:
        $KW = false;
        if (!isset($_POST["\155\x6f\x5f\x6f\141\x75\x74\150\137\155\157\x5f\x6f\141\x75\x74\x68\137\144\145\x62\165\x67\x5f\x63\150\145\x63\x6b"])) {
            goto ub;
        }
        $KW = sanitize_text_field($_POST["\x6d\x6f\x5f\157\x61\165\x74\x68\x5f\x6d\x6f\137\157\141\x75\164\150\137\x64\x65\142\165\147\x5f\x63\150\x65\143\x6b"]);
        ub:
        $I2 = current_time("\164\151\x6d\145\x73\x74\x61\155\160");
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\x64\x65\x62\165\x67\x5f\145\156\x61\x62\x6c\145", $KW);
        if (!$mx->mo_oauth_client_get_option("\155\x6f\x5f\144\145\142\165\147\x5f\x65\156\x61\142\x6c\145")) {
            goto UI;
        }
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\144\145\142\x75\x67\x5f\143\150\145\143\153", 1);
        $mx->mo_oauth_client_update_option("\155\157\x5f\144\x65\142\165\147\137\164\151\x6d\145", $I2);
        UI:
        if (!$mx->mo_oauth_client_get_option("\155\x6f\137\144\x65\x62\x75\147\x5f\145\156\x61\x62\x6c\145")) {
            goto Cc;
        }
        $mx->mo_oauth_client_update_option("\x6d\157\137\157\x61\165\x74\x68\137\x64\x65\x62\165\x67", "\155\157\137\x6f\x61\165\164\150\137\144\145\x62\x75\x67" . uniqid());
        $fW = $mx->mo_oauth_client_get_option("\x6d\157\137\x6f\141\165\164\150\x5f\x64\145\x62\165\x67");
        $VB = dirname(__DIR__) . DIRECTORY_SEPARATOR . "\117\x41\165\164\150\x48\x61\156\144\x6c\145\162" . DIRECTORY_SEPARATOR . $fW . "\56\x6c\157\x67";
        $ot = fopen($VB, "\x77");
        chmod($VB, 0644);
        $mx->mo_oauth_client_update_option("\155\157\137\x64\x65\142\165\x67\x5f\143\150\145\x63\x6b", 1);
        MO_Oauth_Debug::mo_oauth_log('');
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\144\x65\142\x75\x67\x5f\143\150\145\x63\x6b", 0);
        Cc:
        $Cu = $mx->mo_oauth_client_get_option("\155\x6f\x5f\x64\145\x62\165\147\x5f\145\x6e\x61\x62\x6c\145");
        $zF["\163\x77\151\x74\x63\150\x5f\163\164\141\164\165\163"] = $Cu;
        wp_send_json($zF);
        V0:
    }
    public function miniorange_oauth_save_settings()
    {
        global $mx;
        $n2 = $mx->get_plugin_config()->get_current_config();
        $Wz = "\144\x69\163\x61\142\154\x65\x64";
        if (empty($n2["\x6d\157\x5f\x64\x74\145\137\163\x74\141\x74\145"])) {
            goto Jj;
        }
        $Wz = $mx->mooauthdecrypt($n2["\x6d\x6f\x5f\144\164\145\137\x73\x74\141\x74\145"]);
        Jj:
        if (!(isset($_POST["\x63\x68\141\156\147\145\137\x6d\151\156\x69\x6f\162\141\x6e\x67\x65\x5f\x6e\157\x6e\143\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x63\x68\x61\156\147\145\137\155\151\156\151\157\162\x61\156\147\x65\137\x6e\x6f\156\x63\x65"])), "\x63\150\x61\156\147\x65\137\155\x69\x6e\151\x6f\162\x61\156\x67\x65") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x63\150\141\x6e\147\145\137\155\x69\156\x69\157\162\x61\x6e\x67\145" === $_POST[\MoOAuthConstants::OPTION])) {
            goto jZ;
        }
        mo_oauth_deactivate();
        return;
        jZ:
        if (!(isset($_POST["\x6d\x6f\x5f\x6f\141\165\x74\x68\x5f\x65\x6e\x61\x62\154\x65\x5f\x64\145\x62\165\147\137\144\157\x77\x6e\154\x6f\141\x64\x5f\x6e\157\156\x63\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\157\137\157\141\x75\164\x68\137\145\x6e\141\142\x6c\145\x5f\144\x65\142\165\x67\137\144\x6f\167\x6e\154\x6f\141\144\137\x6e\x6f\x6e\143\145"])), "\155\157\x5f\157\x61\x75\164\x68\137\145\156\141\x62\154\145\x5f\144\145\142\165\x67\x5f\144\157\x77\156\x6c\157\x61\x64") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\157\x5f\x6f\x61\165\164\150\x5f\x65\x6e\141\142\x6c\x65\137\x64\145\x62\x75\x67\137\144\x6f\167\x6e\154\157\141\x64" === $_POST[\MoOAuthConstants::OPTION])) {
            goto qc;
        }
        $kD = plugin_dir_path(__FILE__) . "\57\56\56\57\x4f\x41\x75\x74\x68\110\141\x6e\x64\x6c\x65\x72\57" . $mx->mo_oauth_client_get_option("\155\x6f\137\157\141\165\x74\150\x5f\x64\x65\x62\x75\147") . "\56\154\157\147";
        if (is_file($kD)) {
            goto MA;
        }
        echo "\64\60\x34\x20\x46\151\x6c\145\40\x6e\x6f\164\x20\x66\x6f\165\156\x64\41";
        exit;
        MA:
        $LX = filesize($kD);
        $VS = basename($kD);
        $bQ = strtolower(pathinfo($VS, PATHINFO_EXTENSION));
        $GR = "\x61\160\x70\154\151\x63\x61\164\151\x6f\x6e\x2f\x66\157\162\x63\x65\55\144\x6f\x77\x6e\154\157\141\144";
        if (!ob_get_contents()) {
            goto oE;
        }
        ob_clean();
        oE:
        header("\x50\x72\141\147\155\x61\72\40\x70\165\142\x6c\151\x63");
        header("\105\x78\160\151\x72\145\163\x3a\40\60");
        header("\103\x61\x63\x68\x65\55\x43\x6f\x6e\x74\162\157\x6c\72\40\x6d\165\163\164\55\x72\145\166\x61\154\151\144\x61\164\145\x2c\x20\160\157\x73\164\55\x63\x68\145\x63\x6b\75\x30\x2c\40\160\x72\145\55\x63\x68\145\x63\x6b\x3d\x30");
        header("\103\x61\x63\150\x65\55\103\x6f\x6e\164\x72\157\154\72\40\160\x75\142\x6c\151\143");
        header("\103\x6f\x6e\x74\x65\156\x74\x2d\x44\x65\x73\x63\x72\x69\160\x74\x69\157\x6e\x3a\40\106\151\x6c\145\40\x54\162\x61\x6e\163\x66\x65\162");
        header("\103\x6f\x6e\x74\145\x6e\164\55\124\171\x70\145\x3a\40{$GR}");
        $i0 = "\x43\157\156\164\x65\156\164\x2d\104\x69\x73\x70\157\163\151\x74\151\x6f\x6e\x3a\40\x61\x74\164\x61\x63\150\155\145\156\x74\73\40\x66\151\x6c\x65\x6e\141\x6d\x65\75" . $VS . "\x3b";
        header($i0);
        header("\x43\x6f\156\164\x65\156\x74\55\124\x72\x61\156\163\146\x65\162\55\x45\x6e\x63\x6f\x64\151\156\x67\x3a\x20\142\x69\x6e\141\x72\171");
        header("\103\157\x6e\164\x65\x6e\x74\55\114\145\156\x67\x74\x68\72\x20" . $LX);
        @readfile($kD);
        exit;
        qc:
        if (!(isset($_POST["\155\157\x5f\157\141\165\164\x68\x5f\143\154\145\141\x72\x5f\154\157\147\137\x6e\157\x6e\143\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\157\137\x6f\141\x75\164\150\137\x63\154\x65\141\x72\x5f\x6c\157\x67\137\x6e\x6f\x6e\143\145"])), "\155\157\137\157\x61\x75\164\x68\x5f\143\154\145\141\x72\137\154\x6f\x67") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\157\x5f\x6f\x61\x75\164\150\x5f\143\154\x65\x61\162\x5f\x6c\157\x67" === $_POST[\MoOAuthConstants::OPTION])) {
            goto Pp;
        }
        $kD = plugin_dir_path(__FILE__) . "\x2f\x2e\56\x2f\117\101\x75\164\x68\x48\x61\156\x64\154\145\162\x2f" . $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\157\x61\x75\164\x68\x5f\x64\145\142\x75\x67") . "\x2e\x6c\157\147";
        if (is_file($kD)) {
            goto dL;
        }
        echo "\64\x30\64\x20\x46\x69\154\145\x20\156\157\164\40\146\x6f\165\156\144\41";
        exit;
        dL:
        file_put_contents($kD, '');
        file_put_contents($kD, "\x54\x68\151\x73\40\x69\x73\40\164\150\145\x20\155\151\x6e\151\x4f\x72\141\156\x67\145\x20\117\101\165\164\x68\x20\x70\x6c\165\x67\x69\x6e\x20\104\145\x62\x75\x67\x20\x4c\x6f\x67\40\146\x69\x6c\145");
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x44\x65\x62\x75\x67\x20\x4c\157\147\163\x20\x63\x6c\145\141\x72\x65\144\40\163\165\x63\143\145\163\163\146\x75\154\x6c\171\x2e");
        $this->util->mo_oauth_show_success_message();
        Pp:
        if (!(isset($_POST["\155\157\x5f\157\141\x75\x74\150\137\x72\145\147\x69\x73\164\145\x72\137\x63\165\163\164\157\155\x65\x72\137\156\x6f\x6e\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\157\137\157\141\165\164\x68\x5f\x72\145\x67\151\x73\x74\145\x72\137\x63\165\x73\164\157\155\x65\162\137\x6e\157\156\x63\145"])), "\x6d\157\x5f\157\x61\x75\164\x68\137\162\x65\x67\151\x73\x74\145\x72\x5f\x63\165\x73\164\x6f\x6d\145\x72") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\x5f\x6f\x61\x75\x74\150\x5f\x72\145\x67\x69\163\164\x65\162\x5f\143\165\163\164\157\155\x65\x72" === $_POST[\MoOAuthConstants::OPTION])) {
            goto mv;
        }
        $UU = '';
        $rH = '';
        $oa = '';
        $dv = '';
        $dO = '';
        $NL = '';
        $qM = '';
        if (!($this->util->mo_oauth_check_empty_or_null($_POST["\x65\155\141\x69\154"]) || $this->util->mo_oauth_check_empty_or_null($_POST["\x70\141\163\x73\167\157\x72\x64"]) || $this->util->mo_oauth_check_empty_or_null($_POST["\x63\x6f\x6e\146\151\x72\x6d\x50\141\163\x73\167\157\x72\x64"]))) {
            goto Re;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\101\x6c\x6c\x20\164\150\x65\x20\146\151\x65\154\x64\163\40\141\162\x65\x20\162\x65\161\x75\151\x72\145\144\x2e\40\x50\154\145\x61\163\x65\x20\x65\x6e\164\x65\x72\x20\166\141\x6c\x69\x64\40\x65\156\164\x72\x69\x65\x73\x2e");
        $this->util->mo_oauth_show_error_message();
        return;
        Re:
        if (strlen($_POST["\x70\141\x73\163\x77\157\x72\x64"]) < 8 || strlen($_POST["\143\x6f\156\x66\x69\x72\x6d\x50\x61\x73\x73\167\157\162\x64"]) < 8) {
            goto JR;
        }
        $UU = sanitize_email($_POST["\145\x6d\x61\151\154"]);
        $rH = stripslashes($_POST["\x70\150\x6f\x6e\145"]);
        $oa = stripslashes($_POST["\x70\x61\x73\x73\x77\157\162\144"]);
        $dv = stripslashes($_POST["\x66\156\141\155\x65"]);
        $dO = stripslashes($_POST["\x6c\x6e\x61\x6d\x65"]);
        $NL = stripslashes($_POST["\143\157\155\160\x61\156\x79"]);
        $qM = stripslashes($_POST["\x63\x6f\156\x66\x69\x72\155\120\141\163\x73\167\157\162\x64"]);
        goto pl;
        JR:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\103\150\157\x6f\x73\x65\x20\141\x20\160\141\x73\163\167\x6f\162\144\40\x77\151\164\x68\x20\x6d\x69\156\151\155\x75\155\x20\x6c\x65\156\x67\x74\x68\x20\x38\x2e");
        $this->util->mo_oauth_show_error_message();
        return;
        pl:
        $this->util->mo_oauth_client_update_option("\155\157\x5f\x6f\141\x75\x74\x68\137\x61\144\x6d\151\156\x5f\145\155\141\x69\154", $UU);
        $this->util->mo_oauth_client_update_option("\155\x6f\x5f\157\141\165\x74\150\137\x61\144\155\151\x6e\137\160\x68\157\x6e\x65", $rH);
        $this->util->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\x61\165\164\x68\x5f\141\144\x6d\151\x6e\x5f\x66\156\x61\155\145", $dv);
        $this->util->mo_oauth_client_update_option("\155\x6f\137\157\141\x75\164\x68\x5f\141\144\155\x69\x6e\137\154\156\x61\x6d\x65", $dO);
        $this->util->mo_oauth_client_update_option("\x6d\157\137\x6f\x61\x75\x74\x68\137\141\x64\155\x69\x6e\137\x63\157\x6d\x70\141\x6e\x79", $NL);
        if (!($this->util->mo_oauth_is_curl_installed() === 0)) {
            goto Mz;
        }
        return $this->util->mo_oauth_show_curl_error();
        Mz:
        if (strcmp($oa, $qM) === 0) {
            goto NW;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\120\x61\163\163\x77\157\x72\144\163\x20\144\x6f\40\156\x6f\164\x20\155\x61\164\143\150\x2e");
        $this->util->mo_oauth_client_delete_option("\x76\x65\162\151\x66\x79\x5f\x63\165\163\164\x6f\155\145\x72");
        $this->util->mo_oauth_show_error_message();
        goto dY;
        NW:
        $this->util->mo_oauth_client_update_option("\160\141\x73\163\x77\x6f\x72\x64", $oa);
        $pZ = new Customer();
        $UU = $this->util->mo_oauth_client_get_option("\x6d\x6f\137\x6f\x61\x75\x74\150\x5f\x61\144\x6d\151\156\x5f\x65\x6d\141\151\x6c");
        $Bn = json_decode($pZ->check_customer(), true);
        if (strcasecmp($Bn["\163\x74\141\x74\x75\163"], "\x43\x55\x53\x54\x4f\x4d\x45\122\137\x4e\x4f\124\x5f\x46\x4f\x55\x4e\x44") === 0) {
            goto OH;
        }
        $this->mo_oauth_get_current_customer();
        goto iE;
        OH:
        $this->create_customer();
        iE:
        dY:
        mv:
        if (!(isset($_POST["\155\157\137\x6f\141\x75\164\150\137\166\x65\x72\151\x66\171\137\x63\x75\x73\x74\x6f\x6d\x65\162\x5f\x6e\x6f\x6e\x63\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\x6f\x5f\x6f\x61\165\x74\x68\137\166\x65\162\x69\146\x79\x5f\x63\165\x73\164\157\x6d\x65\x72\x5f\156\x6f\x6e\x63\x65"])), "\155\157\x5f\x6f\141\x75\x74\x68\x5f\x76\x65\162\151\146\x79\137\143\x75\x73\164\x6f\x6d\145\162") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\137\157\x61\x75\x74\x68\137\166\x65\162\x69\x66\x79\x5f\x63\x75\x73\x74\x6f\155\145\x72" === $_POST[\MoOAuthConstants::OPTION])) {
            goto fQ;
        }
        if (!($this->util->mo_oauth_is_curl_installed() === 0)) {
            goto jX;
        }
        return $this->util->mo_oauth_show_curl_error();
        jX:
        $UU = isset($_POST["\x65\155\x61\x69\x6c"]) ? sanitize_email(wp_unslash($_POST["\x65\x6d\x61\x69\154"])) : '';
        $oa = isset($_POST["\160\141\x73\x73\x77\x6f\162\144"]) ? $_POST["\160\x61\x73\163\x77\x6f\x72\x64"] : '';
        if (!($this->util->mo_oauth_check_empty_or_null($UU) || $this->util->mo_oauth_check_empty_or_null($oa))) {
            goto Wn;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\101\154\154\40\164\x68\145\x20\146\x69\x65\154\144\x73\40\141\162\x65\x20\162\145\161\165\x69\x72\x65\144\56\40\120\154\x65\141\163\145\40\x65\156\164\145\x72\x20\x76\x61\x6c\151\x64\40\x65\156\164\x72\151\145\x73\x2e");
        $this->util->mo_oauth_show_error_message();
        return;
        Wn:
        $this->util->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\141\x75\x74\x68\137\141\x64\x6d\x69\156\x5f\145\x6d\x61\x69\x6c", $UU);
        $this->util->mo_oauth_client_update_option("\160\141\x73\163\167\x6f\x72\144", $oa);
        $pZ = new Customer();
        $Bn = $pZ->get_customer_key();
        $Y5 = json_decode($Bn, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            goto nM;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\111\156\166\141\x6c\151\144\40\165\163\145\162\156\141\155\145\x20\157\162\x20\x70\141\x73\x73\x77\x6f\162\x64\56\x20\120\x6c\x65\141\163\x65\x20\x74\162\171\x20\x61\147\x61\151\x6e\x2e");
        $this->util->mo_oauth_show_error_message();
        goto tE;
        nM:
        $this->util->mo_oauth_client_update_option("\x6d\157\x5f\x6f\141\165\164\x68\x5f\x61\x64\155\151\156\x5f\x63\x75\x73\x74\157\x6d\x65\162\x5f\153\145\x79", $Y5["\151\144"]);
        $this->util->mo_oauth_client_update_option("\x6d\x6f\137\157\141\165\x74\150\137\x61\144\x6d\x69\156\137\x61\x70\151\137\153\x65\171", $Y5["\x61\x70\151\x4b\145\171"]);
        $this->util->mo_oauth_client_update_option("\143\165\x73\164\157\155\145\162\x5f\164\x6f\x6b\x65\x6e", $Y5["\x74\157\153\145\x6e"]);
        if (!isset($Dm["\160\150\x6f\x6e\145"])) {
            goto r1;
        }
        $this->util->mo_oauth_client_update_option("\x6d\x6f\x5f\x6f\141\165\x74\x68\x5f\141\144\155\x69\156\137\160\150\x6f\156\145", $Y5["\x70\x68\x6f\x6e\145"]);
        r1:
        $this->util->mo_oauth_client_delete_option("\160\x61\163\163\167\157\162\144");
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x43\165\x73\x74\x6f\155\145\x72\40\x72\x65\x74\x72\151\x65\x76\x65\x64\40\x73\165\x63\143\x65\163\x73\146\165\154\x6c\x79");
        $this->util->mo_oauth_client_delete_option("\x76\x65\x72\151\146\x79\x5f\x63\165\x73\x74\157\x6d\145\162");
        $this->util->mo_oauth_show_success_message();
        tE:
        fQ:
        if (!(isset($_POST["\155\x6f\137\157\x61\x75\x74\x68\137\143\x68\141\x6e\x67\145\137\145\x6d\141\151\x6c\x5f\x6e\x6f\156\x63\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\137\x6f\x61\x75\164\150\x5f\143\x68\x61\156\x67\x65\x5f\x65\x6d\x61\x69\x6c\137\x6e\157\x6e\x63\x65"])), "\x6d\157\137\157\x61\165\164\150\137\x63\x68\141\x6e\147\145\137\x65\155\141\x69\x6c") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\x6f\137\x6f\141\x75\x74\x68\x5f\x63\150\141\x6e\x67\145\137\145\x6d\141\x69\x6c" === $_POST[\MoOAuthConstants::OPTION])) {
            goto K2;
        }
        $this->util->mo_oauth_client_update_option("\x76\145\162\x69\146\171\137\x63\x75\x73\x74\157\x6d\145\162", '');
        $this->util->mo_oauth_client_update_option("\x6d\157\x5f\157\x61\165\x74\150\137\x72\145\147\x69\x73\164\162\141\x74\151\x6f\x6e\x5f\163\x74\141\164\x75\163", '');
        $this->util->mo_oauth_client_update_option("\156\145\x77\137\x72\x65\147\x69\163\x74\162\x61\164\151\157\156", "\164\162\165\145");
        K2:
        if (!(isset($_POST["\155\157\137\157\x61\165\x74\150\x5f\143\157\156\x74\141\143\164\137\x75\x73\x5f\161\165\145\x72\171\137\x6f\x70\x74\x69\x6f\156\137\x6e\157\x6e\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\x6f\x61\165\x74\150\137\143\157\x6e\x74\141\x63\x74\137\165\163\137\161\x75\x65\162\171\137\x6f\x70\164\x69\157\156\137\156\x6f\156\143\145"])), "\x6d\157\x5f\157\141\165\164\150\x5f\143\157\x6e\x74\x61\x63\x74\137\x75\x73\137\161\165\x65\162\171\137\x6f\160\164\151\157\156") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\157\137\157\x61\x75\x74\150\x5f\x63\157\156\164\x61\143\x74\x5f\165\163\137\x71\165\x65\x72\171\x5f\x6f\160\164\151\157\156" === $_POST[\MoOAuthConstants::OPTION])) {
            goto w4;
        }
        if (!($this->util->mo_oauth_is_curl_installed() === 0)) {
            goto WS;
        }
        return $this->util->mo_oauth_show_curl_error();
        WS:
        $UU = isset($_POST["\x6d\157\x5f\x6f\x61\x75\x74\x68\137\x63\157\156\x74\x61\143\x74\x5f\165\x73\x5f\145\x6d\141\151\x6c"]) ? sanitize_text_field(wp_unslash($_POST["\155\157\137\157\x61\x75\x74\x68\137\143\x6f\156\x74\x61\x63\x74\x5f\165\x73\137\145\155\141\151\x6c"])) : '';
        $rH = isset($_POST["\x6d\157\137\x6f\x61\165\x74\x68\x5f\143\x6f\156\164\x61\143\164\137\165\x73\x5f\160\x68\157\x6e\x65"]) ? sanitize_text_field(wp_unslash($_POST["\x6d\x6f\137\x6f\x61\x75\x74\x68\137\143\x6f\x6e\164\x61\x63\164\x5f\165\163\x5f\x70\150\x6f\156\145"])) : '';
        $mO = isset($_POST["\x6d\x6f\x5f\157\141\165\164\x68\x5f\143\157\x6e\x74\x61\143\x74\137\165\x73\x5f\x71\165\x65\x72\171"]) ? sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\x6f\x61\165\x74\x68\137\143\157\x6e\164\x61\x63\164\x5f\165\x73\137\x71\x75\145\x72\x79"])) : '';
        $mT = isset($_POST["\x6d\157\x5f\x6f\141\x75\164\x68\137\163\x65\156\144\x5f\160\x6c\x75\147\x69\156\137\x63\157\x6e\x66\151\x67"]);
        $pZ = new Customer();
        if ($this->util->mo_oauth_check_empty_or_null($UU) || $this->util->mo_oauth_check_empty_or_null($mO)) {
            goto e9;
        }
        $nX = $pZ->submit_contact_us($UU, $rH, $mO, $mT);
        if (false === $nX) {
            goto AZ;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\124\150\x61\x6e\153\x73\x20\146\157\x72\x20\x67\145\x74\164\151\x6e\147\40\151\x6e\40\164\x6f\165\143\x68\x21\x20\x57\145\x20\163\150\x61\154\154\x20\x67\x65\x74\x20\x62\141\x63\x6b\40\164\157\x20\x79\157\x75\x20\163\150\157\162\x74\x6c\171\56");
        $this->util->mo_oauth_show_success_message();
        goto vZ;
        AZ:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\165\162\x20\161\165\145\162\x79\x20\143\x6f\165\x6c\x64\40\156\x6f\x74\40\x62\145\40\163\165\142\155\x69\x74\164\145\x64\56\x20\120\154\x65\141\x73\145\40\x74\x72\x79\40\x61\147\x61\151\x6e\x2e");
        $this->util->mo_oauth_show_error_message();
        vZ:
        goto Ae;
        e9:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\x6c\145\x61\163\145\40\x66\x69\x6c\x6c\x20\x75\x70\40\x45\155\141\151\x6c\x20\x61\156\144\40\x51\165\145\x72\171\40\146\x69\x65\154\144\x73\40\x74\x6f\40\163\165\142\x6d\151\x74\x20\x79\157\165\x72\40\x71\x75\145\x72\171\x2e");
        $this->util->mo_oauth_show_error_message();
        Ae:
        w4:
        if (!(isset($_POST["\x6d\157\137\x6f\141\x75\x74\x68\137\x63\157\156\x74\141\143\x74\x5f\165\x73\x5f\x71\165\x65\162\171\137\157\160\x74\x69\x6f\x6e\x5f\x75\160\x67\x72\x61\x64\x65\137\156\x6f\156\x63\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\x6d\x6f\137\x6f\141\165\164\150\137\143\x6f\x6e\164\x61\x63\164\x5f\165\163\137\x71\165\x65\x72\x79\x5f\157\160\164\151\157\156\137\x75\x70\x67\162\x61\x64\145\x5f\156\157\156\x63\x65"])), "\x6d\157\x5f\x6f\x61\x75\164\150\x5f\143\157\x6e\164\141\143\164\137\165\163\137\x71\165\x65\x72\171\x5f\x6f\x70\164\151\x6f\x6e\x5f\165\160\x67\x72\141\144\145") && isset($_POST[\MoOAuthConstants::OPTION]) && "\155\157\137\x6f\141\x75\x74\x68\x5f\143\x6f\156\164\x61\x63\x74\x5f\165\163\137\161\x75\145\162\171\x5f\x6f\x70\164\151\157\156\137\165\160\147\162\x61\x64\x65" === $_POST[\MoOAuthConstants::OPTION])) {
            goto t4;
        }
        if (!($this->util->mo_oauth_is_curl_installed() === 0)) {
            goto S3;
        }
        return $this->util->mo_oauth_show_curl_error();
        S3:
        $UU = isset($_POST["\155\157\x5f\157\141\x75\x74\x68\137\x63\x6f\x6e\164\141\143\164\137\165\163\137\145\x6d\x61\x69\x6c"]) ? sanitize_text_field(wp_unslash($_POST["\x6d\x6f\x5f\157\x61\x75\x74\150\x5f\x63\157\x6e\164\x61\x63\x74\137\165\x73\x5f\145\x6d\x61\x69\x6c"])) : '';
        $r8 = isset($_POST["\155\x6f\137\x6f\141\x75\x74\150\x5f\143\165\x72\x72\145\156\164\137\x76\145\x72\x73\x69\x6f\x6e"]) ? sanitize_text_field(wp_unslash($_POST["\x6d\x6f\137\x6f\141\165\x74\x68\x5f\143\x75\x72\162\x65\156\x74\x5f\166\145\x72\x73\x69\157\156"])) : '';
        $WG = isset($_POST["\x6d\x6f\x5f\x6f\141\165\x74\150\137\165\x70\147\162\141\x64\x69\x6e\147\x5f\164\x6f\137\x76\145\162\163\151\x6f\x6e"]) ? sanitize_text_field(wp_unslash($_POST["\x6d\157\137\x6f\x61\165\x74\x68\137\x75\160\x67\162\x61\144\x69\x6e\147\x5f\164\157\137\x76\145\x72\x73\x69\157\156"])) : '';
        $Dx = isset($_POST["\x6d\157\x5f\146\x65\x61\164\x75\162\145\163\x5f\x72\x65\161\165\151\x72\145\x64"]) ? sanitize_text_field(wp_unslash($_POST["\155\x6f\137\x66\x65\x61\x74\165\x72\145\x73\137\162\x65\x71\x75\151\162\x65\144"])) : '';
        $pZ = new Customer();
        if ($this->util->mo_oauth_check_empty_or_null($UU)) {
            goto uJ;
        }
        $nX = $pZ->submit_contact_us_upgrade($UU, $r8, $WG, $Dx);
        if (false === $nX) {
            goto Fv;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\124\150\x61\x6e\x6b\163\40\146\157\x72\x20\x67\x65\x74\x74\151\x6e\x67\40\x69\156\x20\164\157\165\x63\150\x21\x20\127\x65\x20\x73\x68\x61\x6c\x6c\40\x67\145\x74\40\x62\x61\x63\x6b\40\x74\x6f\40\171\157\x75\x20\x73\x68\x6f\162\164\x6c\x79\x20\167\151\x74\x68\x20\x71\x75\x6f\x74\141\x74\151\x6f\156");
        $this->util->mo_oauth_show_success_message();
        goto Gu;
        Fv:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x59\157\x75\162\x20\x71\x75\145\162\x79\40\x63\x6f\165\x6c\x64\40\156\157\x74\x20\142\145\40\x73\165\x62\155\151\164\x74\145\144\x2e\40\x50\154\x65\141\x73\145\40\164\162\171\x20\x61\x67\x61\x69\x6e\x2e");
        $this->util->mo_oauth_show_error_message();
        Gu:
        goto a4;
        uJ:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x50\x6c\145\x61\163\145\x20\x66\x69\154\x6c\x20\x75\x70\x20\105\155\x61\151\154\x20\146\151\145\154\144\x20\164\x6f\40\x73\x75\x62\155\x69\164\40\x79\x6f\x75\x72\x20\161\165\145\162\171\x2e");
        $this->util->mo_oauth_show_error_message();
        a4:
        t4:
        if (!($Wz == "\144\x69\163\141\142\x6c\x65\144")) {
            goto oC;
        }
        if (!(isset($_POST["\x6d\x6f\x5f\x6f\141\x75\164\150\137\162\145\163\x74\157\162\145\x5f\x62\141\x63\153\x75\160\137\156\x6f\156\x63\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\157\141\165\164\150\x5f\x72\x65\163\164\157\162\145\x5f\x62\141\x63\153\x75\x70\137\156\157\156\x63\x65"])), "\155\x6f\x5f\x6f\141\165\164\x68\x5f\162\145\x73\164\157\x72\145\x5f\142\141\x63\153\165\160") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\157\x5f\x6f\x61\x75\164\x68\x5f\x72\145\163\164\x6f\162\145\137\x62\x61\x63\153\165\160" === $_POST[\MoOAuthConstants::OPTION])) {
            goto Fx;
        }
        $l5 = "\x54\150\x65\162\x65\x20\167\141\x73\x20\141\x6e\x20\x65\162\x72\157\162\40\165\160\154\157\x61\x64\x69\x6e\x67\x20\x74\x68\x65\40\x66\151\x6c\145";
        if (isset($_FILES["\155\157\137\157\x61\x75\164\x68\x5f\x63\x6c\x69\145\156\x74\137\142\x61\143\153\x75\x70"])) {
            goto FY;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, $l5);
        $this->util->mo_oauth_show_error_message();
        return;
        FY:
        if (!function_exists("\x77\x70\137\150\141\x6e\144\154\145\x5f\x75\x70\154\157\141\x64")) {
            require_once ABSPATH . "\167\x70\x2d\141\144\155\151\156\57\x69\x6e\x63\154\x75\144\145\163\57\x66\x69\154\145\56\x70\x68\160";
        }
        $gA = $_FILES["\x6d\x6f\x5f\157\x61\x75\x74\x68\x5f\143\x6c\x69\x65\156\x74\137\x62\141\x63\x6b\x75\x70"];
        if (!(!isset($gA["\x65\162\x72\x6f\x72"]) || is_array($gA["\145\162\x72\x6f\162"]) || UPLOAD_ERR_OK !== $gA["\145\x72\162\x6f\162"])) {
            goto QU;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, $l5 . "\72\40" . json_encode($gA["\x65\x72\x72\157\162"], JSON_UNESCAPED_SLASHES));
        $this->util->mo_oauth_show_error_message();
        return;
        QU:
        $mE = new \finfo(FILEINFO_MIME_TYPE);
        $u5 = array_search($mE->file($gA["\164\155\160\137\156\141\x6d\x65"]), array("\164\145\x78\164" => "\x74\145\x78\x74\57\160\154\x61\151\x6e", "\x6a\163\157\x6e" => "\x61\x70\160\154\151\143\x61\164\x69\157\x6e\57\x6a\163\x6f\x6e"), true);
        $WT = explode("\x2e", $gA["\156\141\x6d\x65"]);
        $WT = $WT[count($WT) - 1];
        if (!(false === $u5 || $WT !== "\152\163\157\x6e")) {
            goto Iu;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, $l5 . "\x3a\x20\x49\156\166\x61\x6c\151\144\x20\106\x69\154\x65\x20\106\157\x72\155\141\164\x2e");
        $this->util->mo_oauth_show_error_message();
        return;
        Iu:
        $pV = file_get_contents($gA["\x74\x6d\x70\137\156\141\x6d\x65"]);
        $n2 = json_decode($pV, true);
        if (!(json_last_error() !== JSON_ERROR_NONE)) {
            goto tB;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, $l5 . "\72\x20\x49\156\x76\141\154\x69\144\40\x46\x69\x6c\145\40\x46\157\x72\155\x61\x74\56");
        $this->util->mo_oauth_show_error_message();
        return;
        tB:
        $kM = BackupHandler::restore_settings($n2);
        if (!$kM) {
            goto Qr;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x53\145\164\164\151\x6e\x67\163\x20\x72\145\163\x74\157\x72\145\144\40\163\x75\143\x63\145\163\x73\146\165\154\x6c\171\56");
        $this->util->mo_oauth_show_success_message();
        return;
        Qr:
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x54\x68\145\162\145\x20\x77\x61\x73\40\x61\x6e\40\x69\x73\163\x75\x65\x20\x77\150\151\x6c\145\x20\x72\145\163\164\x6f\x72\151\x6e\x67\x20\x74\x68\x65\40\143\157\156\146\151\147\165\162\x61\164\151\157\156\x2e");
        $this->util->mo_oauth_show_error_message();
        return;
        Fx:
        if (!(isset($_POST["\155\157\137\x6f\141\165\164\150\x5f\x64\157\167\x6e\154\x6f\141\x64\x5f\142\x61\143\x6b\x75\x70\137\156\157\156\143\x65"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\x6f\x5f\157\x61\165\x74\150\137\144\x6f\167\x6e\154\x6f\141\x64\x5f\x62\x61\x63\153\x75\x70\x5f\156\157\x6e\143\145"])), "\x6d\x6f\x5f\x6f\141\165\164\x68\x5f\x64\157\167\x6e\x6c\x6f\141\x64\x5f\142\141\143\153\x75\160") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\x6f\137\157\x61\165\x74\x68\x5f\x64\157\x77\x6e\x6c\157\x61\x64\x5f\x62\141\x63\153\x75\x70" === $_POST[\MoOAuthConstants::OPTION])) {
            goto Vl;
        }
        $oY = BackupHandler::get_backup_json();
        header("\103\x6f\x6e\x74\145\x6e\164\x2d\x54\x79\x70\x65\72\40\x61\x70\x70\x6c\x69\x63\141\164\x69\157\156\x2f\152\x73\x6f\156");
        header("\103\157\156\164\x65\x6e\164\x2d\104\151\163\x70\x6f\x73\151\164\x69\157\156\x3a\x20\141\164\x74\141\x63\x68\155\x65\x6e\x74\73\40\x66\151\154\145\156\141\x6d\x65\x3d\42\160\154\165\147\151\x6e\137\142\x61\143\x6b\x75\160\56\x6a\163\x6f\x6e\x22");
        header("\x43\x6f\156\164\145\156\164\x2d\x4c\x65\x6e\x67\164\150\72\40" . strlen($oY));
        header("\x43\x6f\156\156\x65\143\164\x69\157\156\x3a\x20\143\154\x6f\163\x65");
        echo $oY;
        exit;
        Vl:
        oC:
        do_action("\144\157\137\x6d\141\151\156\137\163\x65\164\x74\151\156\x67\x73\x5f\x69\156\164\145\162\156\x61\154", $_POST);
    }
    public function mo_oauth_get_current_customer()
    {
        $pZ = new Customer();
        $Bn = $pZ->get_customer_key();
        $Y5 = json_decode($Bn, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            goto ML;
        }
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\131\157\165\40\x61\154\x72\145\x61\x64\171\x20\x68\x61\x76\145\40\x61\x6e\x20\x61\143\x63\157\x75\x6e\x74\x20\167\151\x74\x68\40\155\x69\x6e\151\x4f\162\141\156\x67\145\x2e\40\120\x6c\x65\x61\163\x65\40\145\156\164\x65\x72\x20\x61\x20\x76\141\154\x69\144\40\x70\141\x73\x73\167\157\x72\144\56");
        $this->util->mo_oauth_client_update_option("\x76\x65\162\151\x66\x79\x5f\143\165\163\164\x6f\155\x65\162", "\164\162\165\145");
        $this->util->mo_oauth_show_error_message();
        goto O4;
        ML:
        $this->util->mo_oauth_client_update_option("\155\157\137\157\x61\165\x74\x68\137\x61\x64\x6d\151\x6e\x5f\x63\x75\163\164\157\x6d\145\162\137\x6b\x65\x79", $Y5["\x69\144"]);
        $this->util->mo_oauth_client_update_option("\x6d\x6f\x5f\157\x61\165\164\150\x5f\141\144\155\x69\x6e\137\x61\x70\x69\x5f\153\145\x79", $Y5["\141\160\151\x4b\145\171"]);
        $this->util->mo_oauth_client_update_option("\143\x75\163\x74\x6f\x6d\x65\162\x5f\164\157\x6b\x65\156", $Y5["\x74\x6f\x6b\x65\x6e"]);
        $this->util->mo_oauth_client_update_option("\x70\141\163\163\167\157\162\144", '');
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x43\x75\163\164\x6f\155\x65\162\x20\x72\x65\x74\162\x69\x65\x76\145\x64\40\163\x75\143\143\x65\163\x73\146\165\x6c\154\171");
        $this->util->mo_oauth_client_delete_option("\166\145\x72\x69\146\171\137\x63\165\163\164\157\155\x65\x72");
        $this->util->mo_oauth_client_delete_option("\156\145\x77\137\x72\145\x67\151\163\164\x72\141\164\151\157\x6e");
        $this->util->mo_oauth_show_success_message();
        O4:
    }
    public function create_customer()
    {
        global $mx;
        $pZ = new Customer();
        $Y5 = json_decode($pZ->create_customer(), true);
        if (strcasecmp($Y5["\x73\164\141\164\165\x73"], "\103\125\x53\x54\x4f\115\x45\x52\x5f\125\x53\x45\122\116\101\115\105\x5f\101\114\x52\x45\101\x44\131\x5f\105\x58\x49\123\x54\123") === 0) {
            goto Wq;
        }
        if (strcasecmp($Y5["\163\164\141\x74\165\163"], "\123\x55\x43\x43\x45\x53\123") === 0) {
            goto uW;
        }
        goto p5;
        Wq:
        $this->mo_oauth_get_current_customer();
        $this->util->mo_oauth_client_delete_option("\x6d\x6f\x5f\157\141\x75\x74\150\137\x6e\x65\167\137\x63\x75\163\x74\x6f\x6d\x65\162");
        goto p5;
        uW:
        $this->util->mo_oauth_client_update_option("\155\x6f\137\x6f\x61\165\164\150\137\x61\144\155\x69\x6e\137\143\165\163\164\157\x6d\145\162\x5f\153\145\x79", $Y5["\x69\144"]);
        $this->util->mo_oauth_client_update_option("\x6d\157\x5f\157\141\165\164\150\137\141\x64\155\151\156\x5f\141\160\x69\137\x6b\145\171", $Y5["\141\x70\151\x4b\x65\171"]);
        $this->util->mo_oauth_client_update_option("\x63\165\163\x74\157\155\145\x72\137\164\157\153\x65\x6e", $Y5["\164\x6f\x6b\x65\156"]);
        $this->util->mo_oauth_client_update_option("\x70\141\x73\163\167\x6f\x72\144", '');
        $this->util->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x52\145\x67\x69\x73\164\x65\162\x65\x64\40\x73\165\143\143\x65\163\163\x66\165\154\x6c\x79\56");
        $this->util->mo_oauth_client_update_option("\155\x6f\x5f\157\x61\x75\164\x68\x5f\162\x65\x67\x69\163\164\162\x61\164\151\157\156\x5f\163\x74\x61\164\165\x73", "\x4d\x4f\x5f\117\101\125\124\110\x5f\122\x45\107\111\x53\x54\x52\101\x54\111\x4f\116\137\103\x4f\115\x50\x4c\x45\124\105");
        $this->util->mo_oauth_client_update_option("\155\157\x5f\x6f\x61\x75\164\150\137\x6e\145\x77\x5f\143\165\163\x74\157\x6d\x65\x72", 1);
        $this->util->mo_oauth_client_delete_option("\166\x65\x72\151\x66\x79\x5f\x63\165\x73\x74\x6f\x6d\145\x72");
        $this->util->mo_oauth_client_delete_option("\x6e\145\x77\x5f\x72\145\147\x69\x73\164\162\x61\164\151\x6f\156");
        $this->util->mo_oauth_show_success_message();
        p5:
    }
}
