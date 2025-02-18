<?php


namespace MoOauthClient\GrantTypes;

use MoOauthClient\GrantTypes\Implicit;
use MoOauthClient\OauthHandler;
use MoOauthClient\StorageManager;
use MoOauthClient\Base\InstanceHelper;
use MoOauthClient\LoginHandler;
use MoOauthClient\MO_Oauth_Debug;
class Password
{
    const CSS_URL = MOC_URL . "\x63\x6c\141\163\163\145\x73\x2f\120\162\145\x6d\151\x75\155\x2f\x72\x65\x73\x6f\x75\162\x63\x65\163\57\x70\x77\x64\x73\x74\171\x6c\x65\x2e\x63\163\163";
    const JS_URL = MOC_URL . "\x63\x6c\141\x73\x73\x65\x73\x2f\x50\x72\145\155\151\x75\x6d\x2f\x72\145\163\157\x75\162\143\145\163\x2f\x70\167\144\x2e\152\x73";
    public function __construct($OR = false)
    {
        if (!$OR) {
            goto p12;
        }
        return;
        p12:
        add_action("\x69\x6e\x69\164", array($this, "\142\145\150\x61\x76\145"));
    }
    public function inject_ui()
    {
        global $mx;
        wp_enqueue_style("\x77\160\55\x6d\x6f\x2d\157\143\55\x70\167\x64\55\143\x73\163", self::CSS_URL, array(), $LB = null, $KK = false);
        $V3 = $mx->parse_url($mx->get_current_url());
        $KU = "\x62\x75\164\x74\157\156";
        if (!isset($V3["\x71\165\x65\162\x79"]["\x6c\x6f\x67\x69\156"])) {
            goto AHR;
        }
        return;
        AHR:
        echo "\x9\x9\74\144\151\166\x20\x69\x64\75\x22\160\141\x73\163\167\x6f\x72\144\55\147\x72\141\x6e\164\x2d\x6d\x6f\144\x61\x6c\x22\40\143\154\141\163\x73\75\42\x70\x61\163\x73\167\157\x72\x64\x2d\x6d\157\144\x61\x6c\x20\x6d\157\137\164\141\x62\154\145\x5f\154\141\x79\x6f\x75\x74\42\76\xd\xa\x9\11\x9\74\144\x69\166\40\x63\154\x61\163\x73\x3d\42\160\x61\x73\x73\x77\157\x72\144\55\155\157\144\141\x6c\55\143\x6f\x6e\x74\x65\x6e\164\x22\76\xd\xa\x9\11\11\x9\x3c\x64\151\x76\x20\x63\154\x61\x73\x73\75\42\160\x61\x73\x73\167\x6f\x72\x64\55\155\x6f\144\x61\154\55\x68\x65\x61\x64\145\162\42\x3e\15\xa\x9\11\11\x9\x9\x3c\144\x69\166\x20\143\x6c\141\163\163\x3d\42\160\141\163\x73\167\x6f\162\x64\55\155\x6f\x64\x61\x6c\x2d\150\145\141\144\145\162\x2d\164\x69\x74\154\145\42\x3e\xd\12\x9\x9\11\x9\x9\x9\74\x73\x70\x61\x6e\x20\143\154\x61\163\163\x3d\x22\x70\141\163\x73\167\x6f\x72\x64\55\155\157\144\141\154\x2d\x63\x6c\x6f\x73\145\42\x3e\x26\164\x69\x6d\x65\x73\x3b\74\57\163\x70\141\156\76\xd\12\11\x9\x9\11\11\x9\x3c\163\x70\x61\156\x20\151\144\75\42\x70\x61\163\163\167\x6f\x72\144\55\155\157\x64\x61\154\55\x68\x65\x61\x64\145\x72\55\x74\151\164\154\x65\x2d\164\145\170\x74\x22\x3e\74\x2f\x73\x70\x61\x6e\76\xd\12\x9\11\x9\x9\x9\x3c\x2f\x64\151\x76\76\xd\12\11\x9\11\11\x3c\57\144\151\166\x3e\15\12\x9\x9\11\x9\74\146\157\162\155\x20\x69\x64\75\x22\160\x77\144\147\x72\156\164\146\162\x6d\x22\x3e\xd\12\11\x9\11\11\x9\74\151\156\x70\x75\164\x20\164\x79\x70\145\75\x22\150\151\144\x64\145\156\x22\40\x6e\141\155\145\75\42\154\157\x67\151\156\x22\x20\x76\x61\x6c\x75\x65\75\42\160\x77\144\147\162\156\164\x66\x72\x6d\x22\x3e\xd\xa\11\x9\x9\x9\11\x3c\x69\156\160\165\164\x20\x74\x79\160\x65\x3d\42\164\x65\170\164\42\x20\x63\x6c\x61\163\163\75\x22\155\x6f\137\164\x61\x62\154\145\x5f\164\x65\x78\x74\x62\157\170\42\x20\151\x64\x3d\42\x70\x77\144\x67\x72\x6e\x74\146\x72\155\x2d\x75\x6e\155\x66\x6c\144\x22\x20\x6e\141\x6d\x65\x3d\x22\143\x61\154\x6c\x65\162\x22\x20\160\x6c\x61\x63\x65\150\x6f\x6c\x64\145\162\75\42\125\x73\145\162\x6e\x61\155\x65\42\76\15\12\11\x9\x9\11\x9\74\151\x6e\160\165\164\x20\164\171\160\x65\75\42\x70\x61\163\x73\x77\157\x72\144\42\40\143\x6c\141\163\163\75\x22\x6d\157\137\x74\141\142\x6c\x65\137\x74\x65\x78\x74\142\157\170\x22\x20\x69\x64\75\x22\x70\167\x64\x67\162\156\x74\146\162\x6d\55\160\146\x6c\x64\42\x20\x6e\141\x6d\145\x3d\x22\164\x6f\157\x6c\42\40\160\154\141\x63\x65\150\x6f\x6c\144\145\162\75\42\120\x61\x73\163\x77\157\162\144\x22\x3e\15\xa\x9\11\11\11\x9\74\x69\x6e\160\165\164\x20\x74\171\x70\x65\75\42";
        echo $KU;
        echo "\x22\40\143\154\141\163\163\x3d\42\142\x75\x74\164\157\x6e\40\x62\165\x74\164\x6f\x6e\x2d\160\162\151\x6d\141\162\x79\40\x62\165\x74\164\157\156\55\x6c\141\162\147\145\42\40\x69\x64\75\x22\160\x77\x64\x67\x72\x6e\x74\x66\162\155\x2d\154\x6f\x67\151\156\x22\40\x76\x61\x6c\165\x65\x3d\x22\114\157\x67\x69\x6e\42\x3e\xd\xa\11\11\11\x9\x3c\57\146\157\162\155\76\15\xa\11\11\x9\74\x2f\x64\x69\x76\x3e\15\xa\11\11\74\x2f\144\x69\x76\x3e\15\xa\11\x9";
    }
    public function inject_behaviour()
    {
        wp_enqueue_script("\x77\160\x2d\155\x6f\55\157\x63\55\x70\x77\x64\x2d\152\163", self::JS_URL, ["\152\161\165\145\162\171"], $LB = null, $KK = true);
    }
    public function behave($Ok = '', $jM = '', $pY = '', $mk = '', $uv = false, $OR = false)
    {
        global $mx;
        $Ok = !empty($Ok) ? hex2bin($Ok) : false;
        $jM = !empty($jM) ? hex2bin($jM) : false;
        $pY = !empty($pY) ? $pY : false;
        $mk = !empty($mk) ? $mk : site_url();
        if (!($jM && !$uv)) {
            goto u1I;
        }
        $jM = wp_unslash($jM);
        u1I:
        if (!(!$Ok || !$jM || !$pY)) {
            goto hrB;
        }
        $mx->redirect_user(urldecode($mk));
        exit;
        hrB:
        $Zy = $mx->get_app_by_name($pY);
        if ($Zy) {
            goto m3S;
        }
        $wf = $mx->parse_url(urldecode(site_url()));
        $wf["\161\165\x65\x72\171"]["\145\162\162\x6f\x72"] = "\124\x68\145\x72\145\x20\151\163\40\156\157\x20\141\x70\x70\154\x69\x63\x61\x74\151\x6f\x6e\x20\143\157\156\146\151\147\x75\x72\145\144\40\146\x6f\162\x20\164\x68\151\x73\40\x72\x65\161\165\x65\x73\164";
        $mx->redirect_user($mx->generate_url($wf));
        m3S:
        $xA = $Zy->get_app_config();
        $x1 = array("\147\x72\141\156\164\137\x74\x79\x70\x65" => "\x70\x61\x73\163\167\157\x72\x64", "\143\x6c\x69\x65\156\164\137\151\x64" => $xA["\143\154\151\x65\x6e\x74\137\x69\x64"], "\x63\154\x69\145\x6e\x74\137\x73\x65\143\x72\x65\164" => $xA["\143\154\151\145\156\164\x5f\x73\x65\143\162\x65\x74"], "\165\x73\x65\162\x6e\x61\155\145" => $Ok, "\x70\141\163\163\167\157\162\x64" => $jM, "\151\163\137\x77\x70\x5f\x6c\x6f\147\151\x6e" => $OR);
        $vv = new OauthHandler();
        $rv = $xA["\x61\x63\143\145\163\x73\164\157\x6b\145\x6e\x75\162\x6c"];
        if (!(strpos($rv, "\147\x6f\157\x67\154\145") !== false)) {
            goto rYg;
        }
        $rv = "\150\x74\x74\160\x73\x3a\57\x2f\x77\167\x77\x2e\147\157\x6f\x67\x6c\x65\x61\160\x69\163\56\143\157\155\x2f\x6f\x61\x75\164\150\x32\x2f\x76\64\x2f\x74\157\153\145\156";
        rYg:
        if (!(strpos($rv, "\163\145\162\x76\x69\143\145\x73\x2f\157\x61\165\x74\x68\x32\x2f\x74\x6f\153\145\156") === false && strpos($rv, "\163\141\154\145\163\146\157\162\x63\x65") === false && strpos($xA["\141\143\143\145\x73\163\164\157\153\x65\156\165\162\154"], "\57\157\x61\x6d\x2f\x6f\141\165\x74\x68\62\x2f\141\x63\x63\x65\x73\163\137\164\157\153\145\x6e") === false)) {
            goto Q6o;
        }
        $x1["\x73\x63\x6f\x70\x65"] = $Zy->get_app_config("\163\143\x6f\x70\145");
        Q6o:
        $Ao = isset($xA["\x73\x65\x6e\x64\x5f\150\x65\x61\144\x65\x72\x73"]) ? $xA["\x73\145\156\144\137\150\145\141\144\x65\162\163"] : 0;
        $Qq = isset($xA["\x73\x65\x6e\x64\137\x62\x6f\x64\171"]) ? $xA["\163\x65\156\x64\137\x62\157\144\171"] : 0;
        do_action("\x6d\157\137\x67\x65\x73\143\x6f\154\x5f\150\x61\x6e\144\x6c\x65\x72", $Ok, $jM, $pY);
        $pM = $vv->get_access_token($rv, $x1, $Ao, $Qq);
        if (!is_wp_error($pM)) {
            goto eBI;
        }
        return $pM;
        eBI:
        MO_Oauth_Debug::mo_oauth_log("\124\157\153\x65\x6e\40\x52\x65\x73\x70\x6f\156\163\145\x20\x52\x65\x63\x65\151\166\145\144\x20\75\76\40");
        MO_Oauth_Debug::mo_oauth_log($pM);
        if ($pM) {
            goto CJ2;
        }
        $lM = new \WP_Error();
        $lM->add("\151\x6e\x76\141\154\x69\144\137\160\141\x73\x73\167\157\162\144", __("\x3c\x73\x74\x72\157\x6e\147\x3e\105\x52\x52\x4f\x52\x3c\57\x73\x74\x72\157\156\x67\x3e\72\40\x49\x6e\x63\x6f\162\x72\x65\143\164\40\x45\x6d\x61\x69\154\x20\141\144\x64\162\x65\163\163\40\x6f\x72\40\x50\x61\x73\x73\167\157\x72\x64\x2e"));
        return $lM;
        CJ2:
        $j6 = isset($pM["\x61\143\x63\x65\163\x73\137\x74\x6f\x6b\145\156"]) ? $pM["\x61\x63\143\145\163\x73\x5f\164\x6f\x6b\145\x6e"] : false;
        $JX = isset($pM["\151\144\x5f\x74\157\153\x65\156"]) ? $pM["\151\x64\137\164\157\x6b\x65\x6e"] : false;
        $sm = isset($pM["\x74\157\153\x65\156"]) ? $pM["\x74\x6f\x6b\145\x6e"] : false;
        $SR = [];
        if (false !== $JX || false !== $sm) {
            goto USh;
        }
        if ($j6) {
            goto h_Y;
        }
        $mx->handle_error("\x49\x6e\166\141\154\x69\x64\x20\164\157\153\x65\156\40\x72\x65\143\x65\x69\x76\x65\144\56");
        MO_Oauth_Debug::mo_oauth_log("\x45\162\x72\x6f\x72\40\x66\162\157\x6d\x20\x54\157\153\x65\x6e\40\105\156\x64\x70\157\x69\x6e\164\x20\75\x3e\x20\x49\x6e\166\x61\x6c\151\144\40\x74\x6f\x6b\x65\156\40\x72\145\143\145\151\x76\145\144");
        exit("\111\156\166\x61\x6c\x69\x64\x20\x74\157\x6b\x65\156\x20\x72\x65\143\145\151\x76\145\144\56");
        h_Y:
        goto tgz;
        USh:
        $TL = '';
        if (!(false !== $sm)) {
            goto ebo;
        }
        $TL = "\164\x6f\x6b\x65\x6e\75" . $sm;
        ebo:
        if (!(false !== $JX)) {
            goto ZMp;
        }
        $TL = "\151\x64\137\x74\x6f\153\145\x6e\x3d" . $JX;
        ZMp:
        $QP = new Implicit($TL);
        if (!is_wp_error($QP)) {
            goto Z6e;
        }
        $mx->handle_error($QP->get_error_message());
        MO_Oauth_Debug::mo_oauth_log($QP->get_error_message());
        wp_die(wp_kses($QP->get_error_message(), \mo_oauth_get_valid_html()));
        exit("\x50\154\145\141\163\x65\x20\x74\162\171\x20\x4c\157\147\x67\x69\x6e\147\40\x69\156\x20\x61\x67\141\151\156\x2e");
        Z6e:
        $CS = $QP->get_jwt_from_query_param();
        $SR = $CS->get_decoded_payload();
        tgz:
        $yZ = $xA["\x72\145\163\x6f\x75\162\x63\145\157\167\156\145\162\144\145\x74\x61\151\x6c\163\165\162\154"];
        if (!(substr($yZ, -1) === "\75")) {
            goto t2P;
        }
        $yZ .= $j6;
        t2P:
        if (!(strpos($yZ, "\x67\157\157\147\x6c\x65") !== false)) {
            goto PE6;
        }
        $yZ = "\150\164\x74\x70\163\72\x2f\x2f\x77\167\x77\56\x67\157\x6f\147\x6c\x65\x61\x70\151\x73\x2e\x63\157\155\x2f\x6f\x61\x75\164\150\62\x2f\x76\61\57\x75\x73\x65\162\151\x6e\146\157";
        PE6:
        if (empty($yZ)) {
            goto T9K;
        }
        $SR = $vv->get_resource_owner($yZ, $j6);
        T9K:
        MO_Oauth_Debug::mo_oauth_log("\x52\145\163\157\x75\162\x63\x65\40\117\x77\156\x65\162\x20\x3d\x3e\40");
        MO_Oauth_Debug::mo_oauth_log($SR);
        $sr = new InstanceHelper();
        $TF = $sr->get_login_handler_instance();
        $gu = [];
        $Yc = new LoginHandler();
        $BW = $Yc->dropdownattrmapping('', $SR, $gu);
        $mx->mo_oauth_client_update_option("\x6d\x6f\x5f\157\x61\x75\x74\150\137\x61\164\x74\x72\137\x6e\x61\x6d\x65\137\154\x69\163\x74" . $pY, $BW);
        if (!$uv) {
            goto dxU;
        }
        $TF->handle_group_test_conf($SR, $xA, $j6, false, $uv);
        exit;
        dxU:
        $blog_id = get_current_blog_id();
        $Nh = new StorageManager();
        $Nh->add_replace_entry("\162\145\144\x69\x72\x65\x63\164\137\165\x72\x69", $mk);
        $Nh->add_replace_entry("\142\x6c\157\x67\137\x69\144", $blog_id);
        $Ql = $Nh->get_state();
        $user = $TF->handle_sso($xA["\x61\x70\160\111\x64"], $xA, $SR, $Ql, $pM, $OR);
        if (!$OR) {
            goto sux;
        }
        return $user;
        sux:
    }
    public function mo_oauth_wp_login($user, $Lj, $oa)
    {
        global $mx;
        $lM = new \WP_Error();
        if (!(empty($Lj) || empty($oa))) {
            goto HoG;
        }
        if (!empty($Lj)) {
            goto tJV;
        }
        $lM->add("\145\155\x70\x74\x79\x5f\x75\x73\x65\x72\156\141\155\145", __("\x3c\x73\164\162\157\x6e\x67\76\105\122\122\117\122\74\57\163\x74\162\157\156\147\x3e\72\x20\x45\155\x61\x69\x6c\x20\146\x69\x65\154\x64\x20\151\x73\x20\145\x6d\160\x74\x79\x2e"));
        tJV:
        if (!empty($oa)) {
            goto mZZ;
        }
        $lM->add("\145\x6d\160\x74\x79\137\x70\x61\x73\163\167\157\x72\144", __("\x3c\163\164\162\157\156\x67\x3e\x45\x52\122\117\x52\x3c\57\163\164\162\157\x6e\147\x3e\x3a\40\x50\141\163\x73\167\x6f\x72\x64\x20\146\x69\x65\154\x64\x20\151\163\40\x65\155\x70\164\x79\x2e"));
        mZZ:
        return $lM;
        HoG:
        $pY = $mx->mo_oauth_client_get_option("\155\157\x5f\157\x61\165\164\x68\x5f\145\156\141\x62\x6c\145\x5f\157\x61\165\164\x68\x5f\167\160\x5f\154\157\x67\151\156");
        $user = false;
        if (\username_exists($Lj)) {
            goto a7c;
        }
        if (!email_exists($Lj)) {
            goto HFc;
        }
        $user = get_user_by("\145\155\141\x69\154", $Lj);
        HFc:
        goto LE8;
        a7c:
        $user = \get_user_by("\x6c\157\x67\x69\156", $Lj);
        LE8:
        if (!($user && wp_check_password($oa, $user->data->user_pass, $user->ID))) {
            goto e9o;
        }
        return $user;
        e9o:
        if (!(false !== $pY)) {
            goto wQg;
        }
        $ZV = '';
        $ZV = do_action("\155\157\137\x6f\141\165\x74\x68\x5f\x63\x75\x73\164\x6f\x6d\137\x73\163\157", \bin2hex($Lj), \bin2hex($oa), $pY, site_url(), false, true);
        if (empty($ZV)) {
            goto cAM;
        }
        return $ZV;
        cAM:
        return $this->behave(\bin2hex($Lj), \bin2hex($oa), $pY, site_url(), false, true);
        wQg:
        $lM->add("\151\156\x76\141\x6c\151\x64\137\x70\x61\163\163\167\157\162\x64", __("\x3c\x73\x74\162\157\x6e\147\76\x45\x52\122\117\122\74\57\x73\x74\x72\x6f\x6e\147\76\72\40\x55\x73\x65\162\156\x61\155\145\40\157\162\40\120\x61\x73\163\167\157\162\144\x20\x69\163\x20\x69\x6e\x76\141\x6c\x69\x64\56"));
        MO_Oauth_Debug::mo_oauth_log($lM);
        return $lM;
    }
}
