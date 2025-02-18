<?php


namespace MoOauthClient\GrantTypes;

class JWSVerify
{
    public $algo;
    public function __construct($wI = '')
    {
        if (!empty($wI)) {
            goto wo3;
        }
        return;
        wo3:
        $wI = explode("\x53", $wI);
        if (!(!is_array($wI) || 2 !== count($wI))) {
            goto NL6;
        }
        return WP_Error("\x69\x6e\x76\141\154\151\144\x5f\x73\x69\x67\x6e\x61\164\165\x72\145", __("\x54\150\145\40\x53\x69\x67\156\x61\164\165\x72\x65\x20\163\145\145\x6d\x73\x20\164\x6f\x20\142\x65\x20\151\156\x76\x61\x6c\151\144\40\x6f\162\40\x75\156\x73\165\160\160\157\x72\164\145\144\x2e"));
        NL6:
        if ("\110" === $wI[0]) {
            goto Xzd;
        }
        if ("\122" === $wI[0]) {
            goto QdK;
        }
        return WP_Error("\x69\x6e\166\141\x6c\151\x64\137\163\x69\x67\x6e\x61\x74\x75\162\x65", __("\124\x68\145\x20\x73\x69\x67\x6e\141\164\x75\x72\x65\40\x61\x6c\x67\157\162\x69\x74\x68\155\x20\x73\145\145\x6d\x73\x20\164\157\40\142\x65\40\x75\156\x73\165\x70\x70\x6f\162\164\145\144\40\157\162\x20\151\156\x76\x61\154\x69\x64\x2e"));
        goto rPv;
        Xzd:
        $this->algo["\141\154\x67"] = "\110\123\101";
        goto rPv;
        QdK:
        $this->algo["\141\x6c\x67"] = "\122\123\101";
        rPv:
        $this->algo["\x73\150\x61"] = $wI[1];
    }
    private function validate_hmac($cj = '', $Ys = '', $YR = '')
    {
        if (!(empty($cj) || empty($YR))) {
            goto MtS;
        }
        return false;
        MtS:
        $Wr = $this->algo["\x73\150\x61"];
        $Wr = "\x73\150\x61" . $Wr;
        $Be = \hash_hmac($Wr, $cj, $Ys, true);
        return hash_equals($Be, $YR);
    }
    private function validate_rsa($cj = '', $hX = '', $YR = '')
    {
        if (!(empty($cj) || empty($YR))) {
            goto up2;
        }
        return false;
        up2:
        $Wr = $this->algo["\163\x68\x61"];
        $yV = '';
        $Rs = explode("\x2d\55\55\x2d\x2d", $hX);
        if (preg_match("\57\134\x72\134\156\174\134\162\x7c\x5c\x6e\x2f", $Rs[2])) {
            goto hb4;
        }
        $fk = "\55\x2d\x2d\55\55" . $Rs[1] . "\x2d\55\x2d\55\x2d\12";
        $FN = 0;
        W3l:
        if (!($zk = substr($Rs[2], $FN, 64))) {
            goto wvl;
        }
        $fk .= $zk . "\12";
        $FN += 64;
        goto W3l;
        wvl:
        $fk .= "\55\x2d\x2d\x2d\x2d" . $Rs[3] . "\x2d\x2d\x2d\55\x2d\xa";
        $yV = $fk;
        goto n4f;
        hb4:
        $yV = $hX;
        n4f:
        $uL = false;
        switch ($Wr) {
            case "\x32\65\x36":
                $uL = openssl_verify($cj, $YR, $yV, OPENSSL_ALGO_SHA256);
                goto S2l;
            case "\x33\x38\64":
                $uL = openssl_verify($cj, $YR, $yV, OPENSSL_ALGO_SHA384);
                goto S2l;
            case "\x35\61\x32":
                $uL = openssl_verify($cj, $YR, $yV, OPENSSL_ALGO_SHA512);
                goto S2l;
            default:
                $uL = false;
                goto S2l;
        }
        cut:
        S2l:
        return $uL;
    }
    public function verify($cj = '', $Ys = '', $YR = '')
    {
        if (!(empty($cj) || empty($YR))) {
            goto ILO;
        }
        return false;
        ILO:
        $wI = $this->algo["\141\154\x67"];
        switch ($wI) {
            case "\x48\x53\x41":
                return $this->validate_hmac($cj, $Ys, $YR);
            case "\122\123\101":
                return @$this->validate_rsa($cj, $Ys, $YR);
            default:
                return false;
        }
        TkD:
        wJd:
    }
}
