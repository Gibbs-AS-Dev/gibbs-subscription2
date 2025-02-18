<?php


namespace MoOauthClient\GrantTypes;

if (!function_exists("\x63\x72\x79\160\x74\137\x72\141\156\144\x6f\155\137\163\164\162\x69\156\147")) {
    if (defined("\x43\x52\131\x50\x54\137\x52\101\116\104\117\x4d\137\111\123\137\x57\x49\x4e\x44\117\127\x53")) {
        goto mBe;
    }
    define("\x43\x52\x59\x50\x54\x5f\122\101\x4e\x44\x4f\x4d\137\111\123\x5f\x57\x49\x4e\104\x4f\x57\x53", strtoupper(substr(PHP_OS, 0, 3)) === "\127\111\x4e");
    mBe:
    function crypt_random_string($ql)
    {
        if ($ql) {
            goto Bnn;
        }
        return '';
        Bnn:
        if (CRYPT_RANDOM_IS_WINDOWS) {
            goto b1G;
        }
        if (!(extension_loaded("\157\x70\145\156\163\x73\x6c") && version_compare(PHP_VERSION, "\65\56\x33\56\x30", "\x3e\75"))) {
            goto JcH;
        }
        return openssl_random_pseudo_bytes($ql);
        JcH:
        static $YA = true;
        if (!($YA === true)) {
            goto DCZ;
        }
        $YA = @fopen("\x2f\x64\145\x76\x2f\165\162\x61\156\x64\157\x6d", "\162\x62");
        DCZ:
        if (!($YA !== true && $YA !== false)) {
            goto w5V;
        }
        return fread($YA, $ql);
        w5V:
        if (!extension_loaded("\x6d\143\x72\x79\160\164")) {
            goto rNo;
        }
        return @mcrypt_create_iv($ql, MCRYPT_DEV_URANDOM);
        rNo:
        goto B2M;
        b1G:
        if (!(extension_loaded("\155\x63\162\171\160\164") && version_compare(PHP_VERSION, "\x35\56\63\56\x30", "\x3e\75"))) {
            goto vX0;
        }
        return @mcrypt_create_iv($ql);
        vX0:
        if (!(extension_loaded("\x6f\160\x65\x6e\163\x73\x6c") && version_compare(PHP_VERSION, "\65\x2e\63\56\64", "\x3e\75"))) {
            goto F74;
        }
        return openssl_random_pseudo_bytes($ql);
        F74:
        B2M:
        static $eE = false, $ZI;
        if (!($eE === false)) {
            goto nyB;
        }
        $VO = session_id();
        $yl = ini_get("\x73\145\x73\163\151\157\156\x2e\165\x73\145\x5f\143\157\x6f\x6b\151\145\x73");
        $YY = session_cache_limiter();
        $kj = isset($_SESSION) ? $_SESSION : false;
        if (!($VO != '')) {
            goto NHs;
        }
        session_write_close();
        NHs:
        session_id(1);
        ini_set("\163\x65\x73\163\x69\157\x6e\x2e\165\163\x65\137\x63\x6f\x6f\x6b\x69\x65\x73", 0);
        session_cache_limiter('');
        session_start(["\162\x65\141\144\137\141\156\144\137\143\154\x6f\x73\145" => true]);
        $ZI = $VZ = $_SESSION["\x73\x65\x65\x64"] = pack("\x48\52", sha1((isset($_SERVER) ? phpseclib_safe_serialize($_SERVER) : '') . (isset($_POST) ? phpseclib_safe_serialize($_POST) : '') . (isset($_GET) ? phpseclib_safe_serialize($_GET) : '') . (isset($_COOKIE) ? phpseclib_safe_serialize($_COOKIE) : '') . phpseclib_safe_serialize($GLOBALS) . phpseclib_safe_serialize($_SESSION) . phpseclib_safe_serialize($kj)));
        if (isset($_SESSION["\x63\x6f\x75\x6e\x74"])) {
            goto ssz;
        }
        $_SESSION["\x63\157\165\156\164"] = 0;
        ssz:
        $_SESSION["\x63\x6f\165\x6e\x74"]++;
        session_write_close();
        if ($VO != '') {
            goto Nx3;
        }
        if ($kj !== false) {
            goto SE2;
        }
        unset($_SESSION);
        goto s1j;
        SE2:
        $_SESSION = $kj;
        unset($kj);
        s1j:
        goto KOI;
        Nx3:
        session_id($VO);
        session_start(["\162\145\x61\144\x5f\x61\156\x64\x5f\x63\x6c\x6f\163\145" => true]);
        ini_set("\163\145\163\x73\x69\x6f\x6e\x2e\165\x73\x65\x5f\143\157\157\153\151\x65\163", $yl);
        session_cache_limiter($YY);
        KOI:
        $NZ = pack("\x48\x2a", sha1($VZ . "\x41"));
        $e3 = pack("\110\x2a", sha1($VZ . "\103"));
        switch (true) {
            case phpseclib_resolve_include_path("\x43\x72\171\x70\164\57\101\105\123\x2e\160\x68\x70"):
                if (class_exists("\x43\162\171\160\x74\137\x41\105\123")) {
                    goto Q20;
                }
                include_once "\101\x45\123\56\160\150\x70";
                Q20:
                $eE = new Crypt_AES(CRYPT_AES_MODE_CTR);
                goto IUz;
            case phpseclib_resolve_include_path("\103\x72\171\160\164\57\124\x77\157\146\151\x73\150\56\x70\x68\160"):
                if (class_exists("\103\162\171\x70\x74\137\124\167\157\x66\x69\x73\150")) {
                    goto U85;
                }
                include_once "\x54\167\x6f\x66\151\x73\x68\56\160\x68\160";
                U85:
                $eE = new Crypt_Twofish(CRYPT_TWOFISH_MODE_CTR);
                goto IUz;
            case phpseclib_resolve_include_path("\103\x72\171\160\x74\x2f\x42\x6c\157\167\x66\151\x73\150\x2e\x70\150\x70"):
                if (class_exists("\103\x72\171\160\164\137\x42\x6c\x6f\167\x66\x69\163\150")) {
                    goto Fzv;
                }
                include_once "\x42\154\x6f\x77\146\151\x73\150\56\160\x68\x70";
                Fzv:
                $eE = new Crypt_Blowfish(CRYPT_BLOWFISH_MODE_CTR);
                goto IUz;
            case phpseclib_resolve_include_path("\x43\x72\x79\x70\164\57\x54\162\x69\x70\154\x65\104\105\x53\56\160\150\x70"):
                if (class_exists("\103\162\x79\160\164\137\x54\x72\151\160\154\x65\x44\x45\x53")) {
                    goto Iaq;
                }
                include_once "\x54\162\x69\x70\x6c\x65\x44\105\123\56\x70\x68\160";
                Iaq:
                $eE = new Crypt_TripleDES(CRYPT_DES_MODE_CTR);
                goto IUz;
            case phpseclib_resolve_include_path("\103\x72\171\160\x74\x2f\x44\105\x53\56\160\x68\x70"):
                if (class_exists("\x43\162\171\160\164\137\104\x45\x53")) {
                    goto C4P;
                }
                include_once "\104\x45\123\x2e\160\150\160";
                C4P:
                $eE = new Crypt_DES(CRYPT_DES_MODE_CTR);
                goto IUz;
            case phpseclib_resolve_include_path("\x43\x72\171\x70\x74\57\122\103\x34\56\160\x68\160"):
                if (class_exists("\x43\162\171\160\164\137\x52\103\x34")) {
                    goto UBd;
                }
                include_once "\122\103\x34\x2e\x70\x68\160";
                UBd:
                $eE = new Crypt_RC4();
                goto IUz;
            default:
                user_error("\143\x72\171\160\164\137\x72\x61\x6e\x64\x6f\155\x5f\x73\164\x72\x69\156\x67\x20\x72\145\161\x75\151\162\x65\163\x20\141\x74\40\154\145\141\163\164\x20\x6f\x6e\x65\x20\163\x79\x6d\155\145\x74\162\x69\x63\x20\143\x69\x70\x68\145\162\x20\x62\x65\x20\x6c\x6f\x61\144\x65\144");
                return false;
        }
        Zi4:
        IUz:
        $eE->setKey($NZ);
        $eE->setIV($e3);
        $eE->enableContinuousBuffer();
        nyB:
        $NU = '';
        I9x:
        if (!(strlen($NU) < $ql)) {
            goto Za5;
        }
        $wz = $eE->encrypt(microtime());
        $AL = $eE->encrypt($wz ^ $ZI);
        $ZI = $eE->encrypt($AL ^ $wz);
        $NU .= $AL;
        goto I9x;
        Za5:
        return substr($NU, 0, $ql);
    }
}
if (!function_exists("\x70\x68\160\163\145\x63\154\151\142\x5f\x73\141\146\x65\137\163\x65\x72\x69\x61\x6c\x69\172\145")) {
    function phpseclib_safe_serialize(&$j8)
    {
        if (!is_object($j8)) {
            goto U93;
        }
        return '';
        U93:
        if (is_array($j8)) {
            goto VnI;
        }
        return serialize($j8);
        VnI:
        if (!isset($j8["\137\137\x70\150\x70\x73\145\x63\154\151\x62\137\155\141\162\x6b\145\162"])) {
            goto dls;
        }
        return '';
        dls:
        $xv = array();
        $j8["\x5f\x5f\x70\x68\x70\163\x65\143\x6c\151\142\137\155\141\x72\x6b\145\162"] = true;
        foreach (array_keys($j8) as $NZ) {
            if (!($NZ !== "\137\137\160\150\x70\x73\x65\143\x6c\151\142\x5f\155\x61\x72\153\x65\162")) {
                goto bV5;
            }
            $xv[$NZ] = phpseclib_safe_serialize($j8[$NZ]);
            bV5:
            xwP:
        }
        DNZ:
        unset($j8["\137\x5f\x70\x68\x70\x73\x65\x63\x6c\x69\x62\137\x6d\x61\x72\153\x65\162"]);
        return serialize($xv);
    }
}
if (!function_exists("\x70\150\x70\163\145\x63\154\151\x62\x5f\x72\145\x73\x6f\154\166\145\137\x69\156\x63\154\165\144\x65\137\160\x61\x74\150")) {
    function phpseclib_resolve_include_path($DW)
    {
        if (!function_exists("\x73\164\162\145\141\155\x5f\162\145\x73\x6f\154\x76\145\x5f\x69\156\143\154\x75\x64\145\137\x70\x61\x74\150")) {
            goto KZD;
        }
        return stream_resolve_include_path($DW);
        KZD:
        if (!file_exists($DW)) {
            goto xXM;
        }
        return realpath($DW);
        xXM:
        $HJ = PATH_SEPARATOR == "\72" ? preg_split("\x23\x28\x3f\74\x21\x70\x68\x61\x72\51\72\43", get_include_path()) : explode(PATH_SEPARATOR, get_include_path());
        foreach ($HJ as $gM) {
            $nY = substr($gM, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR;
            $sk = $gM . $nY . $DW;
            if (!file_exists($sk)) {
                goto TUp;
            }
            return realpath($sk);
            TUp:
            THk:
        }
        OcG:
        return false;
    }
}
