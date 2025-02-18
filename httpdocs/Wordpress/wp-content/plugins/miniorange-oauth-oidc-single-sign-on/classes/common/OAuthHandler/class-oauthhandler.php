<?php


namespace MoOauthClient;

use MoOauthClient\OauthHandlerInterface;
class MO_Oauth_Debug
{
    public static function mo_oauth_log($HG)
    {
        global $mx;
        $Dt = plugin_dir_path(__FILE__) . $mx->mo_oauth_client_get_option("\x6d\x6f\137\157\141\165\x74\x68\x5f\144\x65\x62\x75\x67") . "\56\x6c\157\x67";
        $I2 = time();
        $U_ = "\133" . date("\131\55\x6d\55\x64\x20\x48\72\151\72\163", $I2) . "\40\125\124\103\135\40\72\x20" . print_r($HG, true) . PHP_EOL;
        if (!$mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x64\145\142\x75\x67\137\145\x6e\141\x62\154\145")) {
            goto Nd;
        }
        if ($mx->mo_oauth_client_get_option("\155\157\x5f\144\145\142\165\x67\x5f\x63\x68\x65\x63\x6b")) {
            goto JZ;
        }
        error_log($U_, 3, $Dt);
        goto OM;
        JZ:
        $HG = "\x54\150\151\x73\x20\151\x73\40\155\x69\x6e\151\x4f\162\141\x6e\x67\x65\x20\117\x41\x75\x74\x68\x20\x70\154\x75\147\x69\156\40\x44\x65\142\x75\147\40\x4c\x6f\x67\40\x66\x69\x6c\x65" . PHP_EOL;
        error_log($HG, 3, $Dt);
        OM:
        Nd:
    }
}
class OauthHandler implements OauthHandlerInterface
{
    public function get_token($ZO, $x1, $Ao = true, $Qq = false)
    {
        MO_Oauth_Debug::mo_oauth_log("\124\157\x6b\145\156\40\x72\145\x71\165\x65\163\164\40\x63\157\x6e\x74\x65\x6e\164\40\x3d\76\x20");
        global $mx;
        $lM = new \WP_Error();
        $OR = isset($x1["\151\163\137\167\x70\137\x6c\157\x67\x69\x6e"]) ? $x1["\x69\x73\x5f\167\160\x5f\x6c\157\147\x69\x6e"] : false;
        unset($x1["\x69\163\x5f\167\160\137\x6c\x6f\x67\151\156"]);
        foreach ($x1 as $NZ => $mB) {
            $x1[$NZ] = html_entity_decode($mB);
            tX:
        }
        il:
        $xR = '';
        if (!isset($x1["\143\x6c\151\145\156\x74\137\x73\x65\x63\x72\145\x74"])) {
            goto QJ;
        }
        $xR = $x1["\143\154\151\145\x6e\x74\x5f\163\145\x63\x72\x65\164"];
        QJ:
        $P_ = array("\101\143\143\145\160\x74" => "\141\x70\x70\154\151\x63\141\x74\x69\157\x6e\x2f\x6a\x73\157\x6e", "\x63\150\141\162\x73\x65\x74" => "\125\124\x46\x20\x2d\40\x38", "\103\x6f\156\164\145\156\x74\x2d\x54\x79\160\145" => "\x61\x70\160\154\151\143\141\x74\x69\157\x6e\x2f\170\x2d\167\x77\167\x2d\x66\x6f\x72\155\x2d\165\162\x6c\145\x6e\x63\x6f\x64\145\144", "\101\x75\x74\x68\x6f\162\x69\x7a\141\164\x69\x6f\156" => "\x42\141\x73\151\x63\x20" . base64_encode($x1["\143\x6c\x69\x65\x6e\164\137\151\144"] . "\72" . $xR));
        $P_ = apply_filters("\155\157\x5f\x6f\141\x75\x74\150\x5f\x63\x6f\x75\x73\164\x6f\155\137\145\x78\164\x65\156\144\x5f\164\157\153\145\x6e\145\156\144\160\157\x69\156\x74\137\160\x61\162\x61\155\x73", $P_);
        if (!(isset($x1["\143\157\144\145\x5f\166\145\162\151\x66\151\145\162"]) && !isset($x1["\143\154\x69\145\x6e\164\x5f\163\145\143\x72\x65\x74"]))) {
            goto av;
        }
        unset($P_["\101\x75\x74\x68\x6f\x72\x69\x7a\x61\164\151\x6f\x6e"]);
        av:
        if (1 == $Ao && 0 == $Qq) {
            goto tU;
        }
        if (0 == $Ao && 1 == $Qq) {
            goto Q0;
        }
        goto Jt;
        tU:
        unset($x1["\143\154\x69\x65\156\x74\x5f\151\144"]);
        if (!isset($x1["\143\x6c\x69\x65\156\164\137\163\x65\x63\162\145\164"])) {
            goto Eo;
        }
        unset($x1["\x63\x6c\151\x65\156\164\x5f\163\x65\x63\162\145\x74"]);
        Eo:
        goto Jt;
        Q0:
        if (!isset($P_["\101\x75\x74\x68\x6f\x72\x69\x7a\x61\164\x69\157\156"])) {
            goto Xd;
        }
        unset($P_["\101\165\x74\x68\157\162\151\172\141\164\x69\x6f\156"]);
        Xd:
        Jt:
        MO_Oauth_Debug::mo_oauth_log("\x54\x6f\153\x65\x6e\x20\x65\156\144\x70\x6f\x69\156\x74\x20\125\122\114\40\x3d\x3e\40" . $ZO);
        $x1 = apply_filters("\x6d\x6f\137\157\141\x75\164\x68\137\160\x6f\x6c\141\162\x5f\142\157\144\x79\x5f\x61\162\147\165\155\x65\156\x74\163", $x1);
        MO_Oauth_Debug::mo_oauth_log("\x62\157\x64\171\x20\x3d\76");
        MO_Oauth_Debug::mo_oauth_log($x1);
        MO_Oauth_Debug::mo_oauth_log("\x68\145\x61\144\x65\162\163\40\75\76");
        MO_Oauth_Debug::mo_oauth_log($P_);
        $zF = wp_remote_post($ZO, array("\x6d\145\164\x68\x6f\x64" => "\x50\x4f\123\124", "\x74\x69\x6d\x65\x6f\x75\164" => 45, "\162\x65\x64\151\x72\145\143\164\151\157\x6e" => 5, "\x68\164\x74\160\166\145\x72\163\151\157\156" => "\x31\56\60", "\142\x6c\x6f\x63\153\x69\156\x67" => true, "\150\145\141\x64\145\162\x73" => $P_, "\142\x6f\144\171" => $x1, "\143\x6f\157\x6b\x69\145\163" => array(), "\163\x73\154\x76\145\162\151\x66\171" => false));
        if (!is_wp_error($zF)) {
            goto hS;
        }
        $mx->handle_error($zF->get_error_message());
        MO_Oauth_Debug::mo_oauth_log("\x45\162\162\157\x72\40\x66\x72\x6f\x6d\x20\x54\157\x6b\x65\x6e\40\x45\156\x64\x70\157\151\156\164\x3a\40" . $zF->get_error_message());
        wp_die(wp_kses($zF->get_error_message(), \mo_oauth_get_valid_html()));
        exit;
        hS:
        $zF = $zF["\x62\x6f\x64\x79"];
        if (is_array(json_decode($zF, true))) {
            goto m3;
        }
        $mx->handle_error("\111\156\166\x61\154\151\x64\x20\162\145\x73\160\157\156\163\145\x20\x72\x65\x63\x65\x69\166\145\x64\x20\x3a\x20" . $zF);
        echo "\x3c\x73\164\162\x6f\156\x67\x3e\x52\145\163\160\157\156\x73\145\x20\x3a\x20\x3c\57\x73\164\x72\x6f\156\x67\x3e\x3c\x62\162\x3e";
        print_r($zF);
        echo "\74\142\162\x3e\x3c\142\162\76";
        MO_Oauth_Debug::mo_oauth_log("\105\162\162\x6f\x72\x20\146\162\157\155\x20\x54\x6f\x6b\145\x6e\x20\x45\156\144\160\157\x69\x6e\164\75\76\40\111\x6e\166\x61\x6c\151\144\x20\122\145\163\x70\x6f\x6e\163\x65\40\162\x65\143\x65\151\166\145\x64\56" . $zF);
        exit("\x49\156\x76\141\154\151\x64\x20\162\x65\x73\160\157\156\163\145\40\162\x65\x63\x65\151\x76\x65\x64\56");
        m3:
        $Bn = json_decode($zF, true);
        if (isset($Bn["\145\162\x72\157\162\x5f\x64\145\163\143\x72\x69\160\x74\151\x6f\156"])) {
            goto Hs;
        }
        if (isset($Bn["\145\x72\162\157\x72"])) {
            goto Q5;
        }
        goto dH;
        Hs:
        do_action("\155\157\137\162\145\144\x69\x72\x65\143\x74\x5f\x74\x6f\x5f\143\x75\163\164\x6f\x6d\137\145\162\x72\157\x72\x5f\160\141\x67\145");
        if (!($x1["\147\162\141\156\x74\137\164\171\160\145"] == "\160\x61\163\163\x77\157\x72\x64" && $OR)) {
            goto yk;
        }
        $lM->add("\155\x6f\x5f\157\141\165\164\150\137\151\x64\x70\x5f\x65\x72\x72\157\162", __("\x3c\x73\x74\x72\157\156\147\x3e\x45\122\x52\x4f\x52\x3c\57\163\164\x72\x6f\156\147\76\72\40" . $Bn["\x65\162\162\157\x72\x5f\x64\x65\x73\143\162\x69\160\x74\x69\x6f\156"]));
        return $lM;
        yk:
        $mx->handle_error($Bn["\145\162\162\157\x72\x5f\144\145\x73\143\x72\151\x70\164\x69\157\156"]);
        $this->handle_error(json_encode($Bn["\x65\162\162\x6f\x72\137\x64\x65\x73\x63\162\x69\160\164\x69\157\156"]), $x1);
        return;
        goto dH;
        Q5:
        do_action("\x6d\157\x5f\162\145\144\x69\x72\145\143\x74\137\164\157\137\143\165\163\164\x6f\x6d\137\145\162\162\157\x72\x5f\x70\141\147\x65");
        if (!($x1["\x67\x72\x61\x6e\164\x5f\x74\x79\160\x65"] == "\160\x61\163\163\x77\x6f\x72\144" && $OR)) {
            goto UZ;
        }
        $lM->add("\x6d\157\x5f\157\141\165\164\x68\137\x69\144\160\x5f\x65\162\x72\x6f\162", __("\x3c\163\164\162\x6f\x6e\x67\x3e\105\x52\122\x4f\122\74\x2f\x73\164\x72\x6f\156\147\x3e\x3a\40" . $Bn["\x65\x72\162\157\162"]));
        return $lM;
        UZ:
        $mx->handle_error($Bn["\x65\x72\162\x6f\x72"]);
        $this->handle_error(json_encode($Bn["\x65\x72\x72\157\x72"]), $x1);
        return;
        dH:
        return $zF;
    }
    public function get_atoken($ZO, $x1, $It, $Ao = true, $Qq = false)
    {
        global $mx;
        foreach ($x1 as $NZ => $mB) {
            $x1[$NZ] = html_entity_decode($mB);
            mj:
        }
        C6:
        $xR = '';
        if (!isset($x1["\x63\x6c\x69\145\156\x74\137\163\145\143\162\x65\164"])) {
            goto qO;
        }
        $xR = $x1["\143\x6c\x69\145\156\x74\137\x73\x65\x63\162\x65\x74"];
        qO:
        $Sk = $x1["\143\154\x69\x65\x6e\164\137\x69\x64"];
        $P_ = array("\x41\143\143\145\160\x74" => "\141\x70\x70\x6c\151\x63\141\x74\151\x6f\156\x2f\x6a\163\157\156", "\x63\x68\x61\162\x73\145\x74" => "\125\x54\106\40\55\40\x38", "\103\x6f\x6e\164\145\156\x74\x2d\x54\171\160\x65" => "\x61\160\x70\x6c\151\143\x61\164\151\x6f\156\57\170\x2d\x77\x77\x77\55\x66\157\x72\155\55\x75\162\x6c\145\x6e\143\157\144\x65\144", "\101\165\x74\x68\x6f\162\x69\172\x61\x74\x69\x6f\156" => "\102\141\x73\x69\143\40" . base64_encode($Sk . "\x3a" . $xR));
        $P_ = apply_filters("\x6d\x6f\x5f\157\141\165\164\150\137\x63\x6f\165\x73\164\157\x6d\x5f\145\170\164\145\156\x64\x5f\164\x6f\x6b\145\x6e\145\x6e\144\x70\x6f\x69\156\x74\137\x70\x61\162\141\155\163", $P_);
        if (!isset($x1["\x63\x6f\x64\x65\137\x76\x65\162\151\x66\x69\x65\162"])) {
            goto Fz;
        }
        unset($P_["\x41\x75\x74\x68\157\x72\x69\x7a\x61\x74\x69\x6f\x6e"]);
        Fz:
        if (1 === $Ao && 0 === $Qq) {
            goto yz;
        }
        if (0 === $Ao && 1 === $Qq) {
            goto p2;
        }
        goto ny;
        yz:
        unset($x1["\143\154\151\145\156\164\137\151\144"]);
        if (!isset($x1["\x63\x6c\x69\x65\156\164\x5f\163\x65\x63\x72\x65\164"])) {
            goto Kt;
        }
        unset($x1["\143\x6c\x69\x65\156\x74\x5f\x73\145\143\x72\145\164"]);
        Kt:
        goto ny;
        p2:
        if (!isset($P_["\101\x75\164\x68\157\x72\151\x7a\141\x74\x69\157\156"])) {
            goto G3;
        }
        unset($P_["\101\x75\164\150\x6f\x72\151\x7a\141\164\151\x6f\x6e"]);
        G3:
        ny:
        $oK = curl_init($ZO);
        curl_setopt($oK, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($oK, CURLOPT_ENCODING, '');
        curl_setopt($oK, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($oK, CURLOPT_AUTOREFERER, true);
        curl_setopt($oK, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oK, CURLOPT_MAXREDIRS, 10);
        curl_setopt($oK, CURLOPT_POST, true);
        curl_setopt($oK, CURLOPT_HTTPHEADER, array("\101\x75\x74\150\157\162\151\172\x61\164\x69\x6f\x6e\x3a\40\102\x61\163\151\143\40" . base64_encode($Sk . "\x3a" . $xR), "\101\x63\143\x65\160\164\x3a\x20\x61\160\160\x6c\151\143\141\x74\151\x6f\x6e\57\152\163\x6f\x6e"));
        curl_setopt($oK, CURLOPT_POSTFIELDS, "\x72\145\144\x69\162\x65\143\164\x5f\x75\x72\x69\x3d" . $x1["\x72\x65\144\x69\162\x65\143\x74\137\165\x72\151"] . "\x26\x67\x72\x61\x6e\164\x5f\x74\x79\x70\x65\75" . "\141\x75\x74\150\x6f\x72\151\172\141\x74\151\157\x6e\137\x63\157\x64\145" . "\46\x63\x6c\x69\145\156\x74\137\151\144\x3d" . $Sk . "\46\x63\x6c\x69\145\156\164\137\163\145\x63\162\x65\164\x3d" . $xR . "\x26\x63\157\144\145\x3d" . $It);
        $Bn = curl_exec($oK);
        if (!curl_error($oK)) {
            goto g4;
        }
        echo "\x3c\x62\x3e\x52\145\x73\160\157\x6e\x73\145\x20\72\40\x3c\x2f\142\76\x3c\x62\x72\x3e";
        print_r($Bn);
        echo "\x3c\142\x72\x3e\74\x62\x72\x3e";
        MO_Oauth_Debug::mo_oauth_log(curl_error($oK));
        exit(curl_error($oK));
        g4:
        if (isset($Bn["\145\x72\162\157\162\137\x64\x65\x73\143\162\151\160\164\x69\x6f\x6e"])) {
            goto Ex;
        }
        if (isset($Bn["\145\162\x72\x6f\162"])) {
            goto E0;
        }
        if (!isset($Bn["\x61\143\143\x65\x73\163\137\164\157\x6b\145\156"])) {
            goto vL;
        }
        $j6 = $Bn["\141\143\143\x65\163\163\137\164\x6f\153\145\x6e"];
        vL:
        goto SK;
        E0:
        $sa = "\105\x72\162\x6f\162\x20\146\162\x6f\x6d\x20\124\157\153\x65\156\x20\105\x6e\x64\160\x6f\151\156\164\72\40" . $Bn["\145\162\162\157\x72"];
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\x6d\157\x5f\162\145\144\151\162\x65\x63\x74\x5f\x74\157\x5f\x63\165\163\x74\x6f\155\137\145\x72\162\x6f\162\x5f\160\x61\147\x65");
        exit($Bn["\x65\x72\x72\x6f\162\x5f\144\x65\163\x63\162\151\160\x74\151\x6f\x6e"]);
        SK:
        goto cr;
        Ex:
        $sa = "\x45\x72\x72\x6f\162\x20\x66\x72\x6f\155\x20\x54\x6f\x6b\x65\x6e\40\105\156\144\160\157\151\x6e\x74\72\40" . $Bn["\145\x72\x72\x6f\x72\137\144\x65\163\143\162\x69\160\164\151\x6f\x6e"];
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\x6d\x6f\x5f\x72\145\x64\151\x72\145\143\164\x5f\164\157\x5f\143\x75\163\x74\157\x6d\x5f\145\x72\x72\157\x72\x5f\160\x61\147\x65");
        exit($Bn["\145\162\162\157\x72\137\144\x65\x73\143\x72\151\x70\164\x69\157\156"]);
        cr:
        return $Bn;
    }
    public function get_access_token($ZO, $x1, $Ao, $Qq)
    {
        global $mx;
        $zF = $this->get_token($ZO, $x1, $Ao, $Qq);
        if (!is_wp_error($zF)) {
            goto BU;
        }
        return $zF;
        BU:
        $Bn = json_decode($zF, true);
        if (!("\160\x61\x73\163\167\157\x72\x64" === $x1["\147\x72\141\x6e\x74\x5f\164\x79\x70\145"])) {
            goto ax;
        }
        return $Bn;
        ax:
        if (isset($Bn["\141\x63\143\145\x73\163\x5f\x74\157\153\145\156"])) {
            goto qX;
        }
        $lM = "\x49\x6e\166\141\154\151\x64\40\162\x65\x73\x70\157\156\163\x65\x20\162\145\x63\x65\x69\166\x65\x64\x20\146\162\157\x6d\x20\117\101\165\164\150\x20\x50\x72\157\166\x69\144\x65\162\x2e\40\103\x6f\156\164\141\143\x74\x20\x79\x6f\165\x72\40\x61\x64\x6d\151\156\x69\163\x74\x72\x61\x74\x6f\x72\40\146\x6f\x72\x20\155\157\x72\145\x20\x64\145\164\141\x69\x6c\x73\56\x3c\142\162\x3e\74\142\x72\76\74\x73\164\162\x6f\x6e\x67\76\122\x65\163\160\x6f\x6e\163\x65\x20\72\x20\x3c\57\163\x74\162\157\x6e\x67\x3e\x3c\x62\x72\76" . $zF;
        $mx->handle_error($lM);
        MO_Oauth_Debug::mo_oauth_log("\105\x72\x72\x6f\162\40\167\x68\151\154\145\40\146\x65\164\143\x68\x69\156\147\40\x74\157\x6b\145\156\x3a\40" . $lM);
        echo $lM;
        exit;
        goto cv;
        qX:
        return $Bn["\141\143\x63\x65\x73\x73\x5f\x74\157\x6b\145\156"];
        cv:
    }
    public function get_id_token($ZO, $x1, $Ao, $Qq)
    {
        global $mx;
        $zF = $this->get_token($ZO, $x1, $Ao, $Qq);
        $Bn = json_decode($zF, true);
        if (isset($Bn["\x69\144\x5f\164\157\x6b\x65\156"])) {
            goto gd;
        }
        $lM = "\x49\x6e\166\141\154\x69\144\x20\x72\x65\163\160\157\x6e\163\145\x20\162\145\143\145\x69\166\145\x64\x20\x66\162\157\x6d\40\x4f\x70\x65\156\x49\x64\x20\x50\x72\x6f\x76\151\144\x65\x72\56\x20\x43\157\x6e\164\141\x63\164\x20\x79\157\x75\162\x20\x61\144\155\x69\156\x69\163\164\x72\x61\164\x6f\x72\40\146\x6f\x72\40\x6d\x6f\x72\145\x20\x64\145\x74\x61\151\154\x73\56\x3c\142\162\x3e\x3c\142\x72\76\74\x73\164\x72\x6f\156\x67\76\122\x65\x73\x70\157\156\x73\145\40\72\x20\x3c\57\163\x74\x72\157\x6e\147\x3e\x3c\x62\162\x3e" . $zF;
        $mx->handle_error($lM);
        MO_Oauth_Debug::mo_oauth_log("\x45\x72\x72\x6f\162\40\167\150\x69\154\145\40\x66\x65\164\143\150\151\x6e\x67\x20\151\x64\x5f\164\157\x6b\145\156\72\40" . $lM);
        echo $lM;
        exit;
        goto gG;
        gd:
        return $Bn;
        gG:
    }
    public function get_resource_owner_from_id_token($JX)
    {
        global $mx;
        $MA = explode("\x2e", $JX);
        if (!isset($MA[1])) {
            goto m7;
        }
        $au = $mx->base64url_decode($MA[1]);
        if (!is_array(json_decode($au, true))) {
            goto gp;
        }
        return json_decode($au, true);
        gp:
        m7:
        $lM = "\111\156\x76\x61\x6c\x69\x64\40\162\x65\x73\160\157\x6e\x73\x65\x20\162\145\143\x65\151\166\145\x64\x2e\74\142\x72\x3e\x3c\163\x74\162\157\156\x67\76\x69\x64\x5f\x74\157\x6b\x65\x6e\x20\x3a\x20\74\57\163\x74\x72\157\x6e\x67\x3e" . $JX;
        $mx->handle_error($lM);
        MO_Oauth_Debug::mo_oauth_log("\105\x72\162\157\x72\40\x77\150\x69\154\x65\x20\146\x65\x74\x63\x68\151\156\x67\40\162\x65\x73\x6f\x75\162\x63\x65\x20\157\167\156\x65\x72\x20\x66\x72\157\155\40\x69\x64\x20\164\157\x6b\145\x6e\x3a" . $lM);
        echo $lM;
        exit;
    }
    public function get_resource_owner($yZ, $j6)
    {
        global $mx;
        $P_ = array();
        $P_["\x41\x75\x74\150\x6f\x72\151\x7a\x61\x74\x69\157\x6e"] = "\x42\145\141\162\x65\x72\x20" . $j6;
        $P_ = apply_filters("\155\157\137\x65\x78\x74\x65\156\x64\x5f\x75\163\145\162\151\x6e\146\157\x5f\160\x61\162\141\x6d\x73", $P_, $yZ);
        MO_Oauth_Debug::mo_oauth_log("\x52\145\163\x6f\x75\162\143\145\x20\117\167\156\x65\162\40\105\156\144\x70\x6f\151\156\164\x20\75\x3e\x20" . $yZ);
        MO_Oauth_Debug::mo_oauth_log("\x52\x65\163\x6f\165\x72\x63\x65\x20\117\167\156\145\x72\x20\162\145\161\165\x65\163\x74\40\143\157\x6e\x74\145\x6e\164\40\x3d\x3e\40");
        MO_Oauth_Debug::mo_oauth_log("\150\145\x61\x64\145\162\163\x20\75\76");
        MO_Oauth_Debug::mo_oauth_log($P_);
        $yZ = apply_filters("\155\x6f\137\x6f\x61\x75\164\x68\x5f\165\x73\145\162\151\156\x66\x6f\137\151\156\x74\x65\x72\x6e\141\154", $yZ);
        $zF = wp_remote_post($yZ, array("\x6d\x65\x74\x68\157\144" => "\x47\x45\124", "\164\x69\x6d\145\157\165\x74" => 45, "\x72\x65\x64\151\x72\x65\x63\x74\x69\x6f\156" => 5, "\150\x74\164\x70\166\x65\162\x73\151\157\156" => "\61\56\60", "\x62\x6c\157\143\153\x69\156\x67" => true, "\150\x65\x61\144\x65\x72\163" => $P_, "\143\157\157\x6b\151\145\x73" => array(), "\163\x73\154\166\145\162\151\x66\x79" => false));
        if (!is_wp_error($zF)) {
            goto Ik;
        }
        $mx->handle_error($zF->get_error_message());
        MO_Oauth_Debug::mo_oauth_log("\105\x72\162\x6f\x72\40\x66\x72\x6f\155\x20\122\x65\163\x6f\x75\162\143\145\40\x45\x6e\x64\x70\157\x69\156\164\72\x20" . $zF->get_error_message());
        wp_die(wp_kses($zF->get_error_message(), \mo_oauth_get_valid_html()));
        exit;
        Ik:
        $zF = $zF["\142\x6f\x64\171"];
        if (is_array(json_decode($zF, true))) {
            goto Nf;
        }
        $mx->handle_error("\111\156\x76\x61\x6c\x69\x64\40\162\145\163\160\157\156\163\145\x20\162\x65\143\x65\x69\166\145\144\x20\72\40" . $zF);
        echo "\74\x73\x74\x72\157\x6e\147\76\122\145\163\160\x6f\x6e\x73\145\x20\x3a\x20\74\x2f\x73\164\162\x6f\x6e\147\76\x3c\142\x72\76";
        print_r($zF);
        echo "\74\142\x72\x3e\74\142\162\x3e";
        MO_Oauth_Debug::mo_oauth_log("\x49\x6e\166\x61\x6c\151\144\40\162\145\163\x70\x6f\x6e\163\145\x20\x72\145\x63\151\145\166\x65\x64\40\167\x68\151\x6c\x65\x20\x66\145\x74\x63\x68\x69\156\x67\40\x72\145\163\157\x75\x72\143\145\x20\157\167\x6e\x65\162\x20\x64\x65\164\141\151\154\x73");
        exit("\111\156\x76\x61\x6c\151\144\40\x72\145\163\160\157\x6e\163\145\40\x72\x65\x63\145\x69\166\145\144\56");
        Nf:
        $Bn = json_decode($zF, true);
        if (!(strpos($yZ, "\x61\160\151\x2e\143\154\145\x76\x65\x72\56\x63\x6f\155") != false && isset($Bn["\154\151\156\153\163"][1]["\x75\162\151"]) && strpos($yZ, $Bn["\x6c\x69\x6e\x6b\163"][1]["\x75\162\151"]) === false)) {
            goto oK;
        }
        $jy = $Bn["\x6c\x69\156\x6b\163"][1]["\x75\162\151"];
        $ZU = "\150\x74\164\x70\x73\72\57\x2f\141\x70\x69\x2e\x63\154\145\166\x65\162\56\143\x6f\x6d" . $jy;
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\141\165\x74\150\x5f\x63\x6c\151\145\156\164\x5f\143\154\x65\x76\145\x72\x5f\x75\x73\x65\162\x5f\141\x70\x69", $ZU);
        $Bn = $this->get_resource_owner($ZU, $j6);
        oK:
        if (isset($Bn["\145\162\162\x6f\162\x5f\144\145\163\x63\162\151\x70\164\x69\157\x6e"])) {
            goto hX;
        }
        if (isset($Bn["\x65\x72\x72\x6f\x72"])) {
            goto CB;
        }
        goto DB;
        hX:
        $sa = "\x45\162\x72\157\162\x20\146\x72\x6f\x6d\x20\122\x65\x73\x6f\x75\162\143\145\40\x45\x6e\144\x70\x6f\x69\156\x74\72\40" . $Bn["\x65\162\x72\x6f\162\x5f\144\145\x73\x63\x72\151\160\x74\151\x6f\156"];
        $mx->handle_error($Bn["\145\x72\x72\x6f\162\x5f\144\x65\x73\x63\x72\x69\160\164\x69\x6f\156"]);
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\x6d\157\x5f\162\145\x64\151\x72\145\143\164\137\x74\157\137\x63\165\163\x74\157\x6d\x5f\145\162\162\x6f\162\137\160\141\x67\x65");
        exit(json_encode($Bn["\x65\x72\162\157\162\137\x64\145\163\x63\162\151\160\x74\151\157\x6e"]));
        goto DB;
        CB:
        $sa = "\x45\162\x72\x6f\162\x20\146\162\157\155\40\122\145\163\x6f\165\162\x63\145\x20\105\156\x64\160\157\151\x6e\164\x3a\x20" . $Bn["\x65\162\162\x6f\x72"];
        $mx->handle_error($Bn["\x65\162\162\x6f\x72"]);
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\x6d\157\137\x72\x65\144\x69\162\145\x63\x74\x5f\x74\x6f\x5f\143\165\163\x74\x6f\155\137\x65\162\162\157\x72\137\x70\x61\x67\x65");
        exit(json_encode($Bn["\x65\162\x72\157\162"]));
        DB:
        return $Bn;
    }
    public function get_response($QR)
    {
        $zF = wp_remote_get($QR, array("\x6d\145\x74\150\157\x64" => "\x47\x45\124", "\164\151\155\145\x6f\165\x74" => 45, "\162\x65\144\x69\x72\x65\143\164\x69\157\156" => 5, "\x68\164\x74\160\x76\145\x72\163\151\157\156" => 1.0, "\142\x6c\157\143\x6b\151\156\147" => true, "\150\145\141\144\x65\162\x73" => array(), "\x63\157\157\153\x69\145\163" => array(), "\163\163\x6c\166\145\x72\x69\146\x79" => false));
        if (!is_wp_error($zF)) {
            goto Kd;
        }
        MO_Oauth_Debug::mo_oauth_log($zF->get_error_message());
        wp_die(wp_kses($zF->get_error_message(), \mo_oauth_get_valid_html()));
        exit;
        Kd:
        $zF = $zF["\142\157\x64\171"];
        $Bn = json_decode($zF, true);
        if (isset($Bn["\145\x72\x72\x6f\x72\x5f\144\x65\163\x63\162\x69\x70\164\151\x6f\156"])) {
            goto DS;
        }
        if (isset($Bn["\145\x72\x72\x6f\162"])) {
            goto bV;
        }
        goto s7;
        DS:
        $mx->handle_error($Bn["\x65\x72\162\157\x72\137\144\x65\x73\x63\x72\151\160\164\x69\x6f\156"]);
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\x6d\157\x5f\x72\145\144\x69\x72\x65\143\164\x5f\164\x6f\x5f\x63\165\163\x74\x6f\x6d\137\x65\162\x72\157\162\x5f\160\141\x67\x65");
        goto s7;
        bV:
        $mx->handle_error($Bn["\x65\x72\x72\157\162"]);
        MO_Oauth_Debug::mo_oauth_log($sa);
        do_action("\155\x6f\137\162\145\144\x69\x72\145\x63\164\x5f\164\157\137\x63\x75\x73\x74\157\155\x5f\145\x72\x72\157\x72\x5f\x70\141\x67\145");
        s7:
        return $Bn;
    }
    private function handle_error($lM, $x1)
    {
        global $mx;
        if (!($x1["\147\162\141\156\x74\137\164\171\x70\x65"] === "\x70\x61\163\163\167\157\x72\x64")) {
            goto s_;
        }
        $a7 = $mx->get_current_url();
        $a7 = apply_filters("\x6d\x6f\x5f\157\141\165\164\150\137\x77\157\x6f\x63\157\x6d\x6d\x65\x72\143\145\x5f\143\150\145\143\x6b\x6f\165\x74\137\143\x6f\x6d\160\141\x74\x69\x62\151\x6c\151\x74\x79", $a7);
        if ($a7 != '') {
            goto DV;
        }
        return;
        goto SN;
        DV:
        $a7 = "\x3f\157\160\x74\x69\x6f\x6e\75\145\162\x72\157\x72\x6d\141\156\141\x67\x65\162\x26\x65\x72\162\157\162\75" . \base64_encode($lM);
        MO_Oauth_Debug::mo_oauth_log("\x45\x72\162\x6f\x72\x3a\40" . $lM);
        wp_die($lM);
        exit;
        SN:
        s_:
        MO_Oauth_Debug::mo_oauth_log("\x45\162\162\157\162\x3a\x20" . $lM);
        exit($lM);
    }
}
