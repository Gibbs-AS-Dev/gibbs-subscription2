<?php


namespace MoOauthClient\Standard;

use MoOauthClient\LoginHandler as FreeLoginHandler;
use MoOauthClient\Config;
use MoOauthClient\StorageManager;
use MoOauthClient\MO_Oauth_Debug;
class LoginHandler extends FreeLoginHandler
{
    public $config;
    public function handle_group_test_conf($SR = array(), $xA = array(), $j6 = '', $eO = false, $cz = false)
    {
        global $mx;
        $this->render_test_config_output($SR, false);
        if (!(!isset($xA["\x67\x72\x6f\x75\x70\x64\x65\164\141\x69\154\163\165\x72\x6c"]) || '' === $xA["\x67\162\157\165\160\144\x65\x74\141\x69\x6c\x73\x75\162\x6c"])) {
            goto VIi;
        }
        return;
        VIi:
        $io = [];
        $xL = $xA["\147\162\x6f\x75\160\144\145\x74\141\x69\154\163\x75\162\x6c"];
        if (!(strpos($xL, "\141\160\x69\56\143\x6c\x65\x76\145\x72\56\143\157\155") != false && isset($SR["\x64\141\164\x61"]["\151\x64"]))) {
            goto aa9;
        }
        $xL = str_replace("\x75\x73\x65\162\151\144", $SR["\144\x61\164\141"]["\151\144"], $xL);
        aa9:
        MO_Oauth_Debug::mo_oauth_log("\107\x72\157\x75\x70\40\x44\x65\x74\x61\x69\x6c\163\40\x55\122\114\72\40" . $xL);
        if (!('' === $j6)) {
            goto mMH;
        }
        if (has_filter("\155\157\x5f\x6f\x61\x75\x74\x68\137\x63\x66\141\137\147\162\x6f\165\160\137\x64\x65\x74\141\x69\154\163")) {
            goto dvA;
        }
        MO_Oauth_Debug::mo_oauth_log("\x41\x63\143\x65\163\x73\x20\124\157\x6b\145\x6e\x20\105\155\160\164\171");
        return;
        dvA:
        mMH:
        if (!('' !== $xL)) {
            goto b8k;
        }
        if (has_filter("\x6d\x6f\x5f\157\141\x75\164\x68\x5f\x63\146\141\137\x67\162\157\165\160\137\x64\x65\164\x61\151\x6c\x73")) {
            goto U5a;
        }
        if (has_filter("\155\157\137\x6f\x61\165\164\x68\x5f\x67\x72\x6f\x75\160\137\x64\145\x74\141\151\154\x73")) {
            goto cQm;
        }
        if (has_filter("\155\x6f\137\157\141\165\x74\150\x5f\162\x61\x76\x65\x6e\x5f\147\162\x6f\165\x70\x5f\x64\145\x74\141\151\154\x73")) {
            goto OkO;
        }
        $io = $this->oauth_handler->get_resource_owner($xL, $j6);
        goto KqC;
        OkO:
        $io = apply_filters("\155\157\x5f\x6f\141\165\x74\150\x5f\x72\x61\166\x65\x6e\x5f\147\162\157\x75\160\137\x64\145\x74\x61\x69\x6c\x73", $SR["\x65\155\141\151\x6c"], $xL, $j6, $xA, $eO);
        KqC:
        goto xDW;
        cQm:
        $io = apply_filters("\155\157\x5f\157\141\x75\164\x68\x5f\147\162\x6f\165\160\137\x64\145\x74\x61\151\x6c\163", $xL, $j6, $xA, $eO);
        xDW:
        goto WSq;
        U5a:
        MO_Oauth_Debug::mo_oauth_log("\x46\145\x74\x63\x68\151\x6e\147\40\x43\x46\x41\x20\107\162\157\x75\x70\x2e\56");
        $io = apply_filters("\x6d\157\x5f\157\x61\165\x74\x68\x5f\143\x66\141\137\147\x72\157\165\160\137\x64\x65\164\x61\151\x6c\x73", $SR, $xL, $j6, $xA, $eO);
        WSq:
        $gu = $mx->mo_oauth_client_get_option("\155\x6f\137\x6f\x61\165\164\x68\137\x61\x74\164\x72\137\156\x61\x6d\145\x5f\x6c\x69\163\x74" . $xA["\141\160\160\x49\x64"]);
        $Gp = [];
        $BW = $this->dropdownattrmapping('', $io, $Gp);
        $gu = (array) $gu + $BW;
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\141\165\164\150\x5f\x61\x74\x74\x72\x5f\156\141\x6d\145\137\154\x69\163\164" . $xA["\141\160\x70\x49\144"], $gu);
        if (!($cz && '' !== $cz)) {
            goto V07;
        }
        if (!(is_array($io) && !empty($io))) {
            goto x1n;
        }
        $this->render_test_config_output($io, true);
        x1n:
        return;
        V07:
        b8k:
    }
    public function handle_group_user_info($SR, $xA, $j6)
    {
        if (!(!isset($xA["\x67\x72\157\x75\160\x64\x65\164\x61\x69\x6c\163\165\x72\154"]) || '' === $xA["\147\x72\x6f\165\x70\x64\145\x74\x61\x69\x6c\x73\x75\162\154"])) {
            goto m3B;
        }
        return $SR;
        m3B:
        $xL = $xA["\147\x72\x6f\165\160\x64\x65\x74\141\x69\x6c\163\x75\x72\154"];
        if (!(strpos($xL, "\141\160\x69\56\x63\x6c\x65\x76\145\162\56\x63\x6f\x6d") != false && isset($SR["\x64\141\x74\x61"]["\x69\x64"]))) {
            goto qaO;
        }
        $xL = str_replace("\x75\163\145\x72\x69\144", $SR["\x64\141\x74\x61"]["\151\x64"], $xL);
        qaO:
        if (!('' === $j6)) {
            goto EDp;
        }
        return $SR;
        EDp:
        $io = array();
        if (!('' !== $xL)) {
            goto ymC;
        }
        if (has_filter("\155\x6f\137\x6f\x61\x75\164\x68\137\x63\146\141\x5f\147\162\x6f\x75\160\x5f\144\145\x74\x61\x69\x6c\163")) {
            goto pd9;
        }
        if (has_filter("\155\x6f\137\x6f\x61\x75\x74\150\x5f\147\162\x6f\165\160\137\144\x65\164\x61\151\x6c\163")) {
            goto JLl;
        }
        if (has_filter("\x6d\157\x5f\x6f\141\165\x74\150\137\x72\141\166\x65\x6e\x5f\x67\162\157\165\x70\x5f\144\x65\x74\141\x69\154\x73")) {
            goto oJK;
        }
        $io = $this->oauth_handler->get_resource_owner($xL, $j6);
        goto Pc1;
        oJK:
        $io = apply_filters("\x6d\x6f\x5f\x6f\x61\165\x74\150\137\x72\141\x76\145\x6e\137\147\162\x6f\x75\x70\137\x64\x65\164\141\151\x6c\x73", $SR["\x65\x6d\141\151\154"], $xL, $j6, $xA, $eO);
        Pc1:
        goto ePn;
        JLl:
        $io = apply_filters("\155\157\137\157\141\165\x74\150\x5f\147\x72\157\x75\x70\x5f\144\x65\164\141\151\x6c\163", $xL, $j6, $xA);
        ePn:
        goto NkV;
        pd9:
        MO_Oauth_Debug::mo_oauth_log("\106\x65\164\x63\150\151\156\147\40\103\106\101\40\x47\x72\157\165\160\x2e\x2e");
        $io = apply_filters("\155\x6f\137\x6f\141\165\164\150\137\143\x66\141\x5f\147\x72\x6f\165\x70\137\144\x65\x74\141\x69\x6c\163", $SR, $xL, $j6, $xA, $eO);
        NkV:
        ymC:
        MO_Oauth_Debug::mo_oauth_log("\107\162\x6f\x75\x70\40\x44\145\x74\141\x69\154\163\x20\75\76\x20");
        MO_Oauth_Debug::mo_oauth_log($io);
        if (!(is_array($io) && count($io) > 0)) {
            goto l2v;
        }
        $SR = array_merge_recursive($SR, $io);
        l2v:
        MO_Oauth_Debug::mo_oauth_log("\122\x65\163\157\165\162\x63\145\x20\x4f\x77\156\145\x72\x20\x41\x66\x74\x65\162\40\x6d\145\x72\147\x69\x6e\147\x20\167\x69\x74\150\40\107\x72\157\x75\160\40\x64\x65\x74\151\141\x6c\x73\x20\x3d\x3e\40");
        MO_Oauth_Debug::mo_oauth_log($SR);
        return $SR;
    }
    public function mo_oauth_client_map_default_role($b2, $xA)
    {
        $Ib = new \WP_User($b2);
        if (!(isset($xA["\145\x6e\141\142\154\145\137\x72\157\x6c\145\x5f\155\141\x70\x70\151\156\147"]) && !boolval($xA["\x65\x6e\141\x62\154\x65\137\x72\x6f\154\145\137\155\x61\x70\160\x69\156\x67"]))) {
            goto jnq;
        }
        $Ib->set_role('');
        return;
        jnq:
        if (!(isset($xA["\137\155\x61\160\x70\151\x6e\x67\137\x76\141\x6c\165\x65\137\x64\145\146\141\x75\x6c\x74"]) && '' !== $xA["\x5f\155\141\x70\x70\x69\156\x67\137\x76\141\154\165\145\137\144\145\x66\141\x75\154\164"])) {
            goto wSp;
        }
        $Ib->set_role($xA["\137\155\x61\160\160\x69\156\147\x5f\x76\141\x6c\165\145\137\x64\x65\146\141\x75\x6c\164"]);
        wSp:
    }
    public function handle_sso($vP, $xA, $SR, $Ql, $pM, $OR = false)
    {
        global $mx;
        $Nh = new StorageManager($Ql);
        do_action("\x6d\x6f\137\x6f\x61\x75\164\x68\137\154\151\156\153\x5f\x64\151\163\143\x6f\x72\x64\x5f\141\x63\143\157\165\x6e\x74", $Nh, $SR);
        $Ui = isset($xA["\x75\163\x65\162\156\141\x6d\x65\137\141\x74\x74\x72"]) ? $xA["\165\x73\x65\x72\x6e\x61\x6d\145\137\141\164\164\162"] : '';
        $aQ = isset($xA["\x65\x6d\x61\x69\x6c\137\x61\x74\164\x72"]) ? $xA["\x65\x6d\x61\151\x6c\x5f\x61\164\164\162"] : '';
        $lC = isset($xA["\x66\151\x72\163\x74\156\x61\x6d\145\137\x61\x74\x74\162"]) ? $xA["\x66\x69\162\x73\x74\156\x61\155\x65\137\x61\164\x74\x72"] : '';
        $Zp = isset($xA["\x6c\x61\x73\164\x6e\141\155\145\x5f\141\164\x74\162"]) ? $xA["\x6c\x61\163\164\x6e\x61\x6d\x65\137\141\164\164\x72"] : '';
        $xD = isset($xA["\144\x69\163\x70\154\141\x79\137\x61\164\164\162"]) ? $xA["\144\151\x73\160\154\x61\x79\x5f\141\164\164\162"] : '';
        $Lj = $mx->getnestedattribute($SR, $Ui);
        $UU = $mx->getnestedattribute($SR, $aQ);
        $m3 = $mx->getnestedattribute($SR, $lC);
        $sF = $mx->getnestedattribute($SR, $Zp);
        $dM = $Lj;
        $this->config = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\x61\165\164\150\137\x63\x6c\x69\x65\156\x74\137\x63\157\x6e\146\151\x67");
        $this->config = !$this->config || empty($this->config) ? array() : $this->config->get_current_config();
        $Aj = isset($this->config["\x61\143\x74\151\166\141\x74\x65\x5f\x75\163\x65\162\137\x61\156\x61\x6c\171\x74\151\143\163"]) ? $this->config["\x61\143\x74\x69\166\141\164\145\137\165\x73\x65\162\x5f\x61\x6e\141\154\x79\164\151\143\163"] : 0;
        $current_user = wp_get_current_user();
        if (!($current_user->ID !== 0)) {
            goto PPm;
        }
        do_action("\155\x6f\137\157\141\165\x74\x68\137\144\151\163\143\157\x72\144\137\146\154\157\167\x5f\x68\141\156\144\x6c\145", $current_user, $pM, $SR);
        do_action("\155\157\x5f\x6f\141\165\164\150\137\x6c\157\x67\147\145\144\x5f\151\156\137\165\x73\145\162\137\164\141\147\137\165\160\x64\x61\x74\145", $current_user, $pM, $SR);
        $AE = get_option("\x6d\x6f\x5f\x64\x72\x6d\137\x73\x79\x6e\x63\x5f\162\145\x64\151\162\x65\143\164");
        if (!(isset($AE) && $AE)) {
            goto iiE;
        }
        wp_redirect($AE);
        exit;
        iiE:
        PPm:
        if (empty($xD)) {
            goto V96;
        }
        switch ($xD) {
            case "\x46\116\x41\x4d\105":
                $dM = $m3;
                goto rsx;
            case "\114\x4e\101\x4d\105":
                $dM = $sF;
                goto rsx;
            case "\125\123\105\x52\116\x41\115\105":
                $dM = $Lj;
                goto rsx;
            case "\106\116\101\x4d\105\x5f\x4c\x4e\101\x4d\105":
                $dM = $m3 . "\x20" . $sF;
                goto rsx;
            case "\x4c\x4e\101\x4d\105\x5f\106\x4e\x41\x4d\x45":
                $dM = $sF . "\x20" . $m3;
            default:
                goto rsx;
        }
        Hcy:
        rsx:
        V96:
        if (!empty($Lj)) {
            goto Byk;
        }
        MO_Oauth_Debug::mo_oauth_log("\125\163\x65\162\156\141\155\145\x20\x3a\40" . $Lj);
        $this->check_status(array("\x6d\163\x67" => "\125\x73\x65\162\156\141\155\145\x20\156\x6f\x74\x20\x72\x65\x63\x65\151\166\x65\144\56\x20\103\x68\x65\143\x6b\x20\171\x6f\x75\x72\40\x3c\x73\x74\162\x6f\156\147\x3e\101\x74\164\x72\151\142\165\x74\x65\40\x4d\141\160\x70\151\156\x67\74\x2f\x73\164\162\157\x6e\147\x3e\x20\143\157\x6e\x66\151\147\x75\162\x61\164\x69\x6f\156\56", "\x63\x6f\x64\145" => "\x55\x4e\x41\115\105", "\163\164\x61\164\165\x73" => false, "\141\160\x70\x6c\151\x63\x61\164\x69\x6f\156" => $vP, "\145\155\x61\151\x6c" => '', "\x75\x73\145\162\156\x61\155\145" => ''), $Aj);
        Byk:
        if (!(!empty($UU) && false === strpos($UU, "\x40"))) {
            goto z_e;
        }
        $this->check_status(array("\x6d\163\147" => "\115\141\160\160\145\144\40\105\x6d\x61\151\x6c\40\x61\164\164\x72\x69\x62\x75\x74\x65\40\144\157\145\163\40\156\157\x74\x20\x63\157\156\x74\141\151\x6e\40\166\x61\154\x69\x64\x20\x65\155\141\151\x6c\56", "\x63\157\144\145" => "\105\x4d\101\x49\x4c", "\163\164\141\164\x75\163" => false, "\x61\160\x70\154\151\x63\x61\164\151\x6f\156" => $vP, "\143\x6c\151\145\x6e\x74\137\151\160" => $mx->get_client_ip(), "\145\155\141\151\154" => $UU, "\x75\163\x65\162\156\141\155\x65" => $Lj), $Aj);
        z_e:
        if (!is_multisite()) {
            goto kRl;
        }
        $blog_id = $Nh->get_value("\142\154\157\x67\x5f\x69\144");
        switch_to_blog($blog_id);
        do_action("\x6d\157\x5f\157\x61\x75\164\150\x5f\143\x6c\151\x65\x6e\x74\x5f\143\157\156\143\157\x72\144\x5f\162\145\x73\164\162\151\x63\164\x5f\x6c\x6f\x67\x69\156", $xA, $SR, $blog_id);
        kRl:
        do_action("\x6d\157\137\x6f\141\165\164\x68\x5f\162\145\x73\x74\x72\x69\143\164\137\x65\155\x61\x69\x6c\x73", $UU, $this->config);
        if (!has_filter("\155\157\137\x6f\141\x75\x74\150\x5f\x6d\x6f\x64\151\x66\171\137\165\163\145\162\156\141\x6d\145\x5f\x61\164\x74\x72")) {
            goto lSh;
        }
        $Lj = apply_filters("\x6d\157\137\x6f\x61\165\x74\150\x5f\x6d\x6f\144\151\146\x79\137\x75\163\145\162\156\141\x6d\145\137\141\164\164\x72", $SR);
        lSh:
        $user = get_user_by("\x6c\x6f\147\x69\156", $Lj);
        $tD = isset($xA["\141\154\154\157\x77\137\144\165\x70\154\x69\143\x61\x74\145\x5f\145\x6d\x61\151\154\x73"]) ? true : false;
        if ($user) {
            goto nYd;
        }
        if (!(!$tD || $tD && !$xA["\141\x6c\x6c\x6f\x77\137\x64\165\160\x6c\151\143\x61\x74\145\137\145\155\141\x69\154\x73"])) {
            goto k4w;
        }
        $user = get_user_by("\145\155\141\151\x6c", $UU);
        k4w:
        nYd:
        $b2 = $user ? $user->ID : 0;
        $CJ = 0 === $b2;
        if (!has_filter("\155\x6f\x5f\x6f\141\x75\164\x68\x5f\x67\x65\164\137\x75\x73\145\162\137\142\x79\x5f\x65\x6d\141\151\x6c")) {
            goto mSo;
        }
        $user = apply_filters("\x6d\157\137\x6f\x61\165\164\150\137\147\145\164\x5f\x75\x73\145\162\137\x62\171\x5f\145\155\141\151\x6c", $Lj, $UU);
        mSo:
        if (!has_filter("\x6d\x6f\x5f\157\x61\x75\x74\x68\137\x63\150\x65\x63\153\137\x75\163\145\162\x5f\142\171\137\145\155\141\x69\x6c")) {
            goto i3y;
        }
        $CJ = apply_filters("\x6d\157\137\x6f\141\x75\164\150\x5f\x63\150\x65\x63\153\x5f\x75\x73\x65\x72\x5f\142\171\x5f\145\155\141\x69\154", $Lj, $UU);
        i3y:
        $b2 = $user ? $user->ID : 0;
        if (!(isset($xA["\141\x75\164\x6f\x63\162\145\x61\x74\145\165\x73\145\x72\163"]) && 1 !== intval($xA["\x61\165\164\x6f\143\x72\145\141\164\x65\x75\163\145\x72\163"]))) {
            goto cLF;
        }
        $blog_id = 1;
        $qo = apply_filters("\x6d\x6f\x5f\x6f\141\x75\164\150\x5f\143\x6c\151\x65\x6e\x74\x5f\144\x69\x73\x61\x62\154\145\x5f\x61\165\x74\157\137\x63\x72\145\x61\164\x65\x5f\x75\x73\145\x72\x73\137\146\157\162\137\163\160\x65\143\x69\146\x69\143\137\x69\x64\x70", $b2, $blog_id, $this->config, $xA);
        $this->config = $qo[0];
        $xA = $qo[1];
        cLF:
        if (!(!(isset($this->config["\x61\165\x74\x6f\x5f\x72\x65\x67\x69\x73\x74\x65\x72"]) && 1 === intval($this->config["\x61\165\x74\157\x5f\162\145\x67\151\163\x74\145\162"])) && $CJ)) {
            goto XhI;
        }
        $this->check_status(array("\x6d\x73\x67" => "\x52\145\x67\151\163\164\162\x61\164\x69\157\156\40\151\x73\40\x64\151\x73\x61\x62\154\x65\144\x20\x66\x6f\162\x20\164\x68\151\163\x20\x73\x69\164\145\56\40\x50\154\145\141\163\145\x20\x63\x6f\x6e\x74\141\x63\x74\x20\171\157\x75\x72\40\141\144\x6d\x69\x6e\x69\x73\x74\162\141\164\x6f\x72", "\x63\157\x64\x65" => "\122\105\x47\111\x53\124\x52\101\124\x49\117\x4e\137\104\111\x53\x41\102\x4c\105\x44", "\163\164\141\x74\165\x73" => false, "\141\x70\160\154\151\x63\x61\x74\151\x6f\156" => $vP, "\143\154\151\x65\156\164\x5f\x69\x70" => $mx->get_client_ip(), "\145\x6d\x61\151\154" => $UU, "\165\163\145\x72\x6e\x61\155\145" => $Lj), $Aj);
        XhI:
        if (!$CJ) {
            goto Xrj;
        }
        $ql = 10;
        $az = false;
        $jj = false;
        $n2 = apply_filters("\155\157\137\x6f\x61\x75\164\x68\x5f\x70\141\163\x73\x77\x6f\162\x64\137\160\x6f\154\x69\143\x79\137\x6d\141\156\141\x67\x65\162", $ql);
        if (!is_array($n2)) {
            goto t_I;
        }
        $ql = intval($n2["\160\141\163\x73\167\157\x72\144\x5f\x6c\x65\x6e\x67\x74\x68"]);
        $az = $n2["\163\160\x65\143\151\x61\154\x5f\143\150\141\162\141\x63\164\145\162\163"];
        $jj = $n2["\x65\170\x74\x72\x61\x5f\x73\x70\x65\143\151\x61\x6c\x5f\x63\150\x61\162\x61\x63\x74\145\162\x73"];
        t_I:
        $NO = wp_generate_password($ql, $az, $jj);
        $vZ = get_user_by("\145\155\x61\x69\x6c", $UU);
        if (!$vZ) {
            goto QFI;
        }
        add_filter("\160\162\x65\x5f\x75\163\145\162\x5f\x65\155\141\x69\x6c", array($this, "\x73\x6b\x69\160\x5f\x65\155\x61\x69\154\x5f\x65\x78\151\x73\164"), 30);
        QFI:
        $Lj = apply_filters("\155\x6f\137\x6f\x61\x75\x74\150\x5f\147\x65\x74\137\165\163\x65\162\156\141\155\x65\137\x77\x69\164\150\137\x70\x6f\163\x74\x66\151\170\x5f\x61\144\x64\145\144", $Lj, $UU);
        $b2 = wp_create_user($Lj, $NO, $UU);
        if (!is_wp_error($b2)) {
            goto BT2;
        }
        MO_Oauth_Debug::mo_oauth_log("\105\x72\162\157\162\40\x63\x72\x65\141\164\151\156\x67\40\127\120\40\x75\x73\145\162");
        goto cCB;
        BT2:
        MO_Oauth_Debug::mo_oauth_log("\116\145\167\x20\165\x73\x65\162\40\143\x72\145\x61\164\145\144\x20\x3d\x3e");
        MO_Oauth_Debug::mo_oauth_log("\125\163\x65\x72\40\x49\104\40\x3d\x3e\40" . $b2);
        cCB:
        $eJ = array("\x49\x44" => $b2, "\x75\x73\145\x72\x5f\145\155\141\151\154" => $UU, "\165\163\145\162\x5f\154\157\x67\151\156" => $Lj, "\165\163\x65\162\137\x6e\151\143\145\x6e\x61\155\x65" => $Lj);
        do_action("\x75\x73\145\162\x5f\162\x65\147\x69\163\x74\145\162", $b2, $eJ);
        Xrj:
        if (!($CJ || (!isset($this->config["\153\145\145\160\137\145\170\x69\163\x74\x69\x6e\x67\137\x75\x73\145\x72\x73"]) || 1 !== intval($this->config["\153\145\145\160\x5f\145\170\x69\x73\164\x69\156\147\137\165\163\x65\162\163"])))) {
            goto k6c;
        }
        if (!is_wp_error($b2)) {
            goto ZrZ;
        }
        if (!get_user_by("\x6c\157\147\151\156", $Lj)) {
            goto ewf;
        }
        $b2 = get_user_by("\x6c\157\147\151\156", $Lj)->ID;
        ewf:
        ZrZ:
        $ub = array("\111\104" => $b2, "\146\x69\x72\163\x74\x5f\156\141\x6d\145" => $m3, "\x6c\141\163\164\137\x6e\141\x6d\x65" => $sF, "\144\x69\163\160\x6c\x61\x79\x5f\x6e\141\x6d\x65" => $dM, "\x75\163\145\162\x5f\x6c\x6f\147\151\x6e" => $Lj, "\x75\x73\x65\x72\137\x6e\151\143\x65\x6e\x61\x6d\x65" => $Lj);
        if (isset($this->config["\x6b\x65\x65\x70\x5f\145\x78\x69\x73\164\151\156\147\137\x65\x6d\141\151\154\x5f\141\x74\x74\x72"]) && 1 === intval($this->config["\153\145\145\160\x5f\x65\x78\151\x73\x74\151\156\x67\x5f\145\x6d\x61\151\154\x5f\141\164\164\x72"])) {
            goto TvR;
        }
        $ub["\x75\x73\145\x72\x5f\145\x6d\141\x69\154"] = $UU;
        wp_update_user($ub);
        MO_Oauth_Debug::mo_oauth_log("\x41\164\164\162\151\142\x75\164\x65\40\x4d\x61\160\x70\x69\x6e\x67\40\x44\x6f\156\145");
        goto Wrw;
        TvR:
        wp_update_user($ub);
        MO_Oauth_Debug::mo_oauth_log("\x41\164\164\162\151\x62\165\164\x65\40\x4d\x61\x70\x70\151\x6e\x67\x20\104\157\156\x65");
        Wrw:
        if (!isset($SR["\x73\x75\x62"])) {
            goto aMj;
        }
        update_user_meta($b2, "\155\x6f\x5f\x62\x61\143\153\143\150\x61\x6e\x6e\145\154\x5f\141\x74\x74\162\x5f\x73\x75\142", $SR["\x73\x75\x62"]);
        aMj:
        if (!isset($SR["\x73\151\144"])) {
            goto PMb;
        }
        update_user_meta($b2, "\155\x6f\137\x62\x61\143\153\x63\x68\141\x6e\x6e\145\154\x5f\141\164\x74\x72\x5f\163\x69\x64", $SR["\163\151\x64"]);
        PMb:
        update_user_meta($b2, "\x6d\x6f\137\x6f\x61\165\164\150\137\x62\x75\x64\144\171\x70\162\x65\163\163\137\141\164\x74\162\151\x62\165\x74\x65\163", $SR);
        MO_Oauth_Debug::mo_oauth_log("\102\x75\144\x64\171\x50\162\145\x73\163\x20\141\164\164\x72\151\x62\165\164\145\163\40\165\160\144\141\x74\x65\144\40\163\165\143\x63\145\x73\163\x66\165\154\x6c\171");
        k6c:
        $user = get_user_by("\111\104", $b2);
        MO_Oauth_Debug::mo_oauth_log("\125\163\145\x72\40\x46\x6f\x75\156\144");
        MO_Oauth_Debug::mo_oauth_log("\125\x73\x65\x72\x20\x49\104\40\75\76\x20" . $b2);
        $Uk = $mx->is_multisite_plan();
        if (!is_multisite()) {
            goto TdG;
        }
        MO_Oauth_Debug::mo_oauth_log("\115\165\154\164\x69\x73\x69\x74\145\40\x50\x6c\141\156");
        $Fn = $mx->mo_oauth_client_get_option("\x6d\157\x5f\157\141\x75\x74\150\x5f\x63\x33\x56\151\x63\x32\x6c\60\x5a\130\116\x7a\132\x57\x78\x6c\x59\63\122\154\132\101");
        $JV = array();
        if (!isset($Fn)) {
            goto pA3;
        }
        $JV = json_decode($mx->mooauthdecrypt($Fn), true);
        pA3:
        $zw = false;
        if (!(is_array($JV) && in_array($blog_id, $JV))) {
            goto peP;
        }
        $zw = true;
        peP:
        $BE = intval($mx->mo_oauth_client_get_option("\156\157\117\x66\x53\165\142\123\x69\164\145\163"));
        $bt = get_sites(["\x6e\x75\155\x62\x65\x72" => 1000]);
        if (!(is_multisite() && $Uk && ($Uk && !$zw && $BE < 1000))) {
            goto A52;
        }
        $lM = "\131\157\x75\40\x68\141\166\145\x20\x6e\x6f\x74\40\165\160\147\x72\141\144\x65\144\x20\164\157\40\164\150\x65\40\x63\157\x72\x72\x65\143\x74\x20\x6c\151\143\x65\x6e\x73\x65\40\x70\154\x61\x6e\56\x20\x45\x69\x74\150\145\x72\x20\171\x6f\165\x20\150\141\x76\145\40\x70\x75\x72\143\x68\x61\x73\145\x64\x20\x66\157\162\40\151\x6e\x63\157\x72\162\x65\x63\164\40\x6e\x6f\x2e\x20\157\x66\40\x73\x69\x74\145\x73\x20\x6f\162\x20\171\157\165\40\150\141\166\x65\40\143\162\x65\x61\164\x65\x64\40\x61\x20\x6e\x65\x77\40\163\165\142\163\x69\x74\x65\x2e\40\x43\157\x6e\x74\x61\143\x74\40\x74\x6f\40\x79\x6f\165\162\40\141\144\155\151\x6e\151\x73\x74\162\x61\164\x6f\x72\x20\x74\157\40\165\160\147\x72\141\144\x65\40\x79\x6f\165\x72\x20\x73\165\142\163\x69\x74\x65\56";
        MO_Oauth_Debug::mo_oauth_log($lM);
        $mx->handle_error($lM);
        wp_die($lM);
        A52:
        TdG:
        if ($user) {
            goto piJ;
        }
        return;
        piJ:
        $hF = '';
        if (isset($this->config["\x61\146\164\x65\162\137\154\157\147\151\156\x5f\165\162\x6c"]) && '' !== $this->config["\x61\146\164\145\x72\x5f\154\x6f\x67\151\x6e\x5f\x75\162\x6c"]) {
            goto wLJ;
        }
        $pG = $Nh->get_value("\162\145\144\151\x72\x65\143\x74\137\165\162\151");
        $AM = parse_url($pG);
        if (!(isset($AM["\x70\141\164\150"]) && strpos($AM["\160\141\x74\x68"], "\167\x70\55\x6c\157\x67\151\x6e\x2e\160\150\160") !== false)) {
            goto kmr;
        }
        $pG = site_url();
        kmr:
        if (!isset($AM["\x71\165\x65\162\x79"])) {
            goto Dz5;
        }
        parse_str($AM["\161\x75\145\162\x79"], $TL);
        if (!isset($TL["\162\x65\144\x69\162\x65\143\164\137\x74\157"])) {
            goto fzZ;
        }
        $pG = $TL["\x72\145\x64\151\162\x65\x63\164\x5f\x74\x6f"];
        fzZ:
        Dz5:
        $hF = rawurldecode($pG && '' !== $pG ? $pG : site_url());
        $Zz = get_option("\165\163\x69\156\147\x5f\x63\165\163\x74\157\x6d\137\154\x6f\147\151\156\x5f\x62\164\156");
        if (!$Zz) {
            goto Ixn;
        }
        $hF = apply_filters("\155\157\x5f\x6f\x61\165\164\x68\137\x64\x69\x73\x5f\165\x70\x64\141\x74\145\137\141\x63\164\165\x61\154\x5f\x6c\151\156\153", $hF, $Lj);
        Ixn:
        goto q_G;
        wLJ:
        $hF = $this->config["\x61\146\164\145\x72\x5f\154\x6f\147\151\156\137\165\x72\154"];
        q_G:
        if (!($mx->get_versi() === 1)) {
            goto T7p;
        }
        if (isset($xA["\x65\156\x61\142\x6c\x65\137\x72\157\x6c\x65\137\x6d\x61\x70\x70\x69\156\x67"])) {
            goto spG;
        }
        $xA["\145\x6e\x61\142\x6c\145\137\162\x6f\x6c\145\137\155\141\x70\x70\x69\156\x67"] = true;
        if (!(isset($xA["\x63\154\x69\x65\156\164\x5f\x63\x72\x65\144\x73\137\x65\x6e\x63\162\160\171\164\145\144"]) && boolval($xA["\x63\x6c\x69\x65\x6e\164\137\143\x72\145\144\163\x5f\145\x6e\x63\162\160\x79\164\145\x64"]))) {
            goto j3I;
        }
        $xA["\143\x6c\x69\x65\x6e\164\x5f\151\144"] = $mx->mooauthencrypt($xA["\143\x6c\x69\x65\156\164\x5f\151\x64"]);
        $xA["\x63\154\x69\x65\x6e\x74\137\163\145\143\162\145\x74"] = $mx->mooauthencrypt($xA["\x63\154\x69\145\x6e\164\137\163\145\x63\x72\x65\164"]);
        j3I:
        $mx->set_app_by_name($x1["\141\x70\160\x5f\x6e\x61\x6d\x65"], $xA);
        spG:
        if (!(!user_can($b2, "\141\x64\155\x69\x6e\151\163\164\x72\141\164\x6f\162") && $CJ || !isset($xA["\x6b\x65\145\x70\x5f\x65\x78\x69\163\164\151\x6e\x67\137\165\x73\145\162\x5f\162\157\x6c\145\163"]) || 1 !== intval($xA["\x6b\145\x65\160\137\x65\170\x69\163\x74\x69\x6e\147\137\165\163\x65\162\137\162\x6f\x6c\145\163"]))) {
            goto epm;
        }
        $this->mo_oauth_client_map_default_role($b2, $xA);
        MO_Oauth_Debug::mo_oauth_log("\122\x6f\154\x65\x20\x4d\141\x70\160\151\156\x67\40\104\157\x6e\x65");
        epm:
        T7p:
        do_action("\155\x6f\x5f\157\141\x75\164\x68\137\x63\x6c\x69\x65\156\164\137\155\141\160\137\x72\x6f\154\145\163", array("\165\163\145\x72\x5f\x69\x64" => $b2, "\141\x70\160\x5f\x63\157\x6e\x66\x69\147" => $xA, "\156\x65\x77\137\165\163\x65\x72" => $CJ, "\162\x65\x73\157\165\162\x63\145\x5f\157\x77\x6e\x65\162" => $SR, "\141\x70\x70\137\x6e\x61\155\145" => $vP, "\143\157\156\x66\x69\x67" => $this->config));
        MO_Oauth_Debug::mo_oauth_log("\x52\x6f\154\x65\40\115\x61\160\160\151\x6e\x67\40\x44\157\x6e\145");
        do_action("\155\157\137\x6f\141\x75\x74\x68\137\154\157\x67\147\145\144\137\x69\x6e\x5f\x75\x73\x65\162\137\x74\157\153\x65\x6e", $user, $pM);
        do_action("\155\x6f\x5f\x6f\x61\165\164\150\x5f\141\x64\x64\137\144\151\x73\x5f\x75\163\145\162\137\163\x65\x72\x76\x65\162", $b2, $pM, $SR);
        $this->check_status(array("\x6d\x73\x67" => "\114\x6f\x67\x69\x6e\x20\123\165\x63\143\x65\163\163\146\x75\x6c\x21", "\x63\x6f\x64\x65" => "\114\x4f\107\x49\x4e\137\123\x55\103\x43\105\123\123", "\163\x74\141\164\x75\163" => true, "\x61\160\x70\154\x69\x63\141\x74\x69\157\x6e" => $vP, "\143\x6c\151\x65\156\164\137\151\160" => $mx->get_client_ip(), "\156\x61\x76\151\147\x61\x74\x69\157\156\x75\162\154" => $hF, "\x65\x6d\141\x69\x6c" => $UU, "\165\163\x65\162\156\141\155\x65" => $Lj), $Aj);
        if (!$OR) {
            goto pbD;
        }
        return $user;
        pbD:
        do_action("\155\x6f\x5f\x6f\141\x75\x74\x68\x5f\163\x65\x74\x5f\x6c\157\147\x69\x6e\137\x63\157\x6f\153\x69\x65");
        do_action("\155\157\x5f\157\141\165\x74\150\x5f\x67\x65\x74\x5f\x75\x73\x65\x72\x5f\x61\164\164\162\x73", $user, $SR);
        update_user_meta($user->ID, "\155\157\x5f\x6f\x61\x75\x74\x68\137\143\154\x69\145\156\x74\137\x6c\141\163\164\137\x69\144\x5f\164\x6f\x6b\145\156", isset($pM["\x69\x64\x5f\164\157\x6b\x65\156"]) ? $pM["\x69\x64\x5f\x74\x6f\x6b\145\x6e"] : $pM["\141\143\143\145\163\163\x5f\164\157\153\x65\156"]);
        wp_set_current_user($user->ID);
        $fr = false;
        $fr = apply_filters("\155\x6f\137\162\x65\x6d\145\155\x62\x65\x72\137\155\145", $fr);
        wp_set_auth_cookie($user->ID, $fr);
        if (!isset($SR["\x72\157\154\x65\163"])) {
            goto A9g;
        }
        apply_filters("\x6d\157\137\x6f\x61\x75\164\x68\x5f\165\x70\144\x61\164\x65\x5f\142\x62\x70\x5f\162\x6f\x6c\145", $user->ID, $SR["\162\157\154\145\163"]);
        A9g:
        if (!has_action("\x6d\x6f\137\x68\x61\143\153\137\154\157\x67\151\156\x5f\x73\145\x73\163\x69\x6f\x6e\x5f\162\x65\x64\x69\x72\x65\x63\164")) {
            goto itz;
        }
        $sm = $mx->gen_rand_str();
        $oa = $mx->gen_rand_str();
        $n2 = array("\165\163\145\x72\137\151\144" => $user->ID, "\x75\x73\145\162\137\x70\141\163\x73\x77\157\162\144" => $oa);
        set_transient($sm, $n2);
        do_action("\155\x6f\x5f\x68\x61\x63\153\137\154\157\147\x69\156\137\x73\x65\163\163\x69\x6f\x6e\137\162\145\x64\x69\162\x65\x63\x74", $user, $oa, $sm, $pG);
        itz:
        do_action("\167\160\137\x6c\x6f\x67\x69\x6e", $user->user_login, $user);
        setcookie("\155\x6f\137\x6f\141\x75\x74\x68\x5f\x6c\157\147\x69\156\137\x61\x70\x70\x5f\163\145\163\163\x69\157\x6e", $vP, null, "\57", null, true, true);
        do_action("\155\157\137\157\141\x75\164\150\137\x67\145\164\137\164\157\x6b\x65\156\137\146\x6f\162\137\150\145\141\144\x6c\x65\x73\163", $user, $pM, $hF);
        do_action("\x6d\157\x5f\157\x61\165\164\x68\137\x67\x65\164\x5f\x63\x75\x72\162\145\x6e\164\137\141\x70\x70\156\x61\x6d\x65", $vP);
        $jW = $Nh->get_value("\162\145\163\164\162\x69\x63\x74\x72\145\144\151\x72\145\x63\x74") !== false;
        $n3 = $Nh->get_value("\x70\x6f\x70\165\x70") === "\151\x67\156\x6f\162\145";
        if (isset($this->config["\x70\157\160\165\x70\x5f\154\x6f\147\x69\156"]) && 1 === intval($this->config["\160\x6f\160\165\160\x5f\x6c\x6f\x67\151\x6e"]) && !$n3 && !boolval($jW)) {
            goto FUG;
        }
        do_action("\x6d\x6f\137\157\x61\165\x74\150\x5f\x72\145\x64\x69\x72\x65\x63\164\x5f\157\x61\165\x74\150\137\165\163\x65\162\x73", $user, $hF);
        header("\114\x6f\x63\x61\x74\x69\x6f\x6e\x3a\40" . $hF);
        goto UyY;
        FUG:
        echo "\74\x73\143\162\151\x70\x74\x3e\x77\151\x6e\144\x6f\x77\x2e\x6f\x70\145\156\145\162\56\x48\141\156\144\154\x65\120\157\160\x75\160\x52\x65\163\165\154\164\50\42" . $hF . "\42\x29\x3b\167\151\156\x64\157\x77\x2e\x63\154\157\163\145\50\51\x3b\74\x2f\x73\143\162\x69\160\x74\x3e";
        UyY:
        exit;
    }
    public function check_status($x1, $Aj)
    {
        global $mx;
        if (isset($x1["\163\164\x61\164\165\x73"])) {
            goto MzH;
        }
        MO_Oauth_Debug::mo_oauth_log("\x53\x6f\x6d\145\164\150\151\x6e\147\x20\x77\x65\156\x74\x20\167\x72\157\x6e\147\56\40\120\154\145\141\x73\145\40\164\162\171\40\x4c\157\x67\x67\151\156\x67\x20\151\156\x20\x61\147\x61\x69\x6e\x2e");
        $mx->handle_error("\123\x6f\x6d\145\x74\150\x69\x6e\x67\40\x77\145\156\164\x20\x77\162\157\x6e\x67\x2e\x20\120\154\145\x61\x73\x65\x20\x74\162\171\x20\x4c\x6f\147\147\151\156\x67\x20\151\156\x20\141\x67\x61\151\x6e\56");
        wp_die(wp_kses("\x53\157\x6d\x65\164\150\x69\156\x67\40\x77\x65\156\x74\x20\x77\x72\x6f\156\147\x2e\40\x50\154\x65\141\163\x65\40\164\162\171\40\x4c\157\147\147\151\x6e\147\x20\x69\x6e\x20\x61\x67\141\x69\156\56", \mo_oauth_get_valid_html()));
        MzH:
        if (!(isset($x1["\x73\164\x61\x74\165\163"]) && true === $x1["\x73\164\x61\x74\x75\163"] && (isset($x1["\x63\x6f\x64\145"]) && "\x4c\x4f\x47\111\x4e\x5f\x53\125\103\x43\105\123\123" === $x1["\143\x6f\x64\x65"]))) {
            goto VBh;
        }
        return true;
        VBh:
        if (!(true !== $x1["\x73\164\x61\x74\x75\163"])) {
            goto yRz;
        }
        $l5 = isset($x1["\155\163\x67"]) && !empty($x1["\x6d\163\x67"]) ? $x1["\x6d\163\147"] : "\123\x6f\x6d\145\x74\x68\x69\156\147\x20\167\145\x6e\164\x20\167\162\157\156\147\56\40\x50\154\145\x61\163\145\40\x74\x72\x79\x20\x4c\x6f\147\x67\151\156\x67\40\151\x6e\40\x61\147\x61\x69\x6e\x2e";
        MO_Oauth_Debug::mo_oauth_log($l5);
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        yRz:
    }
    public function skip_email_exist($Mf)
    {
        define("\x57\120\137\x49\x4d\x50\x4f\122\x54\x49\x4e\x47", "\x53\113\x49\120\137\x45\x4d\x41\x49\114\137\x45\130\x49\x53\x54");
        return $Mf;
    }
}
