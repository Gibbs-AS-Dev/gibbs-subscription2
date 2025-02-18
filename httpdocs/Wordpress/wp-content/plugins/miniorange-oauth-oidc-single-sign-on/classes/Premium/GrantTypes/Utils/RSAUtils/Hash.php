<?php


namespace MoOauthClient\GrantTypes;

if (defined("\103\122\131\x50\x54\137\110\x41\123\110\x5f\x4d\117\104\105\x5f\x49\116\x54\105\x52\116\101\x4c")) {
    goto aMv;
}
define("\x43\x52\131\120\124\x5f\110\x41\x53\110\x5f\115\x4f\104\105\137\111\x4e\124\105\122\116\x41\114", 1);
aMv:
if (defined("\103\122\131\120\x54\137\110\x41\x53\110\x5f\x4d\x4f\x44\105\137\115\x48\x41\x53\x48")) {
    goto CgA;
}
define("\x43\122\x59\x50\x54\x5f\x48\x41\123\x48\137\x4d\x4f\104\x45\137\115\x48\101\x53\110", 2);
CgA:
if (defined("\x43\122\x59\120\124\x5f\110\x41\x53\110\137\115\117\104\x45\x5f\110\101\x53\x48")) {
    goto PqO;
}
define("\103\122\x59\120\124\x5f\110\101\123\110\137\x4d\x4f\x44\105\137\110\x41\123\x48", 3);
PqO:
class Crypt_Hash
{
    var $hashParam;
    var $b;
    var $l = false;
    var $hash;
    var $key = false;
    var $opad;
    var $ipad;
    function __construct($cA = "\163\150\141\61")
    {
        if (defined("\x43\x52\x59\x50\x54\x5f\x48\x41\123\x48\x5f\x4d\117\x44\x45")) {
            goto AVC;
        }
        switch (true) {
            case extension_loaded("\x68\x61\163\x68"):
                define("\x43\122\x59\x50\124\x5f\110\x41\123\x48\137\115\x4f\104\x45", CRYPT_HASH_MODE_HASH);
                goto Gmn;
            case extension_loaded("\x6d\150\141\163\150"):
                define("\x43\122\x59\120\124\137\110\101\123\110\x5f\x4d\117\104\x45", CRYPT_HASH_MODE_MHASH);
                goto Gmn;
            default:
                define("\103\122\x59\x50\124\x5f\110\101\x53\110\x5f\x4d\x4f\x44\x45", CRYPT_HASH_MODE_INTERNAL);
        }
        kOl:
        Gmn:
        AVC:
        $this->setHash($cA);
    }
    function Crypt_Hash($cA = "\163\150\141\x31")
    {
        $this->__construct($cA);
    }
    function setKey($NZ = false)
    {
        $this->key = $NZ;
    }
    function getHash()
    {
        return $this->hashParam;
    }
    function setHash($cA)
    {
        $this->hashParam = $cA = strtolower($cA);
        switch ($cA) {
            case "\155\144\65\55\x39\x36":
            case "\163\150\x61\x31\55\x39\66":
            case "\x73\x68\141\62\x35\x36\x2d\x39\66":
            case "\163\x68\141\65\x31\x32\x2d\71\66":
                $cA = substr($cA, 0, -3);
                $this->l = 12;
                goto Hf5;
            case "\x6d\x64\x32":
            case "\155\144\x35":
                $this->l = 16;
                goto Hf5;
            case "\x73\x68\141\x31":
                $this->l = 20;
                goto Hf5;
            case "\163\150\141\x32\65\66":
                $this->l = 32;
                goto Hf5;
            case "\x73\150\141\63\70\x34":
                $this->l = 48;
                goto Hf5;
            case "\163\x68\141\x35\x31\x32":
                $this->l = 64;
        }
        yai:
        Hf5:
        switch ($cA) {
            case "\155\144\62":
                $qi = CRYPT_HASH_MODE == CRYPT_HASH_MODE_HASH && in_array("\155\x64\x32", hash_algos()) ? CRYPT_HASH_MODE_HASH : CRYPT_HASH_MODE_INTERNAL;
                goto KpM;
            case "\163\x68\141\x33\70\64":
            case "\163\x68\x61\x35\x31\x32":
                $qi = CRYPT_HASH_MODE == CRYPT_HASH_MODE_MHASH ? CRYPT_HASH_MODE_INTERNAL : CRYPT_HASH_MODE;
                goto KpM;
            default:
                $qi = CRYPT_HASH_MODE;
        }
        InV:
        KpM:
        switch ($qi) {
            case CRYPT_HASH_MODE_MHASH:
                switch ($cA) {
                    case "\155\144\65":
                        $this->hash = MHASH_MD5;
                        goto FvT;
                    case "\163\x68\x61\x32\65\66":
                        $this->hash = MHASH_SHA256;
                        goto FvT;
                    case "\x73\150\x61\x31":
                    default:
                        $this->hash = MHASH_SHA1;
                }
                OFw:
                FvT:
                return;
            case CRYPT_HASH_MODE_HASH:
                switch ($cA) {
                    case "\x6d\144\x35":
                        $this->hash = "\x6d\x64\x35";
                        return;
                    case "\155\144\x32":
                    case "\x73\x68\141\62\x35\66":
                    case "\163\150\141\x33\70\x34":
                    case "\163\x68\x61\x35\x31\62":
                        $this->hash = $cA;
                        return;
                    case "\x73\x68\x61\x31":
                    default:
                        $this->hash = "\163\x68\141\61";
                }
                uUt:
                XMU:
                return;
        }
        z6f:
        qXE:
        switch ($cA) {
            case "\x6d\144\x32":
                $this->b = 16;
                $this->hash = array($this, "\137\155\x64\62");
                goto Sm2;
            case "\x6d\x64\65":
                $this->b = 64;
                $this->hash = array($this, "\137\155\144\65");
                goto Sm2;
            case "\163\x68\x61\62\x35\66":
                $this->b = 64;
                $this->hash = array($this, "\137\x73\150\x61\62\65\66");
                goto Sm2;
            case "\x73\150\x61\63\x38\x34":
            case "\163\x68\x61\x35\61\62":
                $this->b = 128;
                $this->hash = array($this, "\x5f\x73\x68\141\65\61\62");
                goto Sm2;
            case "\163\150\141\61":
            default:
                $this->b = 64;
                $this->hash = array($this, "\137\x73\150\141\x31");
        }
        YH4:
        Sm2:
        $this->ipad = str_repeat(chr(0x36), $this->b);
        $this->opad = str_repeat(chr(0x5c), $this->b);
    }
    function hash($dr)
    {
        $qi = is_array($this->hash) ? CRYPT_HASH_MODE_INTERNAL : CRYPT_HASH_MODE;
        if (!empty($this->key) || is_string($this->key)) {
            goto gC0;
        }
        switch ($qi) {
            case CRYPT_HASH_MODE_MHASH:
                $YW = mhash($this->hash, $dr);
                goto uXt;
            case CRYPT_HASH_MODE_HASH:
                $YW = hash($this->hash, $dr, true);
                goto uXt;
            case CRYPT_HASH_MODE_INTERNAL:
                $YW = call_user_func($this->hash, $dr);
        }
        reo:
        uXt:
        goto SnF;
        gC0:
        switch ($qi) {
            case CRYPT_HASH_MODE_MHASH:
                $YW = mhash($this->hash, $dr, $this->key);
                goto oJM;
            case CRYPT_HASH_MODE_HASH:
                $YW = hash_hmac($this->hash, $dr, $this->key, true);
                goto oJM;
            case CRYPT_HASH_MODE_INTERNAL:
                $NZ = strlen($this->key) > $this->b ? call_user_func($this->hash, $this->key) : $this->key;
                $NZ = str_pad($NZ, $this->b, chr(0));
                $gu = $this->ipad ^ $NZ;
                $gu .= $dr;
                $gu = call_user_func($this->hash, $gu);
                $YW = $this->opad ^ $NZ;
                $YW .= $gu;
                $YW = call_user_func($this->hash, $YW);
        }
        u_X:
        oJM:
        SnF:
        return substr($YW, 0, $this->l);
    }
    function getLength()
    {
        return $this->l;
    }
    function _md5($x4)
    {
        return pack("\x48\52", md5($x4));
    }
    function _sha1($x4)
    {
        return pack("\x48\52", sha1($x4));
    }
    function _md2($x4)
    {
        static $bf = array(41, 46, 67, 201, 162, 216, 124, 1, 61, 54, 84, 161, 236, 240, 6, 19, 98, 167, 5, 243, 192, 199, 115, 140, 152, 147, 43, 217, 188, 76, 130, 202, 30, 155, 87, 60, 253, 212, 224, 22, 103, 66, 111, 24, 138, 23, 229, 18, 190, 78, 196, 214, 218, 158, 222, 73, 160, 251, 245, 142, 187, 47, 238, 122, 169, 104, 121, 145, 21, 178, 7, 63, 148, 194, 16, 137, 11, 34, 95, 33, 128, 127, 93, 154, 90, 144, 50, 39, 53, 62, 204, 231, 191, 247, 151, 3, 255, 25, 48, 179, 72, 165, 181, 209, 215, 94, 146, 42, 172, 86, 170, 198, 79, 184, 56, 210, 150, 164, 125, 182, 118, 252, 107, 226, 156, 116, 4, 241, 69, 157, 112, 89, 100, 113, 135, 32, 134, 91, 207, 101, 230, 45, 168, 2, 27, 96, 37, 173, 174, 176, 185, 246, 28, 70, 97, 105, 52, 64, 126, 15, 85, 71, 163, 35, 221, 81, 175, 58, 195, 92, 249, 206, 186, 197, 234, 38, 44, 83, 13, 110, 133, 40, 132, 9, 211, 223, 205, 244, 65, 129, 77, 82, 106, 220, 55, 200, 108, 193, 171, 250, 36, 225, 123, 8, 12, 189, 177, 74, 120, 136, 149, 139, 227, 99, 232, 109, 233, 203, 213, 254, 59, 0, 29, 57, 242, 239, 183, 14, 102, 88, 208, 228, 166, 119, 114, 248, 235, 117, 75, 10, 49, 68, 80, 180, 143, 237, 31, 26, 219, 153, 141, 51, 159, 17, 131, 20);
        $T2 = 16 - (strlen($x4) & 0xf);
        $x4 .= str_repeat(chr($T2), $T2);
        $ql = strlen($x4);
        $K6 = str_repeat(chr(0), 16);
        $TH = chr(0);
        $wz = 0;
        u_S:
        if (!($wz < $ql)) {
            goto fEt;
        }
        $L4 = 0;
        SpA:
        if (!($L4 < 16)) {
            goto h4Z;
        }
        $K6[$L4] = chr($bf[ord($x4[$wz + $L4] ^ $TH)] ^ ord($K6[$L4]));
        $TH = $K6[$L4];
        Vd4:
        $L4++;
        goto SpA;
        h4Z:
        X4W:
        $wz += 16;
        goto u_S;
        fEt:
        $x4 .= $K6;
        $ql += 16;
        $Av = str_repeat(chr(0), 48);
        $wz = 0;
        XZM:
        if (!($wz < $ql)) {
            goto FzM;
        }
        $L4 = 0;
        Jse:
        if (!($L4 < 16)) {
            goto e9K;
        }
        $Av[$L4 + 16] = $x4[$wz + $L4];
        $Av[$L4 + 32] = $Av[$L4 + 16] ^ $Av[$L4];
        Jih:
        $L4++;
        goto Jse;
        e9K:
        $wq = chr(0);
        $L4 = 0;
        USP:
        if (!($L4 < 18)) {
            goto vbh;
        }
        $lm = 0;
        tLl:
        if (!($lm < 48)) {
            goto IUs;
        }
        $Av[$lm] = $wq = $Av[$lm] ^ chr($bf[ord($wq)]);
        cM6:
        $lm++;
        goto tLl;
        IUs:
        $wq = chr(ord($wq) + $L4);
        LB3:
        $L4++;
        goto USP;
        vbh:
        EaI:
        $wz += 16;
        goto XZM;
        FzM:
        return substr($Av, 0, 16);
    }
    function _sha256($x4)
    {
        if (!extension_loaded("\163\165\x68\157\163\x69\x6e")) {
            goto bsn;
        }
        return pack("\110\x2a", sha256($x4));
        bsn:
        $cA = array(0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19);
        static $lm = array(0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0xfc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x6ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2);
        $ql = strlen($x4);
        $x4 .= str_repeat(chr(0), 64 - ($ql + 8 & 0x3f));
        $x4[$ql] = chr(0x80);
        $x4 .= pack("\116\62", 0, $ql << 3);
        $oT = str_split($x4, 64);
        foreach ($oT as $uC) {
            $ya = array();
            $wz = 0;
            Pqc:
            if (!($wz < 16)) {
                goto OXd;
            }
            extract(unpack("\116\164\145\x6d\160", $this->_string_shift($uC, 4)));
            $ya[] = $gu;
            wDS:
            $wz++;
            goto Pqc;
            OXd:
            $wz = 16;
            iMC:
            if (!($wz < 64)) {
                goto BxN;
            }
            $di = $this->_rightRotate($ya[$wz - 15], 7) ^ $this->_rightRotate($ya[$wz - 15], 18) ^ $this->_rightShift($ya[$wz - 15], 3);
            $RJ = $this->_rightRotate($ya[$wz - 2], 17) ^ $this->_rightRotate($ya[$wz - 2], 19) ^ $this->_rightShift($ya[$wz - 2], 10);
            $ya[$wz] = $this->_add($ya[$wz - 16], $di, $ya[$wz - 7], $RJ);
            SN6:
            $wz++;
            goto iMC;
            BxN:
            list($aI, $E8, $K6, $XN, $tS, $xa, $Nw, $ce) = $cA;
            $wz = 0;
            k1A:
            if (!($wz < 64)) {
                goto N9Q;
            }
            $di = $this->_rightRotate($aI, 2) ^ $this->_rightRotate($aI, 13) ^ $this->_rightRotate($aI, 22);
            $mM = $aI & $E8 ^ $aI & $K6 ^ $E8 & $K6;
            $kR = $this->_add($di, $mM);
            $RJ = $this->_rightRotate($tS, 6) ^ $this->_rightRotate($tS, 11) ^ $this->_rightRotate($tS, 25);
            $oK = $tS & $xa ^ $this->_not($tS) & $Nw;
            $YS = $this->_add($ce, $RJ, $oK, $lm[$wz], $ya[$wz]);
            $ce = $Nw;
            $Nw = $xa;
            $xa = $tS;
            $tS = $this->_add($XN, $YS);
            $XN = $K6;
            $K6 = $E8;
            $E8 = $aI;
            $aI = $this->_add($YS, $kR);
            kUW:
            $wz++;
            goto k1A;
            N9Q:
            $cA = array($this->_add($cA[0], $aI), $this->_add($cA[1], $E8), $this->_add($cA[2], $K6), $this->_add($cA[3], $XN), $this->_add($cA[4], $tS), $this->_add($cA[5], $xa), $this->_add($cA[6], $Nw), $this->_add($cA[7], $ce));
            JCu:
        }
        yyo:
        return pack("\x4e\70", $cA[0], $cA[1], $cA[2], $cA[3], $cA[4], $cA[5], $cA[6], $cA[7]);
    }
    function _sha512($x4)
    {
        if (class_exists("\x4d\141\x74\x68\x5f\x42\x69\x67\x49\156\x74\x65\x67\145\162")) {
            goto Dxh;
        }
        include_once "\115\141\x74\150\57\x42\151\147\x49\156\164\x65\x67\x65\x72\56\x70\x68\160";
        Dxh:
        static $VD, $ZT, $lm;
        if (isset($lm)) {
            goto A_q;
        }
        $VD = array("\x63\142\x62\142\x39\144\65\x64\143\x31\x30\65\71\145\144\70", "\66\x32\71\141\62\71\x32\x61\63\x36\x37\x63\144\65\x30\x37", "\x39\61\x35\x39\60\61\65\141\x33\60\67\x30\144\144\61\67", "\61\x35\62\146\145\143\x64\70\146\x37\x30\x65\x35\71\x33\x39", "\x36\x37\63\63\x32\x36\x36\67\146\146\143\60\60\x62\63\x31", "\x38\145\142\64\x34\x61\x38\x37\66\70\x35\70\61\x35\61\x31", "\144\x62\60\143\x32\x65\x30\x64\66\x34\146\x39\x38\146\x61\67", "\x34\67\x62\x35\x34\x38\x31\144\x62\145\x66\x61\64\x66\x61\64");
        $ZT = array("\x36\x61\x30\71\x65\x36\x36\67\146\x33\x62\143\x63\x39\x30\70", "\x62\x62\66\67\x61\x65\x38\65\x38\64\143\x61\141\67\x33\142", "\63\143\x36\145\146\63\67\x32\146\x65\x39\x34\146\70\x32\x62", "\x61\65\x34\146\146\65\63\141\65\x66\61\x64\63\x36\146\x31", "\x35\x31\60\145\65\x32\67\146\141\144\x65\66\70\x32\144\x31", "\x39\x62\60\x35\66\x38\70\143\x32\142\63\x65\x36\143\x31\x66", "\61\146\70\63\x64\x39\141\x62\146\x62\64\61\x62\144\66\x62", "\x35\142\x65\x30\143\x64\61\71\61\63\x37\145\62\61\x37\x39");
        $wz = 0;
        hhz:
        if (!($wz < 8)) {
            goto FJI;
        }
        $VD[$wz] = new Math_BigInteger($VD[$wz], 16);
        $VD[$wz]->setPrecision(64);
        $ZT[$wz] = new Math_BigInteger($ZT[$wz], 16);
        $ZT[$wz]->setPrecision(64);
        xUE:
        $wz++;
        goto hhz;
        FJI:
        $lm = array("\x34\x32\70\x61\62\146\x39\70\x64\x37\x32\x38\141\x65\62\62", "\x37\61\63\x37\x34\64\71\x31\x32\x33\x65\x66\66\x35\x63\x64", "\142\65\x63\60\146\142\143\146\x65\x63\x34\x64\63\x62\x32\146", "\x65\x39\142\x35\x64\x62\141\x35\70\61\x38\x39\144\x62\x62\x63", "\x33\x39\65\x36\143\62\65\142\146\63\x34\x38\x62\x35\x33\x38", "\x35\71\x66\61\x31\x31\x66\61\x62\66\x30\65\x64\60\61\71", "\x39\62\x33\146\70\62\x61\x34\141\x66\x31\71\x34\146\x39\142", "\x61\142\x31\143\x35\145\144\x35\x64\x61\x36\x64\x38\61\x31\70", "\x64\x38\60\67\141\x61\71\x38\141\63\x30\63\60\62\64\x32", "\x31\62\x38\63\x35\x62\x30\x31\x34\65\x37\x30\66\x66\142\145", "\62\64\x33\x31\x38\x35\x62\x65\64\x65\x65\64\142\x32\70\143", "\x35\x35\60\x63\67\x64\x63\x33\144\x35\x66\x66\142\64\145\62", "\67\x32\x62\x65\65\x64\x37\x34\146\x32\67\x62\70\x39\x36\146", "\x38\x30\x64\145\x62\x31\x66\145\x33\142\x31\66\71\x36\x62\x31", "\x39\x62\x64\x63\x30\66\141\x37\62\65\143\67\61\x32\63\65", "\x63\61\71\142\146\61\67\x34\143\146\66\x39\x32\x36\71\64", "\x65\64\x39\x62\x36\x39\x63\61\71\145\x66\x31\64\141\144\62", "\x65\x66\142\x65\x34\67\70\x36\x33\x38\64\x66\62\65\x65\x33", "\x30\146\x63\x31\x39\144\x63\66\70\x62\x38\x63\x64\x35\142\x35", "\x32\64\60\x63\141\61\143\143\x37\67\x61\143\71\143\x36\65", "\x32\x64\x65\x39\62\143\x36\x66\65\x39\62\142\x30\x32\x37\65", "\64\x61\67\64\x38\64\x61\x61\x36\x65\x61\x36\145\64\70\x33", "\65\x63\142\60\x61\71\x64\143\142\144\64\x31\146\142\144\64", "\67\66\x66\71\x38\70\144\141\x38\63\x31\61\65\63\142\65", "\71\70\x33\x65\x35\61\65\x32\x65\145\x36\66\144\146\x61\142", "\141\x38\63\x31\143\66\66\144\62\144\142\64\63\x32\61\60", "\142\60\60\63\62\x37\x63\70\x39\70\x66\142\x32\x31\63\x66", "\x62\146\x35\71\x37\x66\143\x37\x62\145\145\146\x30\145\145\64", "\x63\x36\x65\60\x30\x62\146\63\x33\x64\141\x38\70\x66\x63\x32", "\x64\65\x61\67\71\x31\64\x37\71\63\60\141\x61\67\62\65", "\60\x36\x63\x61\x36\63\x35\61\x65\x30\60\x33\70\x32\66\146", "\61\64\62\x39\x32\x39\66\67\60\x61\60\x65\x36\x65\x37\x30", "\x32\67\142\67\x30\x61\70\65\x34\66\144\62\x32\146\x66\x63", "\x32\145\61\142\62\61\63\x38\65\x63\x32\66\x63\x39\62\x36", "\x34\x64\x32\143\x36\x64\x66\x63\65\x61\143\x34\x32\x61\x65\144", "\x35\63\63\x38\60\144\x31\x33\x39\144\71\65\142\x33\144\146", "\66\x35\60\141\x37\x33\65\x34\70\142\141\146\66\63\144\x65", "\x37\66\66\141\x30\141\x62\x62\x33\143\67\x37\142\62\x61\70", "\70\x31\143\62\143\x39\x32\145\64\67\x65\144\141\145\145\66", "\x39\x32\x37\x32\62\x63\x38\x35\61\x34\70\62\63\x35\x33\x62", "\x61\x32\x62\x66\x65\70\x61\61\x34\143\146\x31\x30\63\x36\x34", "\x61\70\x31\x61\x36\x36\x34\142\142\143\x34\62\63\60\x30\61", "\x63\x32\x34\142\x38\x62\67\x30\144\60\146\70\71\x37\71\x31", "\x63\67\66\x63\x35\61\x61\63\60\66\65\x34\142\145\x33\x30", "\x64\x31\x39\x32\x65\x38\x31\71\x64\66\145\x66\65\62\61\x38", "\x64\x36\x39\71\60\66\62\64\x35\x35\66\65\141\x39\61\x30", "\146\64\60\145\x33\x35\70\65\65\x37\x37\61\62\x30\x32\x61", "\x31\x30\x36\141\x61\x30\67\60\63\62\x62\x62\144\x31\142\70", "\61\x39\x61\x34\x63\61\x31\66\x62\70\x64\62\144\60\x63\x38", "\61\145\x33\x37\66\x63\60\70\65\61\x34\x31\x61\x62\x35\63", "\x32\67\x34\70\x37\67\64\x63\x64\146\x38\145\x65\x62\71\71", "\63\64\x62\x30\x62\143\142\65\145\x31\x39\142\x34\70\141\x38", "\x33\x39\61\x63\x30\x63\142\63\143\x35\143\71\x35\x61\x36\x33", "\64\x65\144\x38\x61\141\x34\141\145\x33\64\61\70\141\x63\142", "\65\142\x39\x63\143\x61\x34\x66\x37\x37\66\x33\x65\x33\x37\x33", "\66\x38\62\x65\x36\146\x66\63\x64\66\142\62\142\70\141\x33", "\x37\x34\70\146\70\x32\x65\x65\x35\144\145\146\142\62\x66\x63", "\x37\70\141\65\66\x33\x36\x66\64\x33\x31\x37\62\146\66\x30", "\x38\64\143\x38\x37\x38\x31\x34\x61\x31\146\60\x61\x62\x37\62", "\x38\x63\x63\x37\60\62\60\x38\x31\x61\66\x34\x33\71\x65\x63", "\x39\x30\x62\x65\146\x66\146\141\x32\63\x36\x33\x31\145\62\x38", "\141\x34\65\60\66\x63\145\142\x64\x65\x38\x32\142\x64\145\71", "\x62\x65\146\71\x61\x33\146\67\x62\62\x63\66\67\71\x31\x35", "\143\66\67\x31\x37\70\146\x32\145\63\67\x32\x35\63\62\x62", "\x63\141\62\67\63\145\x63\145\145\x61\62\66\66\x31\71\x63", "\x64\61\70\x36\x62\70\x63\x37\62\x31\x63\x30\143\62\60\67", "\x65\x61\x64\x61\67\x64\144\x36\143\x64\x65\x30\145\142\61\x65", "\x66\65\x37\x64\x34\x66\67\x66\145\x65\x36\145\144\61\67\70", "\x30\x36\146\x30\x36\x37\x61\x61\67\x32\x31\x37\x36\146\x62\x61", "\x30\141\x36\x33\x37\x64\143\x35\141\62\x63\x38\x39\70\141\x36", "\x31\x31\x33\x66\x39\x38\60\64\x62\x65\x66\71\x30\144\x61\x65", "\x31\142\x37\61\60\142\63\x35\61\63\61\143\x34\67\x31\x62", "\62\x38\x64\142\67\67\x66\x35\62\63\60\x34\67\144\70\64", "\x33\x32\x63\x61\x61\142\67\x62\64\60\x63\67\62\64\x39\x33", "\63\143\x39\x65\x62\145\60\141\61\65\143\71\142\145\142\143", "\x34\x33\x31\x64\66\67\x63\64\71\143\61\60\x30\144\64\x63", "\x34\x63\143\65\144\64\142\145\143\x62\63\145\x34\62\142\x36", "\x35\x39\x37\x66\62\71\71\x63\x66\143\66\x35\x37\x65\62\x61", "\x35\146\x63\142\x36\x66\141\x62\63\141\x64\66\x66\141\x65\143", "\x36\x63\x34\64\x31\x39\x38\143\64\141\64\x37\x35\x38\61\67");
        $wz = 0;
        r7n:
        if (!($wz < 80)) {
            goto tsf;
        }
        $lm[$wz] = new Math_BigInteger($lm[$wz], 16);
        S_N:
        $wz++;
        goto r7n;
        tsf:
        A_q:
        $cA = $this->l == 48 ? $VD : $ZT;
        $ql = strlen($x4);
        $x4 .= str_repeat(chr(0), 128 - ($ql + 16 & 0x7f));
        $x4[$ql] = chr(0x80);
        $x4 .= pack("\116\64", 0, 0, 0, $ql << 3);
        $oT = str_split($x4, 128);
        foreach ($oT as $uC) {
            $ya = array();
            $wz = 0;
            N76:
            if (!($wz < 16)) {
                goto mig;
            }
            $gu = new Math_BigInteger($this->_string_shift($uC, 8), 256);
            $gu->setPrecision(64);
            $ya[] = $gu;
            jXH:
            $wz++;
            goto N76;
            mig:
            $wz = 16;
            lIQ:
            if (!($wz < 80)) {
                goto ufp;
            }
            $gu = array($ya[$wz - 15]->bitwise_rightRotate(1), $ya[$wz - 15]->bitwise_rightRotate(8), $ya[$wz - 15]->bitwise_rightShift(7));
            $di = $gu[0]->bitwise_xor($gu[1]);
            $di = $di->bitwise_xor($gu[2]);
            $gu = array($ya[$wz - 2]->bitwise_rightRotate(19), $ya[$wz - 2]->bitwise_rightRotate(61), $ya[$wz - 2]->bitwise_rightShift(6));
            $RJ = $gu[0]->bitwise_xor($gu[1]);
            $RJ = $RJ->bitwise_xor($gu[2]);
            $ya[$wz] = $ya[$wz - 16]->copy();
            $ya[$wz] = $ya[$wz]->add($di);
            $ya[$wz] = $ya[$wz]->add($ya[$wz - 7]);
            $ya[$wz] = $ya[$wz]->add($RJ);
            t4b:
            $wz++;
            goto lIQ;
            ufp:
            $aI = $cA[0]->copy();
            $E8 = $cA[1]->copy();
            $K6 = $cA[2]->copy();
            $XN = $cA[3]->copy();
            $tS = $cA[4]->copy();
            $xa = $cA[5]->copy();
            $Nw = $cA[6]->copy();
            $ce = $cA[7]->copy();
            $wz = 0;
            rmG:
            if (!($wz < 80)) {
                goto JoW;
            }
            $gu = array($aI->bitwise_rightRotate(28), $aI->bitwise_rightRotate(34), $aI->bitwise_rightRotate(39));
            $di = $gu[0]->bitwise_xor($gu[1]);
            $di = $di->bitwise_xor($gu[2]);
            $gu = array($aI->bitwise_and($E8), $aI->bitwise_and($K6), $E8->bitwise_and($K6));
            $mM = $gu[0]->bitwise_xor($gu[1]);
            $mM = $mM->bitwise_xor($gu[2]);
            $kR = $di->add($mM);
            $gu = array($tS->bitwise_rightRotate(14), $tS->bitwise_rightRotate(18), $tS->bitwise_rightRotate(41));
            $RJ = $gu[0]->bitwise_xor($gu[1]);
            $RJ = $RJ->bitwise_xor($gu[2]);
            $gu = array($tS->bitwise_and($xa), $Nw->bitwise_and($tS->bitwise_not()));
            $oK = $gu[0]->bitwise_xor($gu[1]);
            $YS = $ce->add($RJ);
            $YS = $YS->add($oK);
            $YS = $YS->add($lm[$wz]);
            $YS = $YS->add($ya[$wz]);
            $ce = $Nw->copy();
            $Nw = $xa->copy();
            $xa = $tS->copy();
            $tS = $XN->add($YS);
            $XN = $K6->copy();
            $K6 = $E8->copy();
            $E8 = $aI->copy();
            $aI = $YS->add($kR);
            Ig3:
            $wz++;
            goto rmG;
            JoW:
            $cA = array($cA[0]->add($aI), $cA[1]->add($E8), $cA[2]->add($K6), $cA[3]->add($XN), $cA[4]->add($tS), $cA[5]->add($xa), $cA[6]->add($Nw), $cA[7]->add($ce));
            Z9h:
        }
        ZxX:
        $gu = $cA[0]->toBytes() . $cA[1]->toBytes() . $cA[2]->toBytes() . $cA[3]->toBytes() . $cA[4]->toBytes() . $cA[5]->toBytes();
        if (!($this->l != 48)) {
            goto AW1;
        }
        $gu .= $cA[6]->toBytes() . $cA[7]->toBytes();
        AW1:
        return $gu;
    }
    function _rightRotate($gx, $qn)
    {
        $ae = 32 - $qn;
        $hM = (1 << $ae) - 1;
        return $gx << $ae & 0xffffffff | $gx >> $qn & $hM;
    }
    function _rightShift($gx, $qn)
    {
        $hM = (1 << 32 - $qn) - 1;
        return $gx >> $qn & $hM;
    }
    function _not($gx)
    {
        return ~$gx & 0xffffffff;
    }
    function _add()
    {
        static $Di;
        if (isset($Di)) {
            goto sxC;
        }
        $Di = pow(2, 32);
        sxC:
        $NU = 0;
        $a1 = func_get_args();
        foreach ($a1 as $Ry) {
            $NU += $Ry < 0 ? ($Ry & 0x7fffffff) + 0x80000000 : $Ry;
            lpv:
        }
        D_t:
        switch (true) {
            case is_int($NU):
            case version_compare(PHP_VERSION, "\65\56\63\x2e\60") >= 0 && (php_uname("\x6d") & "\337\xdf\337") != "\x41\122\x4d":
            case (PHP_OS & "\337\337\337") === "\x57\111\x4e":
                return fmod($NU, $Di);
        }
        Q1l:
        tKo:
        return fmod($NU, 0x80000000) & 0x7fffffff | (fmod(floor($NU / 0x80000000), 2) & 1) << 31;
    }
    function _string_shift(&$JK, $bu = 1)
    {
        $KZ = substr($JK, 0, $bu);
        $JK = substr($JK, $bu);
        return $KZ;
    }
}
