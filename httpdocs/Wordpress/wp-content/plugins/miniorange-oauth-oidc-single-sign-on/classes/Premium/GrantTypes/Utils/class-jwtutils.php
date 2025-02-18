<?php


namespace MoOauthClient\GrantTypes;

use MoOauthClient\GrantTypes\JWSVerify;
use MoOauthClient\GrantTypes\Crypt_RSA;
use MoOauthClient\GrantTypes\Math_BigInteger;
class JWTUtils
{
    const HEADER = "\x48\105\101\x44\x45\x52";
    const PAYLOAD = "\x50\x41\131\x4c\x4f\x41\104";
    const SIGN = "\123\x49\x47\x4e";
    private $jwt;
    private $decoded_jwt;
    public function __construct($CS)
    {
        $CS = \explode("\56", $CS);
        if (!(3 > count($CS))) {
            goto Pzs;
        }
        return new \WP_Error("\151\156\x76\141\x6c\151\x64\137\x6a\x77\x74", __("\x4a\x57\x54\x20\x52\x65\x63\145\x69\166\x65\144\40\151\x73\40\x6e\157\x74\x20\x61\x20\x76\141\x6c\x69\x64\x20\112\x57\124"));
        Pzs:
        $this->jwt = $CS;
        $AD = $this->get_jwt_claim('', self::HEADER);
        $dY = $this->get_jwt_claim('', self::PAYLOAD);
        $this->decoded_jwt = array("\150\145\x61\x64\x65\162" => $AD, "\x70\141\x79\154\157\x61\144" => $dY);
    }
    private function get_jwt_claim($KC = '', $UC = '')
    {
        global $mx;
        $zK = '';
        switch ($UC) {
            case self::HEADER:
                $zK = $this->jwt[0];
                goto eYf;
            case self::PAYLOAD:
                $zK = $this->jwt[1];
                goto eYf;
            case self::SIGN:
                return $this->jwt[2];
            default:
                $mx->handle_error("\103\x61\x6e\156\157\164\40\106\151\x6e\144\40" . $UC . "\x20\151\x6e\x20\x74\150\x65\40\112\x57\x54");
                wp_die(wp_kses("\x43\141\156\x6e\x6f\x74\40\x46\x69\x6e\144\40" . $UC . "\x20\x69\x6e\40\x74\150\x65\x20\x4a\x57\x54", \mo_oauth_get_valid_html()));
        }
        WBi:
        eYf:
        $zK = json_decode($mx->base64url_decode($zK), true);
        if (!(!$zK || empty($zK))) {
            goto lpa;
        }
        return null;
        lpa:
        return empty($KC) ? $zK : (isset($zK[$KC]) ? $zK[$KC] : null);
    }
    public function check_algo($Lz = '')
    {
        global $mx;
        $oW = $this->get_jwt_claim("\141\154\x67", self::HEADER);
        $oW = explode("\123", $oW);
        if (isset($oW[0])) {
            goto FYO;
        }
        $lM = "\x49\x6e\x76\x61\154\x69\x64\40\x52\145\x73\x70\x6f\156\163\x65\40\122\145\143\145\x69\x76\x65\144\x20\x66\x72\157\x6d\x20\x4f\101\165\164\150\x2f\x4f\160\x65\156\111\x44\40\x50\x72\157\x76\151\x64\x65\x72\56";
        $mx->handle_error($lM);
        wp_die(wp_kses($lM, \mo_oauth_get_valid_html()));
        FYO:
        switch ($oW[0]) {
            case "\110":
                return "\x48\123\x41" === $Lz;
            case "\x52":
                return "\122\123\101" === $Lz;
            default:
                return false;
        }
        rJR:
        HuG:
    }
    public function verify($Ys = '')
    {
        global $mx;
        if (!empty($Ys)) {
            goto MbM;
        }
        return false;
        MbM:
        $IJ = $this->get_jwt_claim("\145\170\160", self::PAYLOAD);
        if (!(is_null($IJ) || time() > $IJ)) {
            goto M4x;
        }
        $nF = "\x4a\x57\124\40\x68\141\x73\x20\142\x65\145\x6e\40\x65\170\x70\x69\x72\x65\x64\56\x20\120\x6c\x65\141\163\x65\40\x74\162\171\40\114\157\x67\x67\x69\x6e\147\40\151\x6e\x20\x61\147\141\x69\156\x2e";
        $mx->handle_error($nF);
        wp_die(wp_kses($nF, \mo_oauth_get_valid_html()));
        M4x:
        $Do = $this->get_jwt_claim("\156\x62\x66", self::PAYLOAD);
        if (!(!is_null($Do) || time() < $Do)) {
            goto Ojv;
        }
        $Ru = "\111\164\x20\x69\163\x20\164\157\x6f\40\145\x61\162\154\171\x20\164\157\x20\x75\x73\x65\x20\x74\150\x69\163\x20\112\x57\x54\56\40\x50\x6c\145\141\163\x65\40\164\x72\x79\40\x4c\x6f\x67\147\151\x6e\147\40\x69\x6e\40\x61\x67\141\x69\x6e\56";
        $mx->handle_error($Ru);
        wp_die(wp_kses($Ru, \mo_oauth_get_valid_html()));
        Ojv:
        $Ad = new JWSVerify($this->get_jwt_claim("\x61\x6c\147", self::HEADER));
        $cj = $this->get_header() . "\56" . $this->get_payload();
        return $Ad->verify(\utf8_decode($cj), $Ys, base64_decode(strtr($this->get_jwt_claim(false, self::SIGN), "\x2d\x5f", "\53\57")));
    }
    public function verify_from_jwks($Xj = '', $oW = "\122\x53\x32\x35\x36")
    {
        global $mx;
        $r6 = wp_remote_get($Xj);
        if (!is_wp_error($r6)) {
            goto AuN;
        }
        return false;
        AuN:
        $r6 = json_decode($r6["\x62\157\x64\171"], true);
        $uL = false;
        if (!(json_last_error() !== JSON_ERROR_NONE)) {
            goto bZw;
        }
        return $uL;
        bZw:
        if (isset($r6["\153\x65\171\163"])) {
            goto z2a;
        }
        return $uL;
        z2a:
        foreach ($r6["\x6b\145\171\163"] as $NZ => $mB) {
            if (!(!isset($mB["\x6b\164\171"]) || "\x52\123\101" !== $mB["\x6b\164\x79"] || !isset($mB["\145"]) || !isset($mB["\x6e"]))) {
                goto Jpo;
            }
            goto nyl;
            Jpo:
            $uL = $uL || $this->verify($this->jwks_to_pem(["\x6e" => new Math_BigInteger($mx->base64url_decode($mB["\x6e"]), 256), "\x65" => new Math_BigInteger($mx->base64url_decode($mB["\x65"]), 256)]));
            if (!(true === $uL)) {
                goto An4;
            }
            goto Djl;
            An4:
            nyl:
        }
        Djl:
        return $uL;
    }
    private function jwks_to_pem($Tn = array())
    {
        $NS = new Crypt_RSA();
        $NS->loadKey($Tn);
        return $NS->getPublicKey();
    }
    public function get_decoded_header()
    {
        return $this->decoded_jwt["\x68\x65\141\x64\145\162"];
    }
    public function get_decoded_payload()
    {
        if (!isset($this->decoded_jwt["\160\141\x79\x6c\157\x61\x64"])) {
            goto Enu;
        }
        return $this->decoded_jwt["\160\141\x79\x6c\x6f\141\144"];
        Enu:
    }
    public function get_header()
    {
        return $this->jwt[0];
    }
    public function get_payload()
    {
        return $this->jwt[1];
    }
}
