<?php


namespace MoOauthClient;

use MoOauthClient\Base\InstanceHelper;
use MoOauthClient\OauthHandler;
use MoOauthClient\StorageManager;
use MoOauthClient\MO_Custom_OAuth1;
class LoginHandler
{
    public $oauth_handler;
    public function __construct()
    {
        $this->oauth_handler = new OauthHandler();
        add_action("\151\x6e\151\164", array($this, "\155\x6f\x5f\157\141\165\164\x68\x5f\144\145\x63\x69\x64\x65\137\x66\154\157\x77"));
        add_action("\155\157\137\x6f\141\165\164\150\137\x63\154\x69\145\156\164\x5f\164\x69\147\x68\x74\137\154\157\x67\x69\156\137\151\x6e\x74\x65\162\x6e\x61\154", array($this, "\x68\x61\x6e\144\154\x65\137\x73\163\x6f"), 10, 4);
    }
    public function mo_oauth_decide_flow()
    {
        global $mx;
        if (!(isset($_REQUEST[\MoOAuthConstants::OPTION]) && "\x74\x65\163\164\x61\x74\164\162\x6d\141\x70\160\151\x6e\x67\143\x6f\156\146\151\147" === $_REQUEST[\MoOAuthConstants::OPTION])) {
            goto oT;
        }
        $qS = $_REQUEST["\141\160\160"];
        wp_safe_redirect(site_url() . "\x3f\157\x70\x74\151\157\x6e\x3d\157\x61\165\164\150\x72\x65\144\x69\162\x65\x63\164\x26\x61\160\160\x5f\156\141\155\145\75" . rawurlencode($qS) . "\x26\x74\x65\163\164\x3d\164\x72\x75\x65");
        exit;
        oT:
        $this->mo_oauth_login_validate();
    }
    public function mo_oauth_login_validate()
    {
        global $mx;
        $Nh = new StorageManager();
        if (!(isset($_REQUEST[\MoOAuthConstants::OPTION]) and strpos($_REQUEST[\MoOAuthConstants::OPTION], "\157\141\x75\x74\x68\162\145\144\151\162\145\x63\164") !== false)) {
            goto i1;
        }
        if (isset($_REQUEST["\155\x6f\137\154\157\x67\151\x6e\137\160\x6f\x70\x75\x70"])) {
            goto Fc;
        }
        if (!(isset($_REQUEST["\162\x65\163\157\x75\162\x63\x65"]) && !empty($_REQUEST["\x72\x65\163\157\165\x72\x63\145"]))) {
            goto FE;
        }
        if (!empty($_REQUEST["\162\145\163\x6f\x75\162\143\145"])) {
            goto Vy;
        }
        $mx->handle_error("\124\150\x65\x20\162\145\163\160\157\x6e\163\145\40\146\x72\157\155\40\165\x73\145\x72\151\x6e\x66\x6f\x20\x77\141\163\x20\145\155\160\164\x79\x2e");
        MO_Oauth_Debug::mo_oauth_log("\124\150\x65\40\162\x65\163\160\157\x6e\x73\x65\x20\146\x72\157\x6d\x20\x75\163\x65\162\x69\156\146\x6f\40\x77\x61\163\40\145\x6d\160\164\171\x2e");
        wp_die(wp_kses("\x54\x68\145\40\162\145\163\x70\157\x6e\x73\145\x20\146\162\157\155\x20\165\163\145\x72\x69\156\146\x6f\40\167\141\163\40\145\x6d\160\164\171\x2e", \mo_oauth_get_valid_html()));
        Vy:
        $Nh = new StorageManager(urldecode($_REQUEST["\162\145\x73\x6f\165\162\x63\145"]));
        $SR = $Nh->get_value("\x72\x65\x73\157\165\162\x63\x65");
        $vP = $Nh->get_value("\x61\160\x70\156\x61\x6d\145");
        $Bi = $Nh->get_value("\x72\x65\x64\151\x72\145\143\164\137\x75\162\151");
        $j6 = $Nh->get_value("\x61\x63\x63\145\163\x73\137\x74\157\x6b\x65\x6e");
        $xA = $mx->get_app_by_name($vP)->get_app_config();
        $cz = isset($_REQUEST["\164\145\x73\164"]) && !empty($_REQUEST["\164\x65\163\x74"]);
        if (!($cz && '' !== $cz)) {
            goto vo;
        }
        $this->handle_group_test_conf($SR, $xA, $j6, false, $cz);
        exit;
        vo:
        $Nh->remove_key("\x72\145\x73\x6f\165\x72\143\x65");
        $Nh->add_replace_entry("\160\x6f\160\165\160", "\151\147\156\157\162\145");
        if (!has_filter("\x77\x6f\157\x63\157\x6d\x6d\x65\x72\143\145\137\x63\x68\x65\x63\x6b\157\x75\x74\137\147\x65\164\x5f\x76\141\154\165\x65")) {
            goto WJ;
        }
        $SR["\141\160\x70\156\141\155\x65"] = $vP;
        WJ:
        do_action("\x6d\157\137\141\142\x72\x5f\146\151\154\164\x65\162\x5f\x6c\x6f\147\x69\x6e", $SR);
        $this->handle_sso($vP, $xA, $SR, $Nh->get_state(), ["\141\143\x63\145\x73\163\137\x74\157\x6b\145\156" => $j6]);
        FE:
        if (isset($_REQUEST["\141\x70\x70\137\x6e\x61\155\x65"])) {
            goto R7;
        }
        $l5 = "\120\x6c\145\x61\163\145\x20\143\x68\145\x63\x6b\40\x69\146\40\171\157\x75\40\141\162\145\x20\163\145\156\144\151\x6e\147\40\164\150\145\40\47\x61\160\160\137\156\141\x6d\145\47\x20\x70\141\x72\x61\x6d\145\164\145\x72";
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        R7:
        $bj = isset($_REQUEST["\141\x70\160\x5f\x6e\141\155\145"]) && !empty($_REQUEST["\141\160\x70\137\156\x61\x6d\x65"]) ? $_REQUEST["\141\x70\160\x5f\x6e\141\x6d\x65"] : '';
        if (!($bj == '')) {
            goto bb;
        }
        $l5 = "\x4e\157\x20\163\x75\x63\150\x20\141\x70\x70\40\x66\157\165\x6e\144\x20\x63\157\x6e\146\x69\147\x75\x72\145\x64\x2e\40\120\x6c\145\x61\163\x65\x20\143\x68\145\143\x6b\x20\x69\146\x20\171\157\165\x20\141\x72\x65\x20\163\145\156\x64\x69\156\147\x20\x74\x68\145\x20\143\x6f\162\x72\x65\x63\x74\40\141\x70\x70\137\156\141\x6d\145";
        MO_Oauth_Debug::mo_oauth_log($l5);
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        bb:
        $FO = $mx->mo_oauth_client_get_option("\155\157\137\157\141\x75\x74\150\137\141\160\x70\163\x5f\x6c\151\x73\164");
        if (is_array($FO) && isset($FO[$bj])) {
            goto KG;
        }
        $l5 = "\x4e\x6f\x20\x73\x75\143\150\x20\x61\160\160\x20\146\x6f\x75\x6e\x64\40\143\x6f\156\x66\x69\x67\165\x72\x65\x64\x2e\x20\x50\154\145\x61\x73\145\40\143\x68\x65\x63\x6b\x20\151\x66\40\x79\x6f\x75\40\x61\x72\145\x20\x73\x65\156\144\151\x6e\147\40\x74\x68\145\40\143\157\162\x72\145\x63\164\40\141\160\160\137\156\x61\155\x65";
        MO_Oauth_Debug::mo_oauth_log($l5);
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        KG:
        $XI = "\x2f\57" . $_SERVER["\x48\x54\x54\120\x5f\110\x4f\x53\x54"] . $_SERVER["\x52\x45\x51\125\x45\x53\x54\x5f\x55\122\x49"];
        $XI = strtok($XI, "\77");
        $a7 = isset($_REQUEST["\162\145\x64\x69\162\145\x63\x74\x5f\x75\162\154"]) ? urldecode($_REQUEST["\162\145\144\x69\x72\145\143\x74\137\x75\x72\x6c"]) : $XI;
        $cz = isset($_REQUEST["\164\x65\163\x74"]) ? urldecode($_REQUEST["\164\145\x73\x74"]) : false;
        $Ih = isset($_REQUEST["\x72\145\x73\164\162\x69\143\x74\162\145\x64\151\162\145\143\x74"]) ? urldecode($_REQUEST["\162\x65\163\x74\x72\151\x63\x74\162\x65\144\x69\162\145\x63\x74"]) : false;
        $Zy = $mx->get_app_by_name($bj);
        $xI = $Zy->get_app_config("\147\162\141\156\164\137\x74\x79\160\145");
        if (!is_multisite()) {
            goto Y5;
        }
        $blog_id = get_current_blog_id();
        $Fn = $mx->mo_oauth_client_get_option("\155\x6f\137\157\141\165\x74\150\137\143\x33\x56\x69\143\x32\154\x30\132\x58\116\172\x5a\x57\x78\154\131\63\122\x6c\x5a\x41");
        $JV = array();
        if (!isset($Fn)) {
            goto hW;
        }
        $JV = json_decode($mx->mooauthdecrypt($Fn), true);
        hW:
        $zw = false;
        $Uk = $mx->mo_oauth_client_get_option("\x6d\x6f\137\x6f\x61\x75\164\x68\x5f\x69\163\115\x75\x6c\164\151\x53\x69\164\145\x50\x6c\x75\147\151\x6e\122\145\x71\x75\145\163\x74\x65\144");
        if (!(is_array($JV) && in_array($blog_id, $JV))) {
            goto WC;
        }
        $zw = true;
        WC:
        if (!(is_multisite() && $Uk && ($Uk && !$zw) && !$cz && $mx->mo_oauth_client_get_option("\156\x6f\117\146\x53\165\142\x53\151\x74\x65\x73") < 1000)) {
            goto b7;
        }
        $mx->handle_error("\x4c\x6f\x67\151\156\40\x69\x73\40\144\151\163\141\x62\x6c\145\x64\x20\146\157\162\40\164\150\151\163\x20\x73\x69\164\145\x2e\x20\120\x6c\x65\x61\x73\145\40\x63\157\x6e\164\141\143\164\40\171\157\x75\x72\x20\x61\144\155\151\156\151\163\164\x72\x61\164\x6f\x72\56");
        MO_Oauth_Debug::mo_oauth_log("\x4c\157\147\151\x6e\x20\x69\x73\40\x64\151\x73\x61\x62\x6c\x65\x64\x20\146\x6f\x72\40\x74\x68\151\163\40\x73\x69\164\145\x2e\x20\x50\x6c\x65\x61\x73\145\40\x63\157\156\164\x61\x63\x74\x20\171\x6f\x75\162\x20\141\144\155\x69\156\151\x73\x74\162\x61\x74\157\162\x2e");
        wp_die("\114\x6f\x67\151\x6e\40\x69\163\x20\x64\x69\x73\x61\142\x6c\145\144\x20\146\157\x72\40\x74\150\x69\x73\40\x73\x69\164\145\x2e\x20\x50\154\145\141\x73\x65\40\x63\157\x6e\x74\141\x63\x74\x20\x79\x6f\165\162\x20\141\x64\x6d\x69\156\x69\x73\164\x72\141\x74\157\x72\56");
        b7:
        $Nh->add_replace_entry("\142\154\x6f\x67\x5f\151\x64", $blog_id);
        Y5:
        MO_Oauth_Debug::mo_oauth_log("\107\x72\x61\x6e\x74\72\x20" . $xI);
        if ($xI && "\x50\x61\163\163\167\x6f\x72\144\x20\x47\x72\x61\156\x74" === $xI) {
            goto Md;
        }
        if (!($xI && "\103\154\151\x65\156\164\x20\x43\162\x65\x64\x65\156\164\151\141\x6c\x73\40\107\x72\x61\x6e\164" === $xI)) {
            goto vT;
        }
        do_action("\155\157\137\x6f\141\x75\x74\150\137\143\x6c\151\x65\x6e\164\137\143\x72\145\x64\x65\156\164\151\x61\154\x73\137\147\x72\x61\x6e\164\x5f\x69\x6e\151\164\x69\x61\164\x65", $bj, $cz);
        exit;
        vT:
        goto yJ;
        Md:
        do_action("\160\x77\144\x5f\x65\163\163\x65\156\164\x69\x61\154\163\137\151\x6e\x74\145\x72\x6e\x61\154");
        do_action("\x6d\x6f\x5f\x6f\x61\165\x74\x68\137\143\154\x69\145\x6e\x74\137\141\144\144\137\x70\x77\144\x5f\152\163");
        echo "\x9\x9\x9\11\x3c\x73\x63\162\x69\x70\x74\x3e\15\xa\11\x9\x9\11\x9\x76\141\162\x20\x6d\x6f\x5f\157\x61\x75\x74\150\137\x61\160\160\x5f\156\x61\x6d\x65\40\x3d\x20\x22";
        echo wp_kses($bj, \mo_oauth_get_valid_html());
        echo "\x22\x3b\15\12\x9\11\11\11\11\x64\x6f\143\x75\x6d\145\156\164\x2e\x61\144\x64\105\x76\x65\x6e\x74\x4c\151\163\164\x65\156\145\x72\50\x27\x44\x4f\x4d\x43\x6f\156\x74\145\156\x74\x4c\157\x61\x64\x65\x64\x27\54\x20\146\x75\x6e\x63\x74\151\x6f\156\x28\51\40\173\15\xa\x9\x9\11\11\x9\x9";
        if ($cz) {
            goto Yj;
        }
        echo "\x9\x9\x9\x9\11\11\11\155\157\x4f\101\165\164\x68\114\x6f\x67\x69\x6e\120\x77\x64\x28\155\x6f\137\157\141\x75\x74\150\x5f\x61\x70\x70\137\x6e\x61\155\145\54\x20\146\141\154\x73\145\54\x20\x27";
        echo $a7;
        echo "\47\51\73\15\12\x9\x9\11\11\x9\x9";
        goto lb;
        Yj:
        echo "\x9\x9\x9\x9\11\x9\11\x6d\x6f\117\101\165\164\x68\x4c\157\x67\x69\156\120\x77\144\x28\x6d\157\x5f\157\x61\165\x74\150\x5f\x61\160\x70\x5f\156\141\155\145\x2c\40\164\162\165\x65\54\x20\x27";
        echo $a7;
        echo "\x27\51\73\15\12\11\x9\11\x9\11\x9";
        lb:
        echo "\11\11\x9\11\11\x7d\x2c\40\146\x61\154\x73\x65\x29\x3b\xd\xa\x9\x9\x9\11\x3c\57\x73\x63\162\x69\x70\164\76\15\xa\11\11\x9\11";
        exit;
        yJ:
        if (!($Zy->get_app_config("\x61\160\160\111\x64") === "\x74\167\151\164\164\145\162" || $Zy->get_app_config("\x61\x70\x70\111\144") === "\x6f\141\165\x74\x68\61")) {
            goto QM;
        }
        MO_Oauth_Debug::mo_oauth_log("\117\x61\x75\164\x68\61\40\146\x6c\157\x77");
        $cz = isset($_REQUEST["\164\145\x73\164"]) && !empty($_REQUEST["\164\145\163\x74"]);
        if (!($cz && '' !== $cz)) {
            goto fj;
        }
        setcookie("\157\141\x75\x74\150\x31\x5f\x74\x65\x73\x74", "\x31", time() + 20);
        fj:
        setcookie("\157\141\165\164\150\x31\x61\160\160\x6e\x61\155\145", $bj, time() + 60);
        $_COOKIE["\157\x61\165\164\150\x31\141\160\x70\156\141\x6d\145"] = $bj;
        MO_Custom_OAuth1::mo_oauth1_auth_request($bj);
        exit;
        QM:
        $ZY = md5(rand(0, 15));
        $Nh->add_replace_entry("\141\160\x70\x6e\x61\x6d\145", $bj);
        $Nh->add_replace_entry("\x72\145\144\151\x72\x65\x63\164\137\165\x72\x69", $a7);
        $Nh->add_replace_entry("\x74\x65\163\164\137\143\x6f\x6e\146\x69\x67", $cz);
        $Nh->add_replace_entry("\162\145\x73\164\162\151\x63\164\162\x65\144\151\162\145\143\164", $Ih);
        $Nh->add_replace_entry("\163\x74\x61\164\x65\x5f\x6e\157\x6e\x63\x65", $ZY);
        $Nh = apply_filters("\x6d\157\x5f\157\x61\165\164\150\x5f\x73\x65\x74\x5f\x63\x75\x73\x74\x6f\155\x5f\x73\164\x6f\x72\141\x67\145", $Nh);
        $Ql = $Nh->get_state();
        $Ql = apply_filters("\163\164\141\x74\x65\x5f\x69\156\x74\145\162\x6e\141\x6c", $Ql);
        $nz = $Zy->get_app_config("\x61\x75\164\x68\157\x72\x69\172\145\x75\x72\154");
        if (!($Zy->get_app_config("\163\x65\x6e\144\137\163\164\141\x74\145") === false || $Zy->get_app_config("\163\x65\156\x64\x5f\x73\164\x61\164\x65") === '')) {
            goto BQ;
        }
        $Zy->update_app_config("\163\x65\x6e\144\137\163\x74\x61\164\x65", 1);
        $mx->set_app_by_name($bj, $Zy->get_app_config('', false));
        BQ:
        if ($Zy->get_app_config("\163\145\x6e\x64\x5f\163\x74\141\x74\x65")) {
            goto z2;
        }
        setcookie("\163\x74\x61\164\x65\137\160\x61\162\141\155", $Ql, time() + 60);
        z2:
        $yx = $Zy->get_app_config("\160\x6b\143\x65\x5f\x66\x6c\x6f\x77");
        $Bi = $Zy->get_app_config("\162\x65\144\151\x72\x65\x63\x74\x5f\x75\162\151");
        $Sk = urlencode($Zy->get_app_config("\143\x6c\x69\145\156\x74\x5f\151\x64"));
        $Bi = empty($Bi) ? \site_url() : $Bi;
        if ($yx && 1 === $yx) {
            goto k4;
        }
        $sW = $Zy->get_app_config("\163\x65\156\144\x5f\x73\164\141\164\x65") ? "\46\x73\164\141\164\x65\x3d" . $Ql : '';
        if ($Zy->get_app_config("\x73\145\x6e\144\137\x73\164\x61\164\145")) {
            goto b_;
        }
        setcookie("\163\x74\x61\164\x65\137\x70\141\162\x61\x6d", $Ql, time() + 60);
        MO_Oauth_Debug::mo_oauth_log("\163\x74\x61\164\145\40\160\141\162\141\155\145\x74\145\162\x20\156\157\x74\40\163\x65\x6e\x74");
        goto ZQ;
        b_:
        MO_Oauth_Debug::mo_oauth_log("\x73\164\141\x74\x65\40\160\141\x72\x61\x6d\x65\x74\145\x72\40\x73\x65\156\164");
        ZQ:
        if (strpos($nz, "\x3f") !== false) {
            goto Nk;
        }
        $nz = $nz . "\x3f\143\x6c\151\x65\x6e\x74\x5f\x69\x64\75" . $Sk . "\46\x73\143\157\x70\145\75" . $Zy->get_app_config("\163\143\157\x70\x65") . "\x26\x72\x65\x64\151\162\145\143\x74\137\165\x72\151\75" . urlencode($Bi) . "\x26\162\x65\x73\x70\157\x6e\x73\145\x5f\164\x79\x70\x65\x3d\143\157\144\145" . $sW;
        goto N4;
        Nk:
        $nz = $nz . "\46\x63\154\x69\145\156\164\x5f\151\144\75" . $Sk . "\x26\x73\143\157\160\x65\75" . $Zy->get_app_config("\163\143\x6f\x70\145") . "\46\x72\x65\144\x69\162\145\x63\x74\x5f\165\x72\x69\75" . urlencode($Bi) . "\x26\x72\145\163\x70\157\156\163\145\137\x74\171\160\x65\x3d\x63\x6f\x64\145" . $sW;
        N4:
        goto gj;
        k4:
        MO_Oauth_Debug::mo_oauth_log("\x50\x4b\103\x45\x20\x66\154\x6f\167");
        $It = bin2hex(openssl_random_pseudo_bytes(32));
        $qE = $mx->base64url_encode(pack("\110\x2a", $It));
        $Sz = $mx->base64url_encode(pack("\110\x2a", hash("\163\x68\x61\62\65\66", $qE)));
        $Nh->add_replace_entry("\143\x6f\x64\x65\137\x76\145\x72\151\x66\x69\145\162", $qE);
        $Ql = $Nh->get_state();
        $sW = $Zy->get_app_config("\163\145\156\144\x5f\x73\x74\141\x74\145") ? "\x26\x73\164\141\164\145\x3d" . $Ql : '';
        if ($Zy->get_app_config("\x73\x65\156\144\x5f\163\164\141\x74\x65")) {
            goto XV;
        }
        MO_Oauth_Debug::mo_oauth_log("\x73\164\x61\164\x65\x20\160\141\x72\141\x6d\x65\x74\145\162\40\156\x6f\164\40\x73\145\156\164");
        goto kO;
        XV:
        MO_Oauth_Debug::mo_oauth_log("\x73\x74\141\x74\x65\40\160\x61\x72\x61\155\145\x74\x65\x72\40\x73\145\156\164");
        kO:
        if (strpos($nz, "\x3f") !== false) {
            goto uU;
        }
        $nz = $nz . "\x3f\x63\x6c\151\x65\156\x74\137\x69\144\75" . $Sk . "\46\x73\143\x6f\x70\145\x3d" . $Zy->get_app_config("\163\x63\x6f\160\x65") . "\46\x72\145\144\151\162\145\x63\164\137\x75\162\151\75" . urlencode($Bi) . "\46\x72\145\163\160\x6f\x6e\x73\x65\x5f\x74\x79\x70\x65\x3d\143\157\144\x65" . $sW . "\x26\143\157\144\145\x5f\x63\x68\x61\154\154\145\156\147\145\x3d" . $Sz . "\46\x63\x6f\x64\145\x5f\x63\x68\141\x6c\x6c\145\x6e\147\145\137\x6d\x65\164\150\x6f\144\75\123\62\65\x36";
        goto z1;
        uU:
        $nz = $nz . "\46\x63\154\x69\145\156\x74\137\x69\x64\75" . $Sk . "\x26\x73\143\157\160\145\x3d" . $Zy->get_app_config("\163\143\x6f\160\x65") . "\x26\162\145\x64\x69\x72\x65\143\164\137\165\x72\151\x3d" . urlencode($Bi) . "\46\162\x65\x73\x70\x6f\x6e\163\x65\x5f\x74\171\x70\145\x3d\143\x6f\144\x65" . $sW . "\46\x63\157\x64\x65\137\x63\150\141\x6c\154\145\156\x67\145\75" . $Sz . "\x26\143\x6f\x64\145\137\x63\150\x61\x6c\x6c\x65\x6e\147\145\x5f\155\x65\x74\150\157\144\x3d\x53\62\65\66";
        z1:
        gj:
        if (!(null !== $Zy->get_app_config("\x73\145\x6e\x64\137\x6e\x6f\x6e\143\x65") && $Zy->get_app_config("\163\x65\156\144\x5f\156\x6f\156\143\x65"))) {
            goto oN;
        }
        $rJ = md5(rand());
        $mx->set_transient("\x6d\x6f\x5f\157\x61\x75\164\150\137\x6e\157\156\x63\145\x5f" . $rJ, $rJ, time() + 120);
        $nz = $nz . "\x26\x6e\157\156\x63\x65\x3d" . $rJ;
        MO_Oauth_Debug::mo_oauth_log("\x6e\x6f\156\x63\x65\x20\160\141\162\x61\155\x65\164\x65\162\40\163\145\x6e\x74");
        oN:
        if (!(strpos($nz, "\x61\160\x70\x6c\x65") !== false)) {
            goto wK;
        }
        $nz = $nz . "\x26\162\x65\x73\x70\157\x6e\x73\x65\x5f\x6d\x6f\144\145\x3d\x66\x6f\162\x6d\137\x70\157\163\164";
        wK:
        $nz = apply_filters("\x6d\x6f\x5f\141\x75\x74\x68\x5f\165\162\x6c\137\151\x6e\x74\145\162\156\141\x6c", $nz, $bj);
        MO_Oauth_Debug::mo_oauth_log("\x41\165\164\150\157\x72\x69\x7a\x61\x69\x6f\x6e\40\105\156\144\160\x6f\151\156\x74\x20\75\76\40" . $nz);
        header("\114\x6f\x63\141\x74\x69\157\x6e\72\40" . $nz);
        exit;
        Fc:
        i1:
        if (isset($_GET["\x65\162\162\x6f\162\137\x64\x65\163\143\x72\x69\x70\x74\151\x6f\156"])) {
            goto OU;
        }
        if (!isset($_GET["\145\162\x72\x6f\x72"])) {
            goto cx;
        }
        do_action("\155\x6f\137\x72\145\144\151\162\x65\143\x74\x5f\164\x6f\137\x63\x75\x73\x74\157\x6d\x5f\145\x72\162\157\162\x5f\x70\x61\147\145");
        $lM = "\x45\162\162\157\x72\40\x66\x72\x6f\x6d\x20\x41\165\164\150\157\162\x69\x7a\145\x20\105\156\x64\x70\157\x69\156\x74\x3a\40" . sanitize_text_field($_GET["\145\162\x72\x6f\162"]);
        MO_Oauth_Debug::mo_oauth_log($lM);
        $mx->handle_error($lM);
        wp_die($lM);
        cx:
        goto lZ;
        OU:
        if (!(strpos($_GET["\x73\164\x61\x74\x65"], "\x64\157\x6b\141\156\55\x73\x74\x72\151\160\x65\x2d\x63\157\x6e\x6e\x65\x63\x74") !== false)) {
            goto uf;
        }
        return;
        uf:
        do_action("\x6d\157\x5f\x72\145\x64\151\x72\145\143\164\x5f\x74\x6f\x5f\143\165\163\164\x6f\155\x5f\x65\x72\x72\x6f\x72\x5f\160\141\x67\x65");
        $Hd = "\105\162\x72\x6f\x72\x20\144\x65\x73\x63\x72\151\160\x74\x69\x6f\x6e\40\x66\162\x6f\x6d\40\101\x75\164\150\157\162\151\172\145\x20\105\156\x64\x70\x6f\x69\x6e\x74\x3a\40" . sanitize_text_field($_GET["\x65\162\x72\157\x72\137\x64\x65\x73\x63\x72\x69\x70\164\151\x6f\156"]);
        MO_Oauth_Debug::mo_oauth_log($Hd);
        $mx->handle_error($Hd);
        wp_die($Hd);
        lZ:
        if (!(strpos($_SERVER["\122\105\x51\125\x45\x53\x54\137\125\122\111"], "\157\x70\145\156\x69\x64\143\141\154\x6c\142\x61\x63\x6b") !== false || strpos($_SERVER["\122\105\x51\125\x45\x53\124\x5f\125\x52\111"], "\x6f\x61\x75\164\x68\x5f\x74\x6f\153\145\156") !== false && strpos($_SERVER["\x52\105\121\125\x45\x53\124\x5f\x55\122\111"], "\x6f\x61\165\x74\150\137\x76\145\x72\x69\146\151\145\x72"))) {
            goto qC;
        }
        MO_Oauth_Debug::mo_oauth_log("\117\x61\x75\164\150\x31\40\x63\141\x6c\x6c\142\141\143\x6b\x20\146\154\157\x77");
        if (!empty($_COOKIE["\157\x61\165\x74\150\61\x61\x70\160\x6e\141\x6d\x65"])) {
            goto Hh;
        }
        MO_Oauth_Debug::mo_oauth_log("\x52\x65\164\165\162\x6e\x69\156\x67\40\x66\162\157\x6d\40\x4f\101\x75\x74\150\61");
        return;
        Hh:
        MO_Oauth_Debug::mo_oauth_log("\117\x41\165\164\x68\61\40\x61\160\160\40\146\157\165\156\144");
        $bj = $_COOKIE["\x6f\x61\165\x74\x68\61\x61\160\160\156\141\x6d\145"];
        $SR = MO_Custom_OAuth1::mo_oidc1_get_access_token($_COOKIE["\157\x61\x75\164\x68\61\141\160\x70\x6e\141\155\145"]);
        $MY = apply_filters("\155\157\x5f\164\x72\137\141\x66\164\145\162\x5f\160\162\x6f\x66\x69\154\x65\x5f\x69\x6e\x66\157\x5f\x65\x78\x74\162\141\143\164\x69\157\x6e\137\146\162\x6f\x6d\x5f\164\157\153\x65\156", $SR);
        $gu = [];
        $BW = $this->dropdownattrmapping('', $SR, $gu);
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\157\141\x75\x74\150\x5f\141\164\164\162\137\156\x61\x6d\145\x5f\x6c\x69\163\x74" . $bj, $BW);
        if (!(isset($_COOKIE["\157\x61\x75\x74\x68\61\x5f\164\x65\163\x74"]) && $_COOKIE["\157\x61\165\164\150\61\x5f\164\x65\163\164"] == "\61")) {
            goto iz;
        }
        $Zy = $mx->get_app_by_name($bj);
        $t4 = $Zy->get_app_config();
        $this->render_test_config_output($SR, false, $t4, $bj);
        exit;
        iz:
        $Zy = $mx->get_app_by_name($bj);
        $KG = $Zy->get_app_config("\x75\x73\x65\162\x6e\x61\155\x65\137\141\164\x74\x72");
        $aQ = isset($xA["\145\155\x61\x69\x6c\x5f\141\164\164\x72"]) ? $xA["\x65\x6d\141\151\x6c\137\141\x74\x74\x72"] : '';
        $UU = $mx->getnestedattribute($SR, $aQ);
        $O6 = $mx->getnestedattribute($SR, $KG);
        if (!empty($O6)) {
            goto bq;
        }
        MO_Oauth_Debug::mo_oauth_log("\x55\163\145\162\x6e\141\155\145\x20\x6e\x6f\x74\x20\x72\145\143\145\151\166\145\x64\56\120\x6c\x65\141\x73\145\40\x63\157\x6e\x66\151\147\x75\x72\145\40\x41\164\x74\x72\151\142\165\164\x65\40\x4d\x61\160\160\151\156\147");
        wp_die("\125\x73\145\x72\x6e\141\x6d\x65\x20\156\157\x74\x20\x72\x65\143\145\x69\166\145\x64\56\x50\154\145\141\163\x65\40\x63\157\156\x66\151\147\x75\x72\145\40\101\164\x74\162\x69\x62\x75\164\145\40\x4d\141\x70\160\151\x6e\x67");
        bq:
        if (!empty($UU)) {
            goto vt;
        }
        $user = get_user_by("\154\157\x67\x69\156", $O6);
        goto li;
        vt:
        $UU = $mx->getnestedattribute($SR, $aQ);
        if (!(false === strpos($UU, "\100"))) {
            goto e6;
        }
        MO_Oauth_Debug::mo_oauth_log("\115\141\x70\x70\x65\x64\40\x45\155\x61\x69\154\40\141\x74\164\162\151\142\x75\x74\x65\40\x64\x6f\x65\163\x20\x6e\x6f\164\x20\143\157\156\x74\141\151\156\40\x76\141\x6c\x69\x64\40\145\155\x61\x69\x6c\56");
        wp_die("\115\141\160\160\x65\x64\x20\x45\155\x61\x69\x6c\x20\x61\164\164\x72\x69\x62\x75\164\145\x20\144\157\145\163\40\156\157\164\40\x63\157\x6e\164\x61\x69\156\x20\x76\x61\154\x69\x64\40\145\155\141\151\x6c\x2e");
        e6:
        li:
        if ($user) {
            goto Mh;
        }
        $b2 = 0;
        if ($mx->mo_oauth_hbca_xyake()) {
            goto pb;
        }
        $user = $mx->mo_oauth_hjsguh_kiishuyauh878gs($UU, $O6);
        goto Lw;
        pb:
        if ($mx->mo_oauth_client_get_option("\155\x6f\x5f\x6f\x61\165\x74\x68\x5f\146\x6c\x61\147") !== true) {
            goto Dz;
        }
        $HG = base64_decode("\120\107\122\160\x64\151\102\x7a\144\110\154\163\132\x54\x30\156\x64\107\126\x34\144\x43\x31\x68\142\x47\x6c\156\x62\x6a\x70\152\x5a\127\x35\60\132\x58\111\x37\112\x7a\x34\70\x59\x6a\x35\x56\x63\x32\x56\x79\111\105\x46\x6a\131\x32\x39\x31\x62\x6e\x51\147\132\107\x39\154\x63\171\x42\x75\x62\63\121\147\132\130\150\x70\143\63\x51\165\120\x43\71\151\120\x6a\167\166\x5a\107\x6c\62\120\x6a\170\151\x63\x6a\64\70\143\x32\61\150\142\x47\x77\53\x56\107\150\160\x63\171\x42\x32\132\130\112\x7a\x61\127\x39\165\111\110\116\x31\143\110\x42\166\x63\x6e\x52\x7a\x49\x45\x46\x31\144\x47\70\147\121\x33\x4a\154\x59\130\122\x6c\x49\x46\x56\172\x5a\x58\x49\147\132\155\x56\x68\x64\110\126\x79\x5a\123\102\x31\143\x48\122\x76\111\104\105\x77\x49\106\x56\172\x5a\x58\x4a\x7a\x4c\x69\102\x51\142\107\x56\x68\143\62\x55\x67\144\x58\102\156\143\155\106\153\x5a\123\x42\60\x62\171\x42\x30\x61\x47\x55\x67\x61\x47\x6c\x6e\141\x47\x56\171\111\110\x5a\154\x63\x6e\116\x70\x62\62\64\147\142\62\131\147\x64\x47\150\x6c\x49\x48\x42\163\144\127\144\x70\x62\151\102\x30\x62\171\102\154\142\155\x46\151\142\x47\x55\147\x59\x58\126\60\x62\x79\x42\x6a\x63\155\126\x68\144\107\125\x67\144\x58\x4e\154\143\151\x42\155\142\63\111\147\144\x57\65\163\141\x57\x31\x70\144\107\x56\153\111\110\x56\172\x5a\130\x4a\x7a\x49\107\x39\171\x49\107\106\153\x5a\x43\x42\61\x63\x32\x56\x79\111\107\61\x68\142\156\126\x68\142\x47\x78\65\x4c\152\167\166\143\62\61\x68\x62\x47\167\x2b");
        MO_Oauth_Debug::mo_oauth_log($HG);
        wp_die($HG);
        goto Io;
        Dz:
        if (!empty($UU)) {
            goto xw;
        }
        $user = $mx->mo_oauth_jhuyn_jgsukaj($O6, $O6);
        goto ej;
        xw:
        $user = $mx->mo_oauth_jhuyn_jgsukaj($UU, $O6);
        ej:
        Io:
        Lw:
        goto Cv;
        Mh:
        $b2 = $user->ID;
        Cv:
        if (!$user) {
            goto QK;
        }
        wp_set_current_user($user->ID);
        $fr = false;
        $fr = apply_filters("\155\x6f\x5f\x72\x65\155\x65\155\x62\145\162\x5f\x6d\145", $fr);
        wp_set_auth_cookie($user->ID, $fr);
        $user = get_user_by("\x49\x44", $user->ID);
        do_action("\167\x70\x5f\154\x6f\147\x69\x6e", $user->user_login, $user);
        wp_safe_redirect(home_url());
        exit;
        QK:
        qC:
        if (!(!isset($_SERVER["\x48\x54\124\120\x5f\130\137\122\105\x51\125\x45\123\124\105\x44\x5f\x57\x49\x54\x48"]) && (strpos($_SERVER["\122\x45\121\125\105\x53\124\137\x55\x52\111"], "\57\x6f\x61\x75\x74\x68\143\141\154\154\x62\141\143\153") !== false || isset($_REQUEST["\x63\x6f\144\145"]) && !empty($_REQUEST["\x63\x6f\144\145"]) && !isset($_REQUEST["\x69\144\x5f\x74\157\x6b\x65\x6e"])))) {
            goto UT;
        }
        if (!(isset($_REQUEST["\160\x6f\163\x74\x5f\111\x44"]) || isset($_REQUEST["\x65\144\x64\x2d\141\143\x74\151\157\156"]))) {
            goto Cf;
        }
        return;
        Cf:
        try {
            if (isset($_COOKIE["\163\164\141\164\145\137\160\x61\162\x61\155"])) {
                goto h8;
            }
            if (isset($_GET["\163\164\x61\x74\x65"])) {
                goto rH;
            }
            $Ir = new StorageManager();
            if (!is_multisite()) {
                goto HB;
            }
            $Ir->add_replace_entry("\x62\x6c\x6f\x67\137\151\144", 1);
            HB:
            $wK = $mx->get_app_by_name();
            if (isset($_GET["\141\x70\x70\137\156\x61\155\x65"])) {
                goto xA;
            }
            $Ir->add_replace_entry("\141\160\160\156\x61\155\145", $wK->get_app_name());
            goto TG;
            xA:
            $Ir->add_replace_entry("\x61\x70\x70\x6e\x61\x6d\x65", $_GET["\141\x70\160\x5f\x6e\x61\155\x65"]);
            TG:
            $Ir->add_replace_entry("\x74\x65\163\164\137\143\x6f\x6e\x66\151\147", false);
            $Ir->add_replace_entry("\x72\x65\144\151\x72\x65\x63\x74\x5f\x75\162\151", site_url());
            $Ql = $Ir->get_state();
            goto AN;
            rH:
            $Ql = wp_unslash($_GET["\163\164\x61\x74\x65"]);
            AN:
            goto BO;
            h8:
            $Ql = $_COOKIE["\x73\x74\141\x74\x65\137\x70\141\x72\141\x6d"];
            BO:
            $Nh = new StorageManager($Ql);
            if (!empty($Nh->get_value("\x61\x70\x70\x6e\141\155\145"))) {
                goto nG;
            }
            return;
            nG:
            $bj = $Nh->get_value("\x61\160\x70\x6e\x61\155\x65");
            $cz = $Nh->get_value("\164\x65\x73\164\137\143\157\x6e\146\x69\x67");
            if (!is_multisite()) {
                goto Sy;
            }
            if (!(empty($Nh->get_value("\x72\x65\x64\x69\162\x65\x63\164\145\x64\x5f\164\157\137\163\165\x62\x73\x69\x74\145")) || $Nh->get_value("\x72\x65\144\x69\162\145\x63\164\x65\144\137\164\x6f\137\x73\165\142\163\x69\164\145") !== "\x72\x65\144\151\162\x65\143\x74")) {
                goto gK;
            }
            MO_Oauth_Debug::mo_oauth_log("\x52\145\x64\151\162\x65\143\164\x69\156\147\40\x66\x6f\x72\x20\x6d\165\x6c\164\151\x73\164\145\40\163\165\x62\x73\x69\x74\x65");
            $blog_id = $Nh->get_value("\x62\x6c\x6f\147\137\151\x64");
            $pG = get_site_url($blog_id);
            $Nh->add_replace_entry("\162\145\x64\151\162\x65\143\x74\145\x64\137\164\x6f\137\x73\165\142\163\x69\164\x65", "\x72\145\x64\x69\162\145\x63\164");
            $rM = $Nh->get_state();
            $pG = $pG . "\77\143\157\x64\x65\75" . $_GET["\x63\157\x64\145"] . "\x26\x73\164\141\164\x65\x3d" . $rM;
            wp_redirect($pG);
            exit;
            gK:
            Sy:
            $vP = $bj ? $bj : '';
            $FO = $mx->mo_oauth_client_get_option("\x6d\x6f\137\x6f\141\165\164\150\137\x61\x70\x70\163\137\154\x69\x73\x74");
            $KG = '';
            $aQ = '';
            $Uq = $mx->get_app_by_name($vP);
            if ($Uq) {
                goto Wy;
            }
            $mx->handle_error("\x41\x70\160\154\x69\x63\x61\164\x69\157\156\40\156\x6f\164\40\x63\x6f\156\146\x69\x67\165\162\x65\144\56");
            MO_Oauth_Debug::mo_oauth_log("\101\x70\x70\154\x69\x63\141\x74\x69\x6f\156\x20\x6e\x6f\x74\x20\143\157\156\x66\151\x67\165\162\145\x64\56");
            exit("\x41\160\x70\x6c\x69\x63\x61\164\x69\x6f\x6e\40\x6e\157\164\x20\x63\157\x6e\146\x69\147\165\x72\145\144\x2e");
            Wy:
            $xA = $Uq->get_app_config();
            if (!(isset($xA["\163\x65\x6e\144\x5f\156\157\x6e\143\x65"]) && $xA["\x73\x65\156\x64\137\156\157\156\143\x65"] === 1)) {
                goto ol;
            }
            if (!(isset($_REQUEST["\x6e\x6f\x6e\143\145"]) && !$mx->get_transient("\x6d\157\x5f\157\x61\165\164\x68\x5f\x6e\x6f\x6e\143\x65\137" . $_REQUEST["\156\x6f\x6e\143\145"]))) {
                goto eh;
            }
            $lM = "\x4e\x6f\x6e\x63\145\x20\x76\145\x72\x69\x66\x69\x63\141\x74\151\157\156\40\151\163\40\x66\x61\151\154\x65\144\56\x20\120\x6c\145\x61\163\145\40\x63\x6f\156\x74\141\x63\x74\x20\164\157\40\x79\x6f\165\x72\40\141\144\x6d\x69\x6e\x69\163\x74\162\141\164\x6f\x72\x2e";
            $mx->handle_error($lM);
            MO_Oauth_Debug::mo_oauth_log($lM);
            wp_die($lM);
            eh:
            ol:
            $yx = $Uq->get_app_config("\160\x6b\x63\x65\137\146\154\x6f\167");
            $Z1 = $Uq->get_app_config("\160\153\143\145\137\x63\154\x69\x65\x6e\x74\137\x73\145\x63\162\x65\164");
            $x1 = array("\147\162\141\156\x74\x5f\164\x79\160\145" => "\x61\x75\x74\x68\157\x72\151\x7a\x61\164\x69\157\x6e\x5f\143\157\x64\x65", "\x63\154\x69\145\156\x74\137\151\x64" => $xA["\143\x6c\151\x65\x6e\x74\137\151\x64"], "\162\145\144\151\x72\x65\143\x74\137\165\x72\x69" => $xA["\162\145\x64\x69\x72\145\143\x74\x5f\x75\x72\x69"], "\143\157\144\145" => $_REQUEST["\143\x6f\144\x65"]);
            if (!(strpos($xA["\x61\x63\143\145\x73\163\164\x6f\153\x65\x6e\165\162\x6c"], "\x73\145\162\166\x69\143\145\x73\x2f\x6f\x61\165\x74\x68\62\x2f\164\157\x6b\x65\x6e") === false && strpos($xA["\x61\143\x63\x65\163\x73\x74\x6f\x6b\145\x6e\x75\162\154"], "\163\x61\154\x65\x73\146\x6f\162\143\x65") === false && strpos($xA["\x61\x63\x63\x65\163\x73\x74\x6f\x6b\145\156\x75\162\154"], "\57\x6f\x61\155\x2f\157\141\165\x74\150\x32\x2f\x61\143\143\145\163\163\x5f\164\157\x6b\x65\x6e") === false)) {
                goto Y2;
            }
            $x1["\x73\x63\157\160\x65"] = $Uq->get_app_config("\163\x63\157\x70\x65");
            Y2:
            if ($yx && 1 === $yx) {
                goto dX;
            }
            $x1["\143\154\151\145\x6e\x74\x5f\163\x65\143\x72\x65\164"] = $xA["\x63\x6c\x69\x65\x6e\164\137\x73\145\143\162\x65\164"];
            goto kM;
            dX:
            if (!($Z1 && 1 === $Z1)) {
                goto x8;
            }
            $x1["\143\x6c\151\x65\156\164\137\163\145\143\162\145\164"] = $xA["\x63\x6c\151\145\156\164\137\x73\x65\x63\162\145\164"];
            x8:
            $x1 = apply_filters("\155\157\137\157\141\165\164\150\137\141\x64\144\x5f\x63\154\x69\145\x6e\x74\x5f\x73\145\143\162\x65\164\x5f\160\x6b\143\145\x5f\x66\154\x6f\167", $x1, $xA);
            $x1["\x63\x6f\x64\x65\x5f\x76\145\x72\x69\x66\151\x65\162"] = $Nh->get_value("\143\157\144\x65\x5f\x76\145\162\x69\x66\x69\145\162");
            kM:
            $Ao = isset($xA["\x73\145\156\144\137\150\x65\x61\x64\145\162\163"]) ? $xA["\163\x65\x6e\x64\x5f\150\x65\x61\x64\145\x72\x73"] : 0;
            $Qq = isset($xA["\x73\x65\156\x64\x5f\142\157\144\171"]) ? $xA["\163\x65\156\144\137\142\x6f\144\171"] : 0;
            if ("\157\160\145\x6e\x69\x64\143\157\156\x6e\x65\x63\x74" === $Uq->get_app_config("\141\x70\x70\137\164\x79\x70\145")) {
                goto rd;
            }
            $rv = $xA["\x61\143\143\x65\x73\x73\x74\157\x6b\145\x6e\165\162\x6c"];
            MO_Oauth_Debug::mo_oauth_log("\117\x41\x75\164\x68\40\x66\154\157\167");
            if (strpos($xA["\x61\165\164\x68\x6f\162\151\x7a\145\165\x72\x6c"], "\x63\x6c\x65\166\x65\x72\56\143\157\155\57\157\141\165\x74\x68") != false || strpos($xA["\141\x63\143\145\163\163\x74\157\153\x65\156\165\x72\154"], "\142\x69\x74\x72\x69\170") != false) {
                goto gz;
            }
            $pM = json_decode($this->oauth_handler->get_token($rv, $x1, $Ao, $Qq), true);
            goto Il;
            gz:
            $pM = json_decode($this->oauth_handler->get_atoken($rv, $x1, $_GET["\143\x6f\x64\145"], $Ao, $Qq), true);
            Il:
            if (!(get_current_user_id() && $cz != 1)) {
                goto Dp;
            }
            wp_clear_auth_cookie();
            wp_set_current_user(0);
            Dp:
            $_SESSION["\160\x72\x6f\143\157\162\145\137\141\143\x63\x65\163\x73\x5f\164\157\153\145\x6e"] = isset($pM["\141\143\x63\145\x73\163\x5f\164\157\x6b\x65\x6e"]) ? $pM["\x61\x63\x63\145\x73\163\137\164\157\x6b\145\x6e"] : false;
            if (isset($pM["\141\143\x63\x65\x73\163\x5f\x74\157\153\145\x6e"])) {
                goto VB;
            }
            do_action("\155\x6f\x5f\x72\145\144\x69\x72\x65\x63\x74\x5f\164\157\x5f\x63\165\x73\164\157\155\x5f\145\162\x72\157\162\137\160\141\147\x65");
            $mx->handle_error("\x49\x6e\166\x61\154\151\144\40\x74\x6f\x6b\x65\156\40\x72\x65\x63\x65\x69\166\x65\144\56");
            MO_Oauth_Debug::mo_oauth_log("\x49\156\166\141\154\151\x64\x20\164\157\x6b\x65\x6e\40\x72\145\143\145\151\166\x65\x64\x2e");
            exit("\x49\156\166\141\154\151\144\x20\164\x6f\x6b\145\x6e\40\x72\145\x63\x65\151\166\x65\x64\56");
            VB:
            MO_Oauth_Debug::mo_oauth_log("\x54\x6f\x6b\x65\x6e\40\122\145\163\160\x6f\156\163\x65\40\x3d\x3e\40");
            MO_Oauth_Debug::mo_oauth_log($pM);
            $yZ = $xA["\x72\145\x73\157\x75\x72\x63\x65\157\167\x6e\145\162\144\145\x74\141\x69\154\x73\x75\x72\154"];
            if (!(substr($yZ, -1) === "\75")) {
                goto qn;
            }
            $yZ .= $pM["\x61\143\x63\145\163\x73\x5f\x74\x6f\x6b\x65\156"];
            qn:
            MO_Oauth_Debug::mo_oauth_log("\101\x63\x63\x65\x73\x73\x20\x74\157\x6b\145\x6e\x20\x72\145\x63\145\151\166\x65\144\x2e");
            MO_Oauth_Debug::mo_oauth_log("\101\143\143\145\163\163\40\x54\157\x6b\145\x6e\40\x3d\76\x20" . $pM["\x61\143\x63\x65\163\163\137\164\157\x6b\x65\156"]);
            $SR = false;
            MO_Oauth_Debug::mo_oauth_log("\122\x65\163\x6f\x75\x72\143\x65\40\117\x77\156\x65\x72\40\75\x3e\40");
            if (!has_filter("\x6d\157\x5f\165\x73\x65\162\151\x6e\146\157\137\146\154\157\x77\x5f\146\157\162\x5f\167\141\x6c\x6d\141\x72\x74")) {
                goto Hg;
            }
            $SR = apply_filters("\155\157\137\x75\x73\145\162\151\x6e\x66\x6f\137\x66\154\157\167\x5f\x66\157\162\137\167\x61\x6c\x6d\x61\162\164", $yZ, $pM, $x1, $vP, $xA);
            Hg:
            if (!($SR === false)) {
                goto Fd;
            }
            $gc = null;
            $gc = apply_filters("\155\x6f\x5f\160\x6f\x6c\141\x72\137\x72\145\147\x69\163\164\x65\162\137\165\163\145\162", $pM);
            if (!(!empty($gc) && !empty($pM["\170\x5f\x75\x73\x65\162\x5f\151\x64"]))) {
                goto sY;
            }
            $yZ .= "\x2f" . $pM["\170\137\x75\x73\145\x72\x5f\151\x64"];
            sY:
            $SR = $this->oauth_handler->get_resource_owner($yZ, $pM["\x61\143\143\145\163\x73\x5f\164\x6f\x6b\145\x6e"]);
            $HW = array();
            if (!(strpos($Uq->get_app_config("\x61\165\x74\150\x6f\x72\x69\172\x65\x75\x72\154"), "\154\x69\x6e\x6b\145\x64\x69\156") !== false && strpos($xA["\163\x63\157\x70\145"], "\x72\x5f\145\155\141\x69\154\x61\x64\144\x72\145\x73\x73") != false)) {
                goto sd;
            }
            $eX = "\150\164\x74\160\163\x3a\x2f\x2f\x61\x70\151\x2e\x6c\x69\x6e\x6b\145\x64\151\x6e\x2e\x63\x6f\x6d\57\166\62\57\145\x6d\141\151\x6c\x41\144\x64\x72\145\163\x73\x3f\161\75\x6d\145\x6d\x62\145\162\x73\46\160\x72\157\152\145\x63\164\x69\x6f\156\75\50\x65\154\x65\155\x65\156\164\x73\52\x28\150\x61\x6e\x64\154\145\x7e\x29\51";
            $HW = $this->oauth_handler->get_resource_owner($eX, $pM["\x61\143\143\x65\163\x73\137\x74\157\153\145\156"]);
            sd:
            $SR = array_merge($SR, $HW);
            Fd:
            if (!has_filter("\155\157\137\143\x68\x65\x63\153\137\x74\157\x5f\x65\170\x65\x63\165\164\x65\x5f\160\x6f\163\x74\137\x75\x73\145\162\151\156\146\157\137\x66\154\x6f\x77\137\x66\x6f\x72\x5f\x77\141\x6c\x6d\x61\x72\x74")) {
                goto IL;
            }
            $SR = apply_filters("\155\x6f\x5f\x63\150\x65\143\x6b\137\164\x6f\137\145\170\145\143\165\164\x65\x5f\160\157\163\x74\137\165\x73\145\x72\151\156\146\157\x5f\x66\154\x6f\167\x5f\x66\157\162\x5f\x77\141\x6c\155\x61\162\x74", $SR, $vP, $xA);
            IL:
            MO_Oauth_Debug::mo_oauth_log($SR);
            $MY = apply_filters("\155\x6f\137\164\x72\x5f\141\146\164\145\162\x5f\x70\162\157\x66\151\x6c\x65\137\x69\156\146\x6f\137\145\x78\164\162\x61\143\164\151\x6f\x6e\x5f\x66\162\x6f\155\137\164\157\153\145\x6e", $SR);
            if (!($MY != '' && is_array($MY))) {
                goto LE;
            }
            $SR = array_merge($SR, $MY);
            LE:
            $D1 = apply_filters("\x61\x63\143\x72\145\x64\x69\164\151\x6f\156\x73\137\145\x6e\144\x70\157\151\x6e\x74", $pM["\141\x63\x63\145\163\x73\137\x74\x6f\x6b\145\156"]);
            if (!($D1 !== '' && is_array($D1))) {
                goto mH;
            }
            $SR = array_merge($SR, $D1);
            mH:
            if (!has_filter("\x6d\x6f\x5f\x70\157\154\141\162\x5f\162\x65\x67\151\163\x74\145\x72\137\165\x73\x65\162")) {
                goto Lb;
            }
            $xB = array();
            $xB["\x74\157\153\145\156"] = $pM["\141\143\143\x65\x73\x73\x5f\164\157\153\145\156"];
            $SR = array_merge($SR, $xB);
            Lb:
            if (!(strpos($Uq->get_app_config("\141\165\164\150\157\x72\x69\x7a\145\165\x72\154"), "\x64\x69\163\x63\157\x72\x64") !== false)) {
                goto qw;
            }
            apply_filters("\155\157\137\x64\x69\163\137\x75\x73\x65\162\x5f\x61\x74\x74\145\x6e\x64\x61\156\143\145", $SR["\x69\x64"]);
            $yH = apply_filters("\155\157\x5f\144\162\155\137\147\145\164\x5f\x75\163\145\x72\x5f\162\x6f\x6c\x65\163", array_key_exists("\x69\144", $SR) ? $SR["\151\x64"] : '');
            if (!(false !== $yH)) {
                goto q0;
            }
            MO_Oauth_Debug::mo_oauth_log("\x44\151\x73\x63\x6f\162\x64\40\122\x6f\x6c\x65\163\40\75\76\x20");
            MO_Oauth_Debug::mo_oauth_log($yH);
            $SR["\x72\x6f\154\145\x73"] = $yH;
            q0:
            $DI = $Nh->get_value("\x64\151\x73\x63\157\162\x64\x5f\165\163\x65\x72\137\151\x64");
            do_action("\155\157\137\x6f\141\165\x74\150\137\141\x64\144\x5f\144\151\163\x5f\165\163\x65\162\137\x73\145\162\166\145\162", $DI, $pM, $SR);
            qw:
            if (!(isset($xA["\x73\145\156\x64\x5f\156\x6f\x6e\x63\145"]) && $xA["\x73\x65\x6e\x64\137\x6e\x6f\156\x63\x65"] === 1)) {
                goto uN;
            }
            if (!(isset($SR["\x6e\157\x6e\143\x65"]) && $SR["\x6e\157\156\143\x65"] != NULL)) {
                goto zE;
            }
            if ($mx->get_transient("\155\157\x5f\157\x61\x75\164\x68\137\156\x6f\156\143\x65\x5f" . $SR["\x6e\157\x6e\x63\145"])) {
                goto Kk;
            }
            $lM = "\116\157\156\143\145\40\166\145\162\151\x66\x69\143\x61\x74\151\157\x6e\40\x69\163\x20\x66\x61\151\154\145\144\56\40\x50\x6c\145\x61\163\x65\x20\x63\x6f\x6e\164\x61\x63\x74\x20\x74\157\40\171\x6f\165\x72\x20\x61\x64\155\151\156\151\163\x74\x72\x61\164\x6f\162\x2e";
            $mx->handle_error($lM);
            MO_Oauth_Debug::mo_oauth_log($lM);
            wp_die($lM);
            goto Ig;
            Kk:
            $mx->delete_transient("\155\x6f\x5f\x6f\141\165\164\150\137\x6e\157\x6e\143\x65\x5f" . $SR["\x6e\157\156\x63\x65"]);
            Ig:
            zE:
            uN:
            $gu = [];
            $BW = $this->dropdownattrmapping('', $SR, $gu);
            $mx->mo_oauth_client_update_option("\155\157\137\157\x61\165\x74\x68\137\141\x74\x74\x72\137\x6e\x61\x6d\x65\x5f\154\151\163\164" . $vP, $BW);
            if (!($cz && '' !== $cz)) {
                goto Kg;
            }
            $this->handle_group_test_conf($SR, $xA, $pM["\141\x63\143\145\163\163\137\164\157\x6b\x65\156"], false, $cz);
            exit;
            Kg:
            goto Dw;
            rd:
            MO_Oauth_Debug::mo_oauth_log("\117\160\145\x6e\x49\x44\40\103\x6f\156\156\x65\x63\164\40\x66\154\x6f\x77");
            $pM = json_decode($this->oauth_handler->get_token($xA["\141\143\143\145\163\x73\164\x6f\x6b\145\x6e\x75\x72\x6c"], $x1, $Ao, $Qq), true);
            $sm = [];
            try {
                $sm = $this->resolve_and_get_oidc_response($pM);
            } catch (\Exception $tS) {
                $mx->handle_error($tS->getMessage());
                MO_Oauth_Debug::mo_oauth_log("\x49\156\x76\141\154\x69\x64\x20\122\145\x73\x70\157\156\x73\145\x20\162\145\143\x65\x69\166\145\144\56");
                do_action("\155\x6f\137\162\x65\x64\151\x72\x65\143\164\137\164\x6f\137\x63\165\x73\164\157\155\137\x65\x72\x72\x6f\x72\137\160\x61\x67\145");
                wp_die("\x49\x6e\166\x61\154\x69\144\x20\122\x65\163\160\x6f\x6e\x73\x65\x20\x72\x65\x63\145\x69\166\x65\x64\x2e");
                exit;
            }
            MO_Oauth_Debug::mo_oauth_log("\x49\x44\x20\124\157\x6b\145\x6e\40\x72\x65\143\x65\151\166\x65\x64\x20\123\165\143\x63\145\163\x73\146\x75\x6c\x6c\171");
            MO_Oauth_Debug::mo_oauth_log("\x49\x44\x20\x54\x6f\153\x65\x6e\x20\75\76\40" . $sm);
            $SR = $this->get_resource_owner_from_app($sm, $vP);
            MO_Oauth_Debug::mo_oauth_log("\x52\x65\163\x6f\x75\x72\143\x65\x20\x4f\x77\x6e\x65\x72\40\75\76\x20");
            MO_Oauth_Debug::mo_oauth_log($SR);
            if (!(strpos($Uq->get_app_config("\x61\165\164\150\x6f\x72\x69\x7a\x65\x75\162\x6c"), "\x74\x77\151\164\x63\150") !== false)) {
                goto cT;
            }
            $ZJ = apply_filters("\x6d\x6f\x5f\164\163\x6d\137\x67\x65\x74\x5f\x75\x73\x65\162\x5f\163\x75\142\x73\143\x72\151\x70\164\x69\157\156", $SR["\x73\x75\x62"], $xA["\143\154\x69\x65\x6e\164\137\151\144"], $pM["\x61\x63\x63\x65\163\163\137\164\157\x6b\x65\156"]);
            if (!(false !== $ZJ)) {
                goto hq;
            }
            MO_Oauth_Debug::mo_oauth_log("\x54\167\151\164\143\150\40\123\x75\x62\x73\143\162\151\x70\164\151\157\x6e\40\75\76\40");
            MO_Oauth_Debug::mo_oauth_log($ZJ);
            $SR["\x73\x75\142\163\x63\162\151\160\x74\151\157\x6e"] = $ZJ;
            hq:
            cT:
            if (!($Uq->get_app_config("\x61\160\x70\x49\x64") === "\x6b\145\171\143\x6c\157\x61\153")) {
                goto F1;
            }
            $sc = apply_filters("\155\157\137\x6b\162\155\137\147\145\x74\137\x75\163\145\x72\x5f\162\x6f\154\x65\x73", $SR, $pM);
            if (!(false !== $sc)) {
                goto k3;
            }
            $SR["\162\x6f\x6c\x65\x73"] = $sc;
            k3:
            F1:
            $SR = apply_filters("\x6d\157\137\141\172\x75\162\145\142\x32\143\x5f\x67\x65\164\137\x75\x73\x65\x72\x5f\x67\162\157\165\x70\x5f\x69\144\163", $SR, $xA);
            $MY = apply_filters("\x6d\157\137\164\x72\x5f\x61\146\x74\145\x72\137\160\x72\157\x66\x69\154\145\x5f\x69\156\x66\157\x5f\145\x78\164\162\x61\143\x74\151\x6f\156\x5f\x66\162\157\155\137\x74\157\x6b\x65\x6e", $SR);
            if (!($MY != '' && is_array($MY))) {
                goto Qy;
            }
            $SR = array_merge($SR, $MY);
            Qy:
            if (!(isset($xA["\163\145\156\x64\137\156\157\156\x63\x65"]) && $xA["\x73\x65\x6e\x64\137\156\x6f\156\x63\145"] === 1)) {
                goto au;
            }
            if (!(isset($SR["\156\157\x6e\x63\145"]) && $SR["\156\157\156\x63\145"] != NULL)) {
                goto Jf;
            }
            if ($mx->get_transient("\x6d\157\137\x6f\141\165\x74\x68\137\x6e\157\x6e\x63\x65\x5f" . $SR["\156\x6f\156\143\x65"])) {
                goto NA;
            }
            $lM = "\116\157\156\x63\x65\x20\x76\x65\x72\x69\146\x69\143\141\164\x69\x6f\x6e\x20\x69\x73\x20\x66\141\151\154\145\x64\56\40\120\x6c\145\x61\163\x65\40\143\x6f\x6e\164\141\143\164\40\x74\x6f\40\x79\157\x75\162\40\x61\144\x6d\x69\156\151\163\x74\x72\x61\x74\157\162\56";
            $mx->handle_error($lM);
            MO_Oauth_Debug::mo_oauth_log($lM);
            wp_die($lM);
            goto nd;
            NA:
            $mx->delete_transient("\x6d\x6f\x5f\157\x61\165\x74\150\137\156\157\x6e\143\145\137" . $SR["\x6e\x6f\x6e\x63\145"]);
            nd:
            Jf:
            au:
            $gu = [];
            $BW = $this->dropdownattrmapping('', $SR, $gu);
            $mx->mo_oauth_client_update_option("\x6d\x6f\137\157\141\165\164\150\137\141\x74\x74\162\x5f\x6e\141\x6d\145\137\154\x69\163\x74" . $vP, $BW);
            if (!($cz && '' !== $cz)) {
                goto fw;
            }
            $pM["\x72\145\x66\162\x65\x73\x68\137\x74\157\x6b\145\x6e"] = isset($pM["\x72\x65\146\162\145\163\150\137\164\x6f\153\x65\x6e"]) ? $pM["\162\145\146\x72\x65\x73\x68\x5f\x74\157\x6b\145\156"] : '';
            $_SESSION["\x70\x72\157\143\157\162\x65\137\x72\x65\146\162\x65\163\x68\137\164\x6f\153\145\156"] = $pM["\x72\x65\x66\x72\x65\x73\x68\x5f\x74\157\x6b\145\156"];
            $ZG = isset($pM["\141\143\x63\145\x73\x73\x5f\x74\157\153\145\156"]) ? $pM["\x61\x63\x63\145\x73\x73\137\164\157\153\145\x6e"] : '';
            $this->handle_group_test_conf($SR, $xA, $ZG, false, $cz);
            MO_Oauth_Debug::mo_oauth_log("\x41\164\x74\x72\151\x62\165\164\145\40\x52\145\143\x65\x69\x76\145\x64\40\123\x75\143\143\145\x73\163\146\165\x6c\x6c\171");
            exit;
            fw:
            Dw:
            if (!(isset($xA["\147\x72\157\x75\160\144\x65\x74\x61\151\154\163\165\x72\154"]) && !empty($xA["\x67\x72\157\165\160\144\145\164\x61\x69\x6c\x73\x75\x72\154"]))) {
                goto TK;
            }
            $SR = $this->handle_group_user_info($SR, $xA, $pM["\x61\x63\x63\x65\163\x73\x5f\164\x6f\x6b\145\x6e"]);
            MO_Oauth_Debug::mo_oauth_log("\x47\x72\157\x75\160\40\x44\145\x74\141\151\154\163\x20\x4f\142\x74\141\151\x6e\145\x64\x20\x3d\x3e\40" . $SR);
            TK:
            MO_Oauth_Debug::mo_oauth_log("\106\x65\164\x63\150\145\x64\40\162\145\x73\157\x75\x72\x63\145\x20\x6f\167\x6e\145\162\40\72\x20" . json_encode($SR));
            if (!has_filter("\167\x6f\x6f\x63\x6f\155\x6d\145\x72\143\x65\x5f\143\150\x65\x63\153\x6f\165\x74\x5f\x67\145\164\137\x76\x61\154\165\145")) {
                goto o1;
            }
            $SR["\x61\x70\x70\x6e\141\155\x65"] = $vP;
            o1:
            do_action("\x6d\157\x5f\141\142\x72\x5f\146\151\154\x74\x65\x72\x5f\154\x6f\147\151\156", $SR);
            $this->handle_sso($vP, $xA, $SR, $Ql, $pM);
        } catch (Exception $tS) {
            $mx->handle_error($tS->getMessage());
            MO_Oauth_Debug::mo_oauth_log($tS->getMessage());
            do_action("\x6d\x6f\137\162\145\144\151\x72\x65\143\x74\137\164\157\137\143\165\x73\x74\x6f\155\x5f\145\x72\162\157\x72\x5f\x70\x61\x67\x65");
            exit(esc_html($tS->getMessage()));
        }
        UT:
    }
    public function dropdownattrmapping($fg, $mF, $gu)
    {
        global $mx;
        foreach ($mF as $NZ => $We) {
            if (is_array($We)) {
                goto A9;
            }
            if (!empty($fg)) {
                goto es;
            }
            array_push($gu, $NZ);
            goto Jy;
            es:
            array_push($gu, $fg . "\x2e" . $NZ);
            Jy:
            goto nf;
            A9:
            if (empty($fg)) {
                goto mL;
            }
            $fg .= "\x2e";
            mL:
            $gu = $this->dropdownattrmapping($fg . $NZ, $We, $gu);
            $fg = rtrim($fg, "\56");
            nf:
            Sj:
        }
        z9:
        return $gu;
    }
    public function resolve_and_get_oidc_response($pM = array())
    {
        if (!empty($pM)) {
            goto xI;
        }
        throw new \Exception("\x54\157\153\x65\x6e\x20\162\x65\x73\160\x6f\156\163\145\x20\x69\x73\x20\145\x6d\x70\x74\171", "\151\x6e\166\141\154\x69\x64\x5f\162\145\x73\160\x6f\x6e\x73\x65");
        xI:
        global $mx;
        $JX = isset($pM["\151\x64\x5f\x74\157\x6b\x65\156"]) ? $pM["\151\144\137\164\157\x6b\145\x6e"] : false;
        $j6 = isset($pM["\141\x63\x63\x65\163\x73\x5f\164\x6f\153\145\156"]) ? $pM["\x61\143\x63\x65\163\x73\137\164\157\x6b\145\x6e"] : false;
        $_SESSION["\160\x72\x6f\x63\x6f\x72\145\x5f\x61\143\x63\145\x73\163\x5f\x74\157\x6b\145\156"] = isset($j6) ? $j6 : $JX;
        if (!$mx->is_valid_jwt($JX)) {
            goto ck;
        }
        return $JX;
        ck:
        if (!$mx->is_valid_jwt($j6)) {
            goto nP;
        }
        return $j6;
        nP:
        MO_Oauth_Debug::mo_oauth_log("\124\x6f\153\145\x6e\x20\151\163\40\x6e\157\x74\40\x61\40\x76\141\x6c\x69\x64\40\112\x57\124\56");
        throw new \Exception("\124\157\153\145\156\x20\151\163\x20\156\157\164\40\141\x20\x76\141\154\151\x64\40\x4a\127\124\56");
    }
    public function handle_group_test_conf($SR = array(), $xA = array(), $j6 = '', $eO = false, $cz = false)
    {
        $this->render_test_config_output($SR, false);
    }
    public function testattrmappingconfig($fg, $mF)
    {
        foreach ($mF as $NZ => $We) {
            if (is_array($We) || is_object($We)) {
                goto X7;
            }
            echo "\x3c\164\162\x3e\x3c\164\x64\x3e";
            if (empty($fg)) {
                goto kx;
            }
            echo $fg . "\56";
            kx:
            echo $NZ . "\x3c\57\164\144\76\74\164\144\x3e" . $We . "\74\x2f\164\x64\76\74\x2f\164\x72\76";
            goto rg;
            X7:
            if (empty($fg)) {
                goto m_;
            }
            $fg .= "\x2e";
            m_:
            $this->testattrmappingconfig($fg . $NZ, $We);
            $fg = rtrim($fg, "\56");
            rg:
            VE:
        }
        Yb:
    }
    public function render_test_config_output($SR, $eO = false)
    {
        MO_Oauth_Debug::mo_oauth_log("\x54\x68\151\163\40\x69\x73\40\x74\145\163\x74\40\143\157\x6e\x66\151\147\x75\162\x61\164\x69\x6f\x6e\x20\146\154\157\x77\40\x3d\76\x20");
        echo "\74\x64\x69\166\40\163\164\x79\154\x65\75\42\x66\157\x6e\164\55\x66\x61\x6d\151\154\x79\72\103\141\154\x69\142\162\x69\x3b\x70\141\144\x64\151\x6e\x67\72\60\x20\x33\45\73\x22\76";
        echo "\74\x73\164\171\x6c\x65\76\164\141\x62\x6c\x65\x7b\142\x6f\x72\x64\x65\162\55\143\157\154\x6c\x61\x70\163\x65\72\143\x6f\x6c\154\141\x70\163\x65\x3b\x7d\x74\150\40\173\142\141\143\153\x67\162\x6f\x75\156\x64\x2d\143\x6f\x6c\157\162\x3a\x20\43\145\x65\x65\x3b\x20\x74\145\x78\x74\x2d\x61\154\151\147\x6e\72\40\x63\145\156\x74\145\162\x3b\x20\x70\141\144\144\x69\156\x67\72\40\x38\160\170\x3b\x20\x62\157\162\144\x65\162\55\167\x69\x64\164\150\72\61\x70\170\73\x20\142\157\162\144\145\x72\55\x73\164\x79\154\x65\x3a\163\157\154\x69\x64\x3b\x20\x62\157\162\x64\145\162\x2d\143\157\x6c\x6f\x72\72\x23\62\61\x32\61\x32\x31\73\x7d\x74\162\x3a\x6e\164\150\x2d\x63\x68\151\x6c\x64\50\157\x64\144\51\40\173\142\141\x63\x6b\147\x72\x6f\165\x6e\x64\55\x63\x6f\154\157\x72\x3a\x20\x23\x66\x32\146\x32\146\x32\x3b\x7d\x20\164\144\x7b\x70\141\144\144\151\156\x67\x3a\x38\x70\170\73\x62\157\162\x64\x65\162\x2d\x77\x69\144\x74\x68\x3a\x31\x70\170\x3b\x20\142\157\x72\x64\145\x72\x2d\x73\x74\171\x6c\x65\x3a\163\x6f\154\151\x64\x3b\40\x62\157\162\144\x65\162\x2d\x63\157\x6c\157\x72\72\43\x32\61\x32\x31\62\x31\x3b\x7d\74\57\x73\164\x79\154\x65\x3e";
        echo "\74\150\x32\76";
        echo $eO ? "\107\162\x6f\165\x70\x20\111\156\x66\x6f" : "\x54\145\163\x74\40\x43\x6f\x6e\146\151\147\x75\x72\x61\x74\x69\x6f\156";
        echo "\74\x2f\150\x32\76\x3c\164\141\142\154\x65\76\x3c\x74\162\x3e\74\164\x68\76\101\x74\x74\162\151\x62\165\x74\x65\40\116\141\155\145\74\x2f\164\x68\76\x3c\164\x68\76\101\164\164\162\151\142\165\x74\145\x20\x56\x61\154\x75\x65\x3c\57\x74\150\x3e\74\57\164\162\x3e";
        $this->testattrmappingconfig('', $SR);
        echo "\x3c\57\164\141\142\x6c\145\76";
        if ($eO) {
            goto ey;
        }
        echo "\74\x64\151\166\40\163\x74\x79\154\145\x3d\x22\160\x61\x64\x64\x69\x6e\147\x3a\x20\x31\x30\x70\x78\x3b\42\76\x3c\57\144\151\x76\76\74\151\156\x70\165\164\x20\x73\x74\171\154\145\75\42\160\141\x64\144\x69\156\x67\72\61\45\x3b\167\x69\x64\x74\x68\72\61\x30\60\x70\170\73\142\141\143\x6b\147\162\157\165\x6e\x64\x3a\40\x23\60\60\71\61\103\104\40\156\x6f\x6e\145\x20\x72\145\x70\145\x61\x74\x20\163\143\x72\157\154\154\40\x30\45\40\60\x25\x3b\x63\165\x72\x73\157\162\x3a\40\x70\x6f\151\x6e\x74\x65\x72\73\x66\x6f\156\164\55\163\x69\172\x65\72\x31\x35\160\170\73\x62\x6f\x72\144\145\x72\x2d\x77\151\x64\x74\x68\x3a\40\x31\160\170\x3b\x62\x6f\x72\x64\x65\x72\55\x73\x74\171\x6c\x65\72\40\163\x6f\x6c\x69\144\73\x62\x6f\162\x64\145\x72\55\x72\141\x64\151\165\163\72\40\63\x70\170\x3b\x77\x68\x69\x74\x65\55\163\x70\141\143\x65\72\40\156\157\x77\x72\141\x70\73\142\x6f\170\x2d\x73\151\172\x69\156\147\x3a\40\x62\x6f\x72\144\x65\162\x2d\x62\x6f\x78\73\142\x6f\162\144\x65\x72\55\x63\157\x6c\157\x72\x3a\x20\43\60\x30\67\x33\x41\x41\x3b\x62\x6f\x78\55\163\150\x61\x64\x6f\x77\x3a\40\60\160\x78\40\x31\x70\170\x20\x30\160\x78\x20\x72\147\x62\x61\x28\61\x32\x30\54\40\x32\60\x30\54\x20\x32\x33\60\x2c\40\60\x2e\x36\x29\x20\x69\x6e\163\145\x74\x3b\x63\157\154\x6f\x72\72\40\43\106\x46\x46\x3b\x22\164\x79\x70\x65\75\x22\142\x75\164\164\157\x6e\42\x20\166\x61\x6c\x75\145\x3d\42\x44\157\156\x65\x22\x20\157\x6e\103\154\x69\x63\x6b\75\42\163\x65\x6c\x66\56\143\x6c\157\163\145\x28\x29\x3b\x22\76\74\57\x64\x69\x76\x3e";
        ey:
    }
    public function handle_sso($vP, $xA, $SR, $Ql, $pM, $OR = false)
    {
        MO_Oauth_Debug::mo_oauth_log("\123\x53\117\40\150\141\x6e\144\154\x69\156\147\40\146\x6c\x6f\167");
        global $mx;
        if (!(get_class($this) === "\115\157\x4f\x61\165\x74\x68\x43\154\x69\x65\x6e\x74\134\x4c\157\x67\151\156\110\141\x6e\x64\154\145\x72" && $mx->check_versi(1))) {
            goto Z2;
        }
        $sr = new \MoOauthClient\Base\InstanceHelper();
        $TF = $sr->get_login_handler_instance();
        $TF->handle_sso($vP, $xA, $SR, $Ql, $pM, $OR);
        Z2:
        $KG = isset($xA["\156\141\155\145\137\x61\x74\164\162"]) ? $xA["\156\141\x6d\145\x5f\141\164\x74\x72"] : '';
        $aQ = isset($xA["\145\x6d\x61\x69\x6c\x5f\x61\164\x74\x72"]) ? $xA["\145\155\x61\x69\154\x5f\141\x74\x74\162"] : '';
        $UU = $mx->getnestedattribute($SR, $aQ);
        $O6 = $mx->getnestedattribute($SR, $KG);
        if (!empty($UU)) {
            goto VP;
        }
        MO_Oauth_Debug::mo_oauth_log("\x45\155\141\x69\x6c\40\x61\x64\x64\162\145\x73\x73\x20\x6e\157\x74\x20\x72\x65\143\x65\151\x76\x65\x64\x2e\x20\x43\150\x65\143\x6b\x20\x79\x6f\165\x72\x20\101\164\x74\162\x69\x62\x75\164\x65\40\115\141\160\x70\151\156\147\40\143\x6f\156\146\151\147\x75\x72\x61\x74\151\x6f\156\x2e");
        wp_die("\105\155\141\151\154\x20\x61\x64\x64\x72\x65\163\163\40\x6e\157\164\40\162\x65\143\145\151\x76\145\x64\x2e\x20\x43\150\145\x63\153\x20\171\x6f\165\x72\x20\x3c\x73\164\x72\157\156\x67\x3e\x41\x74\x74\x72\x69\142\x75\164\x65\40\x4d\x61\160\x70\151\156\x67\74\x2f\x73\164\162\157\156\x67\x3e\40\x63\157\x6e\146\151\147\x75\162\x61\x74\151\x6f\156\x2e");
        VP:
        if (!(false === strpos($UU, "\100"))) {
            goto LF;
        }
        MO_Oauth_Debug::mo_oauth_log("\115\x61\x70\x70\145\144\x20\105\155\141\151\154\x20\x61\x74\164\162\151\142\165\x74\x65\40\144\x6f\145\x73\x20\156\x6f\164\40\x63\157\x6e\164\141\151\156\40\x76\x61\x6c\x69\144\40\145\x6d\x61\151\154\56");
        wp_die("\x4d\141\x70\x70\145\x64\x20\105\155\141\151\x6c\x20\141\164\164\162\x69\x62\165\164\145\x20\144\157\x65\x73\40\x6e\157\164\x20\143\157\156\164\141\x69\x6e\40\x76\x61\x6c\151\144\40\145\155\141\151\x6c\56");
        LF:
        $user = get_user_by("\x6c\157\147\151\x6e", $UU);
        if ($user) {
            goto JC;
        }
        $user = get_user_by("\145\x6d\141\x69\x6c", $UU);
        JC:
        if ($user) {
            goto QX;
        }
        $b2 = 0;
        if ($mx->mo_oauth_hbca_xyake()) {
            goto mf;
        }
        $user = $mx->mo_oauth_hjsguh_kiishuyauh878gs($UU, $O6);
        goto H9;
        mf:
        if ($mx->mo_oauth_client_get_option("\x6d\x6f\137\x6f\x61\165\x74\x68\x5f\x66\154\x61\147") !== true) {
            goto GC;
        }
        $HG = base64_decode("\x50\x47\x52\160\x64\x69\x42\x7a\x64\110\x6c\x73\132\124\x30\x6e\144\x47\x56\64\144\103\61\150\142\107\x6c\x6e\x62\x6a\x70\152\132\x57\65\60\x5a\x58\111\x37\112\x7a\x34\x38\x59\152\65\126\x63\62\126\171\111\x45\106\x6a\x59\x32\71\61\142\156\121\147\132\x47\71\x6c\x63\171\x42\165\142\x33\121\147\x5a\130\x68\160\x63\63\x51\x75\120\x43\71\x69\x50\x6a\167\x76\x5a\x47\x6c\62\120\x6a\170\x69\x63\x6a\64\70\143\62\61\x68\142\x47\x77\x2b\126\x47\150\x70\x63\x79\102\62\132\x58\112\172\x61\127\x39\x75\111\110\x4e\x31\x63\110\x42\166\143\x6e\x52\172\111\105\106\61\144\107\x38\147\x51\x33\x4a\154\131\130\122\154\111\x46\126\172\132\130\x49\x67\132\155\126\150\144\110\126\x79\132\x53\102\61\143\110\x52\166\x49\104\105\x77\x49\x46\126\x7a\132\x58\x4a\x7a\x4c\151\102\x51\x62\107\x56\150\143\62\125\x67\144\x58\x42\x6e\143\x6d\106\153\x5a\x53\x42\60\x62\171\x42\x30\141\x47\x55\147\141\x47\x6c\x6e\x61\107\x56\x79\111\110\x5a\x6c\x63\156\116\x70\142\62\x34\x67\142\62\131\x67\144\x47\x68\154\x49\110\102\x73\144\x57\x64\160\142\151\x42\x30\x62\171\x42\154\x62\x6d\x46\151\x62\x47\125\147\x59\130\126\x30\142\171\x42\x6a\x63\155\126\x68\x64\107\125\147\x64\130\116\154\143\x69\x42\155\142\63\111\x67\x64\127\65\x73\x61\x57\x31\160\144\107\x56\x6b\x49\x48\x56\x7a\132\130\x4a\x7a\x49\107\71\171\x49\x47\106\x6b\x5a\103\102\61\x63\x32\126\171\111\x47\61\x68\142\156\x56\x68\x62\x47\x78\x35\114\152\167\x76\x63\62\61\150\x62\x47\167\x2b");
        MO_Oauth_Debug::mo_oauth_log($HG);
        wp_die($HG);
        goto R2;
        GC:
        $user = $mx->mo_oauth_jhuyn_jgsukaj($UU, $O6);
        R2:
        H9:
        goto Ix;
        QX:
        $b2 = $user->ID;
        Ix:
        if (!$user) {
            goto xt;
        }
        wp_set_current_user($user->ID);
        MO_Oauth_Debug::mo_oauth_log("\x55\x73\x65\162\40\106\157\x75\156\x64");
        $fr = false;
        $fr = apply_filters("\x6d\157\137\162\x65\155\145\155\x62\145\x72\137\155\145", $fr);
        if (!$fr) {
            goto JU;
        }
        MO_Oauth_Debug::mo_oauth_log("\x52\x65\x6d\145\x6d\x62\x65\162\x20\x41\144\144\157\x6e\x20\x61\x63\164\151\166\141\164\145\x64");
        JU:
        wp_set_auth_cookie($user->ID, $fr);
        MO_Oauth_Debug::mo_oauth_log("\125\163\x65\x72\x20\x63\157\x6f\153\x69\145\x20\x73\145\x74");
        $user = get_user_by("\x49\104", $user->ID);
        do_action("\x77\x70\137\x6c\157\x67\151\156", $user->user_login, $user);
        wp_safe_redirect(home_url());
        MO_Oauth_Debug::mo_oauth_log("\125\x73\x65\162\x20\122\x65\x64\151\162\145\x63\x74\145\x64\x20\x74\157\40\150\157\155\x65\x20\165\162\x6c");
        exit;
        xt:
    }
    public function get_resource_owner_from_app($JX, $pY)
    {
        return $this->oauth_handler->get_resource_owner_from_id_token($JX);
    }
}
