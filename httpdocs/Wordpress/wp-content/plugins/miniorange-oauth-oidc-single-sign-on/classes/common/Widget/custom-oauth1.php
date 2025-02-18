<?php


namespace MoOauthClient;

use MoOauthClient\MO_Custom_OAuth1;
use MoOauthClient\MO_Oauth_Debug;
class MO_Custom_OAuth1
{
    public static function mo_oauth1_auth_request($bj)
    {
        global $mx;
        $xA = $mx->get_app_by_name($bj)->get_app_config();
        $Sk = $xA["\143\x6c\x69\145\x6e\x74\137\x69\x64"];
        $xR = $xA["\143\x6c\x69\145\x6e\164\137\x73\145\x63\x72\x65\164"];
        $Or = $xA["\x61\x75\164\x68\157\162\151\172\x65\165\x72\x6c"];
        $ZA = $xA["\162\145\x71\x75\145\x73\164\165\x72\x6c"];
        $ad = $xA["\x61\143\x63\x65\163\x73\x74\157\x6b\x65\156\x75\162\154"];
        $iN = $xA["\x72\x65\x73\x6f\165\162\x63\145\x6f\x77\156\145\x72\144\145\164\x61\x69\x6c\163\165\x72\x6c"];
        $tJ = new MO_Custom_OAuth1_Flow($Sk, $xR, $ZA, $ad, $iN);
        $FZ = $tJ->mo_oauth1_get_request_token();
        if (!(strpos($Or, "\x3f") == false)) {
            goto F_;
        }
        $Or .= "\x3f";
        F_:
        $XZ = $Or . "\x6f\x61\165\x74\x68\137\164\x6f\x6b\x65\x6e\75" . $FZ;
        if (!($FZ == '' || $FZ == NULL)) {
            goto yL;
        }
        MO_Oauth_Debug::mo_oauth_log("\105\x72\x72\x6f\162\x20\151\156\40\122\x65\161\165\x65\163\164\40\124\x6f\x6b\145\x6e\x20\105\x6e\x64\160\x6f\151\156\164");
        wp_die("\x45\162\162\157\x72\40\151\156\x20\x52\x65\161\x75\x65\163\164\40\124\157\x6b\145\156\x20\105\156\x64\x70\x6f\151\156\x74\x3a\x20\x49\156\x76\141\154\x69\x64\x20\164\x6f\153\x65\x6e\40\162\145\x63\x65\151\166\145\144\56\40\x43\x6f\156\x74\141\x63\164\x20\x74\x6f\x20\171\157\x75\x72\40\x61\144\x6d\x69\x6d\x69\163\164\x72\141\164\157\162\x20\x66\x6f\162\x20\x6d\x6f\x72\145\40\151\156\x66\x6f\162\155\141\x74\x69\157\156\x2e");
        yL:
        MO_Oauth_Debug::mo_oauth_log("\x52\145\x71\x75\x65\x73\164\x20\x54\x6f\x6b\x65\x6e\40\162\x65\x63\x65\x69\x76\x65\x64\56");
        MO_Oauth_Debug::mo_oauth_log("\122\145\x71\165\145\x73\164\x20\x54\157\x6b\x65\156\x20\x3d\x3e\40" . $FZ);
        header("\x4c\x6f\x63\x61\164\151\157\156\x3a" . $XZ);
        exit;
    }
    static function mo_oidc1_get_access_token($bj)
    {
        $sw = explode("\x26", $_SERVER["\x52\105\x51\x55\x45\123\x54\x5f\125\x52\x49"]);
        $W4 = explode("\75", $sw[1]);
        $He = explode("\x3d", $sw[0]);
        $FO = get_option("\x6d\157\137\x6f\141\165\x74\150\x5f\x61\160\x70\x73\x5f\154\151\x73\164");
        $vP = $bj;
        $Uq = null;
        foreach ($FO as $NZ => $Zy) {
            if (!($bj == $NZ)) {
                goto u1;
            }
            $Uq = $Zy;
            goto wJ;
            u1:
            hT:
        }
        wJ:
        global $mx;
        $xA = $mx->get_app_by_name($bj)->get_app_config();
        $Sk = $xA["\143\154\x69\145\x6e\x74\137\x69\x64"];
        $xR = $xA["\x63\154\x69\145\156\164\137\163\145\x63\162\x65\x74"];
        $Or = $xA["\141\165\x74\x68\157\162\x69\172\x65\x75\162\x6c"];
        $ZA = $xA["\x72\x65\x71\165\x65\x73\164\x75\x72\x6c"];
        $ad = $xA["\x61\x63\143\145\x73\x73\164\157\153\x65\x6e\165\x72\154"];
        $iN = $xA["\x72\x65\x73\157\x75\x72\143\x65\x6f\167\x6e\145\162\x64\x65\164\x61\x69\154\x73\x75\x72\x6c"];
        $I5 = new MO_Custom_OAuth1_Flow($Sk, $xR, $ZA, $ad, $iN);
        $L1 = $I5->mo_oauth1_get_access_token($W4[1], $He[1]);
        $MK = explode("\x26", $L1);
        $ls = '';
        $OS = '';
        foreach ($MK as $NZ) {
            $NB = explode("\75", $NZ);
            if ($NB[0] == "\x6f\x61\165\x74\150\137\164\x6f\x6b\145\x6e") {
                goto xq;
            }
            if (!($NB[0] == "\157\x61\x75\x74\x68\x5f\164\157\153\x65\156\x5f\163\145\x63\162\x65\x74")) {
                goto Ov;
            }
            $OS = $NB[1];
            Ov:
            goto BW;
            xq:
            $ls = $NB[1];
            BW:
            pA:
        }
        oi:
        MO_Oauth_Debug::mo_oauth_log("\x41\143\x63\x65\163\163\40\x54\157\153\x65\156\x20\162\145\143\x65\151\166\145\144\56");
        MO_Oauth_Debug::mo_oauth_log("\101\143\143\x65\163\x73\40\124\x6f\153\145\x6e\40\x3d\x3e\x20" . $ls);
        $nq = new MO_Custom_OAuth1_Flow($Sk, $xR, $ZA, $ad, $iN);
        $BD = isset($tT[1]) ? $tT[1] : '';
        $uK = isset($T6[1]) ? $T6[1] : '';
        $DN = isset($EX[1]) ? $EX[1] : '';
        $SI = $nq->mo_oauth1_get_profile_signature($ls, $OS);
        if (isset($SI)) {
            goto Fu;
        }
        wp_die("\111\x6e\x76\141\x6c\x69\x64\x20\x43\157\x6e\146\151\x67\165\162\141\164\x69\157\156\163\x2e\x20\120\x6c\x65\x61\163\x65\40\x63\157\156\x74\141\143\164\40\x74\x6f\40\164\x68\x65\40\x61\144\155\151\155\x69\163\164\162\141\x74\x6f\162\40\x66\x6f\x72\40\155\157\162\145\x20\x69\x6e\x66\x6f\x72\155\x61\164\x69\157\156");
        Fu:
        return $SI;
    }
}
class MO_Custom_OAuth1_Flow
{
    var $key = '';
    var $secret = '';
    var $request_token_url = '';
    var $access_token_url = '';
    var $userinfo_url = '';
    function __construct($pw, $xR, $ZA, $ad, $iN)
    {
        $this->key = $pw;
        $this->secret = $xR;
        $this->request_token_url = $ZA;
        $this->access_token_url = $ad;
        $this->userinfo_url = $iN;
    }
    function mo_oauth1_get_request_token()
    {
        $Hz = array("\x6f\141\165\164\150\137\166\x65\162\x73\x69\157\x6e" => "\x31\56\x30", "\x6f\x61\165\x74\150\137\156\157\x6e\x63\x65" => time(), "\x6f\x61\165\164\x68\137\164\x69\x6d\145\163\x74\141\155\160" => time(), "\x6f\x61\x75\x74\150\x5f\x63\157\x6e\x73\x75\x6d\145\162\137\153\145\x79" => $this->key, "\157\x61\165\164\x68\x5f\163\x69\x67\x6e\141\x74\165\x72\x65\x5f\155\x65\164\150\157\144" => "\x48\115\101\103\55\x53\x48\101\x31");
        if (!(strpos($this->request_token_url, "\x3f") != false)) {
            goto Wr;
        }
        $gu = explode("\77", $this->request_token_url);
        $this->request_token_url = $gu[0];
        $f_ = explode("\46", $gu[1]);
        foreach ($f_ as $j8) {
            $sD = explode("\x3d", $j8);
            $Hz[$sD[0]] = $sD[1];
            Yt:
        }
        Pn:
        Wr:
        $T3 = array_keys($Hz);
        $tv = array_values($Hz);
        $Hz = $this->mo_oauth1_url_encode_rfc3986(array_combine($T3, $tv));
        uksort($Hz, "\163\164\162\143\155\x70");
        foreach ($Hz as $lm => $ZI) {
            $L5[] = $this->mo_oauth1_url_encode_rfc3986($lm) . "\x3d" . $this->mo_oauth1_url_encode_rfc3986($ZI);
            BB:
        }
        Ey:
        $gS = implode("\x26", $L5);
        $iB = $gS;
        $iB = str_replace("\x3d", "\45\63\104", $iB);
        $iB = str_replace("\x26", "\x25\62\66", $iB);
        $iB = "\x47\105\124\46" . $this->mo_oauth1_url_encode_rfc3986($this->request_token_url) . "\46" . $iB;
        $Ys = $this->mo_oauth1_url_encode_rfc3986($this->secret) . "\x26";
        $Hz["\157\141\165\164\x68\137\163\x69\x67\x6e\x61\x74\x75\x72\x65"] = $this->mo_oauth1_url_encode_rfc3986(base64_encode(hash_hmac("\x73\150\141\x31", $iB, $Ys, TRUE)));
        uksort($Hz, "\163\164\x72\143\155\160");
        foreach ($Hz as $lm => $ZI) {
            $d5[] = $lm . "\x3d" . $ZI;
            a7:
        }
        Ye:
        $ev = implode("\46", $d5);
        $QR = $this->request_token_url . "\x3f" . $ev;
        MO_Oauth_Debug::mo_oauth_log("\122\145\161\165\x65\x73\x74\40\x54\x6f\x6b\x65\x6e\x20\x55\122\114\x20\75\76\40" . $QR);
        $zF = $this->mo_oauth1_https($QR);
        MO_Oauth_Debug::mo_oauth_log("\122\x65\x71\165\145\163\x74\40\124\157\153\x65\156\x20\x45\156\144\160\157\x69\x6e\x74\x20\x52\145\x73\x70\157\x6e\163\145\40\75\76\40");
        MO_Oauth_Debug::mo_oauth_log($zF);
        $vh = explode("\46", $zF);
        $e0 = '';
        foreach ($vh as $NZ) {
            $NB = explode("\75", $NZ);
            if ($NB[0] == "\x6f\141\x75\x74\x68\x5f\164\x6f\153\x65\x6e") {
                goto iv;
            }
            if (!($NB[0] == "\157\x61\165\x74\150\137\x74\157\153\145\156\137\x73\145\x63\162\145\x74")) {
                goto TD;
            }
            setcookie("\155\157\x5f\164\163", $NB[1], time() + 30);
            TD:
            goto qE;
            iv:
            $e0 = $NB[1];
            qE:
            Ri:
        }
        l4:
        return $e0;
    }
    function mo_oauth1_get_access_token($W4, $He)
    {
        $Hz = array("\157\141\x75\x74\x68\137\166\x65\x72\x73\151\x6f\156" => "\61\x2e\60", "\x6f\x61\x75\164\150\137\x6e\157\156\x63\x65" => time(), "\157\141\x75\x74\x68\137\x74\151\155\145\x73\164\141\x6d\160" => time(), "\157\x61\x75\164\150\137\x63\x6f\156\x73\x75\155\145\162\137\153\x65\x79" => $this->key, "\x6f\141\x75\164\150\137\164\157\153\145\x6e" => $He, "\157\141\x75\x74\x68\x5f\163\x69\x67\x6e\141\164\x75\x72\x65\137\x6d\145\164\x68\x6f\144" => "\110\115\x41\x43\55\123\x48\x41\61", "\x6f\141\165\164\x68\137\x76\x65\x72\x69\x66\x69\x65\x72" => $W4);
        $T3 = $this->mo_oauth1_url_encode_rfc3986(array_keys($Hz));
        $tv = $this->mo_oauth1_url_encode_rfc3986(array_values($Hz));
        $Hz = array_combine($T3, $tv);
        uksort($Hz, "\x73\164\162\x63\x6d\x70");
        foreach ($Hz as $lm => $ZI) {
            $L5[] = $this->mo_oauth1_url_encode_rfc3986($lm) . "\75" . $this->mo_oauth1_url_encode_rfc3986($ZI);
            ZS:
        }
        nW:
        $gS = implode("\x26", $L5);
        $iB = $gS;
        $iB = str_replace("\75", "\45\x33\104", $iB);
        $iB = str_replace("\46", "\45\x32\x36", $iB);
        $iB = "\x47\105\124\x26" . $this->mo_oauth1_url_encode_rfc3986($this->access_token_url) . "\x26" . $iB;
        $MS = isset($_COOKIE["\155\157\137\x74\x73"]) ? $_COOKIE["\x6d\157\137\164\x73"] : '';
        $Ys = $this->mo_oauth1_url_encode_rfc3986($this->secret) . "\x26" . $MS;
        $Hz["\157\141\x75\x74\x68\137\x73\x69\147\156\141\164\x75\x72\145"] = $this->mo_oauth1_url_encode_rfc3986(base64_encode(hash_hmac("\x73\150\141\x31", $iB, $Ys, TRUE)));
        uksort($Hz, "\x73\x74\162\143\155\x70");
        foreach ($Hz as $lm => $ZI) {
            $d5[] = $lm . "\75" . $ZI;
            ee:
        }
        N8:
        $ev = implode("\46", $d5);
        $QR = $this->access_token_url . "\x3f" . $ev;
        MO_Oauth_Debug::mo_oauth_log("\101\143\x63\x65\x73\x73\x20\124\157\x6b\x65\156\x20\105\x6e\144\x70\157\x69\x6e\x74\40\125\x52\114\40\75\76\40" . $QR);
        $zF = $this->mo_oauth1_https($QR);
        MO_Oauth_Debug::mo_oauth_log("\x41\x63\143\145\163\163\x20\124\x6f\x6b\x65\x6e\40\x45\156\144\x70\x6f\x69\x6e\x74\x20\x52\x65\x73\x70\x6f\156\x73\x65\40\75\x3e\x20" . $zF);
        return $zF;
    }
    function mo_oauth1_get_profile_signature($L1, $T6, $EX = '')
    {
        $Hz = array("\157\141\165\164\150\137\x76\x65\162\x73\x69\157\x6e" => "\61\56\x30", "\x6f\x61\165\x74\x68\137\x6e\157\x6e\143\x65" => time(), "\x6f\x61\165\x74\x68\137\164\x69\155\x65\x73\x74\141\x6d\160" => time(), "\x6f\141\x75\x74\x68\137\143\157\x6e\x73\x75\x6d\x65\x72\x5f\x6b\145\x79" => $this->key, "\157\141\x75\x74\150\x5f\x74\157\153\x65\x6e" => $L1, "\x6f\x61\165\x74\150\137\x73\x69\147\156\x61\x74\165\162\145\x5f\155\145\x74\150\x6f\x64" => "\x48\115\x41\103\55\x53\x48\101\x31");
        if (!(strpos($this->userinfo_url, "\77") != false)) {
            goto xf;
        }
        $gu = explode("\x3f", $this->userinfo_url);
        $this->userinfo_url = $gu[0];
        $f_ = explode("\x26", $gu[1]);
        foreach ($f_ as $j8) {
            $sD = explode("\75", $j8);
            $Hz[$sD[0]] = $sD[1];
            Nj:
        }
        sg:
        xf:
        $T3 = $this->mo_oauth1_url_encode_rfc3986(array_keys($Hz));
        $tv = $this->mo_oauth1_url_encode_rfc3986(array_values($Hz));
        $Hz = array_combine($T3, $tv);
        uksort($Hz, "\x73\x74\x72\x63\x6d\160");
        foreach ($Hz as $lm => $ZI) {
            $L5[] = $this->mo_oauth1_url_encode_rfc3986($lm) . "\x3d" . $this->mo_oauth1_url_encode_rfc3986($ZI);
            eM:
        }
        j0:
        $gS = implode("\46", $L5);
        $iB = "\107\x45\x54\46" . $this->mo_oauth1_url_encode_rfc3986($this->userinfo_url) . "\x26" . $this->mo_oauth1_url_encode_rfc3986($gS);
        $Ys = $this->mo_oauth1_url_encode_rfc3986($this->secret) . "\x26" . $this->mo_oauth1_url_encode_rfc3986($T6);
        $Hz["\x6f\x61\x75\x74\x68\x5f\x73\x69\x67\156\141\x74\165\x72\145"] = $this->mo_oauth1_url_encode_rfc3986(base64_encode(hash_hmac("\163\150\141\x31", $iB, $Ys, TRUE)));
        uksort($Hz, "\163\164\162\x63\x6d\x70");
        foreach ($Hz as $lm => $ZI) {
            $d5[] = $lm . "\75" . $ZI;
            ud:
        }
        bo:
        $ev = implode("\46", $d5);
        $QR = $this->userinfo_url . "\77" . $ev;
        MO_Oauth_Debug::mo_oauth_log("\x52\145\x73\x6f\165\162\143\145\40\105\x6e\144\x70\x6f\x69\156\164\40\125\x52\x4c\x20\75\76\x20" . $QR);
        $x1 = array();
        MO_Oauth_Debug::mo_oauth_log("\122\145\163\x6f\x75\x72\x63\145\40\105\156\144\x70\157\151\156\x74\40\x69\156\x66\x6f\x20\75\x3e\x20");
        MO_Oauth_Debug::mo_oauth_log($Hz);
        $eu = wp_remote_get($QR, $x1);
        MO_Oauth_Debug::mo_oauth_log("\122\x65\x73\x6f\x75\x72\143\x65\40\x45\156\144\160\157\151\x6e\164\40\122\x65\x73\160\x6f\x6e\x73\145\40\x3d\76\40");
        MO_Oauth_Debug::mo_oauth_log($eu);
        $SI = json_decode($eu["\142\x6f\144\x79"], true);
        return $SI;
    }
    function mo_oauth1_https($QR, $vI = null)
    {
        if (!isset($vI)) {
            goto I1;
        }
        $x1 = array("\155\x65\x74\x68\x6f\x64" => "\x50\x4f\x53\x54", "\142\x6f\x64\171" => $vI, "\x74\151\155\x65\x6f\x75\164" => "\x31\x35", "\162\145\x64\x69\x72\x65\x63\x74\151\x6f\156" => "\65", "\x68\x74\164\x70\166\x65\x72\163\151\157\x6e" => "\61\56\x30", "\x62\154\157\x63\x6b\151\156\x67" => true);
        MO_Oauth_Debug::mo_oauth_log("\117\x61\x75\164\150\61\x20\x50\117\123\x54\40\105\156\144\x70\157\x69\x6e\x74\40\x41\162\147\x75\155\145\156\164\163\40\75\x3e\x20");
        MO_Oauth_Debug::mo_oauth_log($eu);
        $ih = wp_remote_post($QR, $x1);
        return $ih["\142\157\144\x79"];
        I1:
        $x1 = array();
        $eu = wp_remote_get($QR, $x1);
        if (!is_wp_error($eu)) {
            goto WW;
        }
        wp_die($eu);
        WW:
        $zF = $eu["\142\157\x64\x79"];
        return $zF;
    }
    function mo_oauth1_url_encode_rfc3986($pR)
    {
        if (is_array($pR)) {
            goto Gw;
        }
        if (is_scalar($pR)) {
            goto c3;
        }
        return '';
        goto uX;
        c3:
        return str_replace("\x2b", "\40", str_replace("\45\67\105", "\176", rawurlencode($pR)));
        uX:
        goto Vi;
        Gw:
        return array_map(array("\x4d\x6f\x4f\141\x75\164\150\x43\x6c\151\145\x6e\164\134\115\x4f\x5f\103\x75\x73\164\157\155\137\x4f\101\165\164\150\61\x5f\x46\x6c\x6f\x77", "\x6d\x6f\x5f\x6f\141\165\x74\150\61\x5f\x75\x72\154\x5f\145\x6e\143\x6f\x64\x65\137\162\x66\x63\63\x39\x38\66"), $pR);
        Vi:
    }
}
