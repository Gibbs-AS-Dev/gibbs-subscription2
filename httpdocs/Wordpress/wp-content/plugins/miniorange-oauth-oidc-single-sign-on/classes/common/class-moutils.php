<?php


namespace MoOauthClient;

use MoOauthClient\App;
use MoOauthClient\Backup\EnvVarResolver;
class MOUtils
{
    const FREE = 0;
    const STANDARD = 1;
    const PREMIUM = 2;
    const MULTISITE_PREMIUM = 3;
    const ENTERPRISE = 4;
    const ALL_INCLUSIVE_SINGLE_SITE = 5;
    const MULTISITE_ENTERPRISE = 6;
    const ALL_INCLUSIVE_MULTISITE = 7;
    private $is_multisite = false;
    public function __construct()
    {
        remove_action("\141\144\155\x69\156\x5f\x6e\x6f\164\x69\143\145\x73", array($this, "\x6d\157\137\x6f\141\x75\164\x68\x5f\x73\x75\143\143\x65\x73\x73\137\x6d\145\x73\163\141\147\145"));
        remove_action("\x61\x64\x6d\x69\x6e\x5f\x6e\x6f\x74\x69\143\x65\x73", array($this, "\x6d\157\x5f\157\x61\x75\164\150\x5f\145\x72\162\x6f\162\x5f\x6d\145\x73\x73\141\x67\145"));
        $this->is_multisite = boolval(get_site_option("\x6d\157\x5f\x6f\x61\165\x74\150\x5f\x69\x73\x4d\x75\x6c\164\151\x53\151\164\x65\x50\154\x75\x67\151\156\122\145\161\165\145\x73\164\145\x64")) ? true : ($this->is_multisite_versi() ? true : false);
    }
    public function mo_oauth_success_message()
    {
        $q5 = "\145\162\162\157\162";
        $Kv = $this->mo_oauth_client_get_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION);
        echo "\74\x64\x69\x76\40\x63\154\x61\163\163\75\x27" . $q5 . "\47\76\40\x3c\160\76" . $Kv . "\x3c\57\x70\76\x3c\57\x64\x69\166\76";
    }
    public function mo_oauth_error_message()
    {
        $q5 = "\x75\x70\x64\141\164\x65\144";
        $Kv = $this->mo_oauth_client_get_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION);
        echo "\74\144\151\x76\40\x63\x6c\x61\x73\163\x3d\47" . $q5 . "\x27\x3e\x3c\160\x3e" . $Kv . "\x3c\57\x70\x3e\74\57\x64\151\166\76";
    }
    public function mo_oauth_show_success_message()
    {
        $yM = is_multisite() && $this->is_multisite_versi() ? "\156\x65\164\x77\x6f\x72\153\x5f" : '';
        remove_action("{$yM}\x61\144\x6d\151\156\137\156\x6f\x74\x69\x63\x65\163", array($this, "\155\x6f\137\157\141\x75\x74\x68\x5f\x73\x75\143\x63\x65\x73\x73\137\x6d\x65\x73\x73\x61\x67\x65"));
        add_action("{$yM}\x61\x64\155\151\x6e\x5f\x6e\157\164\x69\143\145\x73", array($this, "\155\x6f\x5f\x6f\x61\165\x74\150\x5f\145\162\162\x6f\162\137\x6d\x65\x73\163\141\x67\145"));
    }
    public function mo_oauth_show_error_message()
    {
        $yM = is_multisite() && $this->is_multisite_versi() ? "\x6e\x65\x74\167\157\x72\153\137" : '';
        remove_action("{$yM}\x61\144\155\x69\x6e\137\x6e\x6f\164\x69\143\x65\x73", array($this, "\155\x6f\137\x6f\x61\x75\x74\150\x5f\x65\x72\x72\157\162\137\155\x65\163\163\141\147\x65"));
        add_action("{$yM}\141\144\x6d\151\x6e\137\x6e\157\164\151\x63\x65\x73", array($this, "\x6d\x6f\x5f\x6f\x61\165\164\150\137\x73\165\x63\143\x65\163\163\137\x6d\145\163\x73\141\x67\x65"));
    }
    public function mo_oauth_is_customer_registered()
    {
        $UU = $this->mo_oauth_client_get_option("\155\157\137\x6f\x61\x75\164\x68\137\x61\144\155\151\x6e\137\x65\x6d\x61\151\x6c");
        $Y5 = $this->mo_oauth_client_get_option("\155\x6f\137\x6f\141\x75\164\x68\137\141\144\155\x69\x6e\x5f\143\x75\x73\164\x6f\x6d\145\x72\137\153\145\x79");
        if (!$UU || !$Y5 || !is_numeric(trim($Y5))) {
            goto EE;
        }
        return 1;
        goto cJ;
        EE:
        return 0;
        cJ:
    }
    public function mooauthencrypt($MQ)
    {
        $BZ = $this->mo_oauth_client_get_option("\143\165\x73\x74\x6f\x6d\145\162\x5f\164\157\x6b\x65\156");
        if ($BZ) {
            goto r4;
        }
        return "\146\x61\x6c\x73\145";
        r4:
        $BZ = str_split(str_pad('', strlen($MQ), $BZ, STR_PAD_RIGHT));
        $ha = str_split($MQ);
        foreach ($ha as $lm => $ZI) {
            $Bh = ord($ZI) + ord($BZ[$lm]);
            $ha[$lm] = chr($Bh > 255 ? $Bh - 256 : $Bh);
            FS:
        }
        B3:
        return base64_encode(join('', $ha));
    }
    public function mooauthdecrypt($MQ)
    {
        $MQ = base64_decode($MQ);
        $BZ = $this->mo_oauth_client_get_option("\x63\x75\163\x74\157\155\x65\x72\137\x74\157\153\145\156");
        if ($BZ) {
            goto DL;
        }
        return "\146\141\x6c\163\x65";
        DL:
        $BZ = str_split(str_pad('', strlen($MQ), $BZ, STR_PAD_RIGHT));
        $ha = str_split($MQ);
        foreach ($ha as $lm => $ZI) {
            $Bh = ord($ZI) - ord($BZ[$lm]);
            $ha[$lm] = chr($Bh < 0 ? $Bh + 256 : $Bh);
            SR:
        }
        jc:
        return join('', $ha);
    }
    public function mo_oauth_check_empty_or_null($mB)
    {
        if (!(!isset($mB) || empty($mB))) {
            goto To;
        }
        return true;
        To:
        return false;
    }
    public function is_multisite_plan()
    {
        return $this->is_multisite;
    }
    public function mo_oauth_is_curl_installed()
    {
        if (in_array("\x63\x75\x72\x6c", get_loaded_extensions())) {
            goto F4;
        }
        return 0;
        goto XP;
        F4:
        return 1;
        XP:
    }
    public function mo_oauth_show_curl_error()
    {
        if (!($this->mo_oauth_is_curl_installed() === 0)) {
            goto LS;
        }
        $this->mo_oauth_client_update_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION, "\x3c\141\x20\x68\x72\145\146\x3d\x22\150\164\164\x70\x3a\57\57\160\x68\160\x2e\156\x65\x74\57\155\x61\x6e\165\x61\154\57\145\156\57\143\x75\162\x6c\56\x69\156\163\x74\141\154\x6c\141\x74\151\x6f\x6e\x2e\x70\150\160\42\40\x74\x61\162\x67\x65\x74\75\42\x5f\x62\154\141\156\153\x22\x3e\x50\x48\x50\x20\103\x55\122\114\x20\145\x78\164\x65\156\163\151\157\156\74\57\141\76\x20\151\163\40\x6e\x6f\x74\40\x69\x6e\x73\x74\141\x6c\x6c\x65\144\40\157\162\40\x64\151\x73\x61\x62\x6c\x65\144\x2e\x20\x50\154\x65\141\163\x65\40\x65\156\141\142\x6c\145\x20\151\164\x20\x74\x6f\x20\143\x6f\x6e\164\151\x6e\165\145\x2e");
        $this->mo_oauth_show_error_message();
        return;
        LS:
    }
    public function mo_oauth_is_clv()
    {
        $yW = $this->mo_oauth_client_get_option("\155\x6f\137\157\x61\x75\164\x68\x5f\154\x76");
        $yW = boolval($yW) ? $this->mooauthdecrypt($yW) : "\146\x61\x6c\x73\x65";
        $yW = !empty($this->mo_oauth_client_get_option("\155\157\137\x6f\x61\165\164\150\137\x6c\153")) && "\x74\x72\x75\145" === $yW ? 1 : 0;
        if (!($yW === 0)) {
            goto Ee;
        }
        return $this->verify_lk();
        Ee:
        return $yW;
    }
    public function mo_oauth_hbca_xyake()
    {
        if ($this->mo_oauth_is_customer_registered()) {
            goto DZ;
        }
        return false;
        DZ:
        if ($this->mo_oauth_client_get_option("\155\157\x5f\x6f\x61\165\164\x68\137\x61\x64\x6d\151\x6e\137\143\165\163\164\x6f\155\x65\162\x5f\153\145\x79") > 138200) {
            goto VI;
        }
        return false;
        goto qm;
        VI:
        return true;
        qm:
    }
    public function get_default_app($M2, $O3 = false)
    {
        if ($M2) {
            goto Dv;
        }
        return false;
        Dv:
        $RQ = false;
        $AG = file_get_contents(MOC_DIR . "\162\x65\163\x6f\x75\162\x63\145\x73\57\141\x70\160\x5f\143\157\x6d\x70\157\156\x65\x6e\164\163\57\144\x65\146\141\x75\x6c\x74\141\160\x70\x73\x2e\152\163\x6f\156", true);
        $hv = json_decode($AG, $O3);
        foreach ($hv as $rF => $K7) {
            if (!($rF === $M2)) {
                goto Ep;
            }
            if ($O3) {
                goto XJ;
            }
            $K7->appId = $rF;
            goto bP;
            XJ:
            $K7["\x61\160\160\111\x64"] = $rF;
            bP:
            return $K7;
            Ep:
            Id:
        }
        J7:
        return false;
    }
    public function get_plugin_config()
    {
        $n2 = $this->mo_oauth_client_get_option("\155\x6f\137\157\141\x75\x74\150\137\143\154\x69\145\x6e\164\x5f\143\157\x6e\x66\151\x67");
        return !$n2 || empty($n2) ? new Config(array()) : $n2;
    }
    public function get_app_list()
    {
        return $this->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\x75\x74\x68\137\x61\x70\160\163\x5f\x6c\151\x73\164") ? $this->mo_oauth_client_get_option("\155\x6f\137\157\141\165\164\150\x5f\141\160\x70\163\x5f\x6c\x69\163\x74") : false;
    }
    public function get_app_by_name($bj = '')
    {
        $FO = $this->get_app_list();
        if ($FO) {
            goto XB;
        }
        return false;
        XB:
        if (!('' === $bj || false === $bj)) {
            goto RQ;
        }
        $cN = array_values($FO);
        return isset($cN[0]) ? $cN[0] : false;
        RQ:
        foreach ($FO as $NZ => $Zy) {
            if (!($bj === $NZ)) {
                goto b2;
            }
            return $Zy;
            b2:
            if (!((int) $bj === $NZ)) {
                goto bj;
            }
            return $Zy;
            bj:
            hJ:
        }
        Bs:
        return false;
    }
    public function get_default_app_by_code_name($bj = '')
    {
        $FO = $this->mo_oauth_client_get_option("\x6d\157\x5f\157\x61\x75\164\150\x5f\141\160\160\x73\137\154\x69\x73\x74") ? $this->mo_oauth_client_get_option("\155\157\x5f\157\141\165\x74\x68\x5f\x61\x70\160\163\x5f\154\x69\x73\164") : false;
        if ($FO) {
            goto ho;
        }
        return false;
        ho:
        if (!('' === $bj)) {
            goto yp;
        }
        $cN = array_values($FO);
        return isset($cN[0]) ? $cN[0] : false;
        yp:
        foreach ($FO as $NZ => $Zy) {
            $t_ = $Zy->get_app_name();
            if (!($bj === $t_)) {
                goto sm;
            }
            return $this->get_default_app($Zy->get_app_config("\x61\x70\x70\137\164\171\x70\145"), true);
            sm:
            IQ:
        }
        lW:
        return false;
    }
    public function set_app_by_name($bj, $xA)
    {
        $FO = $this->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\x75\164\150\137\x61\160\160\x73\x5f\154\151\x73\x74") ? $this->mo_oauth_client_get_option("\155\x6f\x5f\x6f\141\x75\x74\x68\137\141\160\160\163\137\154\x69\x73\164") : false;
        if ($FO) {
            goto fG;
        }
        return false;
        fG:
        foreach ($FO as $NZ => $Zy) {
            if (!(gettype($NZ) === "\151\x6e\x74\145\147\x65\x72")) {
                goto YL;
            }
            $NZ = strval($NZ);
            YL:
            if (!($bj === $NZ)) {
                goto Ie;
            }
            $FO[$NZ] = new App($xA);
            $FO[$NZ]->set_app_name($NZ);
            $this->mo_oauth_client_update_option("\155\x6f\x5f\x6f\141\165\164\150\137\x61\x70\x70\x73\137\x6c\x69\163\x74", $FO);
            return true;
            Ie:
            je:
        }
        nh:
        return false;
    }
    public function mo_oauth_jhuyn_jgsukaj($gt, $uO)
    {
        return $this->mo_oauth_jkhuiysuayhbw($gt, $uO);
    }
    public function mo_oauth_jkhuiysuayhbw($IH, $tQ)
    {
        $MC = 0;
        $zw = false;
        $eG = $this->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\x75\164\150\137\141\165\x74\150\157\x72\151\x7a\x61\164\151\x6f\x6e\x73");
        if (empty($eG)) {
            goto J9;
        }
        $MC = $this->mo_oauth_client_get_option("\x6d\x6f\x5f\157\x61\165\x74\x68\137\x61\x75\164\150\x6f\x72\151\x7a\141\x74\x69\157\x6e\163");
        J9:
        $user = $this->mo_oauth_hjsguh_kiishuyauh878gs($IH, $tQ);
        if (!$user) {
            goto yW;
        }
        ++$MC;
        yW:
        $this->mo_oauth_client_update_option("\155\157\x5f\x6f\x61\165\x74\x68\137\x61\165\164\x68\x6f\x72\x69\172\141\164\x69\157\x6e\163", $MC);
        if (!($MC >= 10)) {
            goto KZ;
        }
        $cl = base64_decode("\x62\x57\71\x66\x62\62\106\x31\144\x47\x68\x66\132\155\x78\150\x5a\x77\75\75");
        $this->mo_oauth_client_update_option($cl, true);
        KZ:
        return $user;
    }
    public function mo_oauth_hjsguh_kiishuyauh878gs($UU, $O6)
    {
        $ql = 10;
        $az = false;
        $jj = false;
        $n2 = apply_filters("\155\157\137\x6f\141\165\x74\150\137\x70\x61\x73\x73\167\157\x72\x64\137\x70\157\154\x69\x63\171\137\155\141\x6e\141\147\x65\162", $ql);
        if (!is_array($n2)) {
            goto lj;
        }
        $ql = intval($n2["\160\x61\163\x73\167\157\162\x64\137\154\145\156\147\x74\x68"]);
        $az = $n2["\x73\160\145\143\x69\141\x6c\137\143\150\141\162\141\143\x74\x65\x72\x73"];
        $jj = $n2["\x65\170\164\x72\x61\137\163\160\x65\x63\151\141\x6c\137\143\150\x61\162\x61\143\x74\145\x72\163"];
        lj:
        $NO = wp_generate_password($ql, $az, $jj);
        $b2 = is_email($UU) ? wp_create_user($UU, $NO, $UU) : wp_create_user($UU, $NO);
        $eJ = array("\x49\x44" => $b2, "\165\x73\x65\162\x5f\x65\x6d\x61\151\x6c" => $UU, "\165\163\145\x72\x5f\x6c\x6f\147\x69\x6e" => $O6, "\x75\x73\145\162\137\x6e\x69\143\x65\x6e\x61\x6d\145" => $O6, "\146\x69\162\x73\x74\137\156\141\x6d\x65" => $O6);
        do_action("\x75\x73\145\162\137\162\145\x67\x69\x73\164\145\x72", $b2, $eJ);
        $user = get_user_by("\154\157\147\151\x6e", $UU);
        wp_update_user(array("\x49\x44" => $b2, "\x66\x69\x72\163\164\x5f\x6e\141\155\x65" => $O6));
        return $user;
    }
    public function check_versi($ug)
    {
        return $this->get_versi() >= $ug;
    }
    public function is_multisite_versi()
    {
        return $this->get_versi() >= 6 || $this->get_versi() == 3;
    }
    public function get_versi()
    {
        return VERSION === "\155\157\x5f\x6d\165\x6c\164\151\x73\x69\164\x65\137\141\x6c\154\x5f\151\x6e\143\x6c\x75\x73\151\166\145\x5f\x76\x65\162\163\x69\157\x6e" ? self::ALL_INCLUSIVE_MULTISITE : (VERSION === "\155\x6f\137\155\x75\x6c\x74\151\163\x69\x74\x65\x5f\x70\x72\145\x6d\x69\165\155\137\x76\145\x72\163\x69\157\156" ? self::MULTISITE_PREMIUM : (VERSION === "\155\x6f\137\x6d\x75\154\164\151\163\151\164\145\x5f\145\156\164\x65\162\160\162\151\163\145\x5f\166\145\x72\x73\x69\x6f\156" ? self::MULTISITE_ENTERPRISE : (VERSION === "\x6d\x6f\x5f\141\154\x6c\x5f\151\156\x63\x6c\165\x73\151\166\145\137\166\145\162\x73\151\x6f\156" ? self::ALL_INCLUSIVE_SINGLE_SITE : (VERSION === "\155\x6f\137\x65\156\x74\145\162\160\162\151\x73\x65\x5f\166\145\162\x73\x69\157\x6e" ? self::ENTERPRISE : (VERSION === "\155\157\x5f\x70\162\x65\x6d\151\165\x6d\x5f\x76\x65\162\x73\x69\157\x6e" ? self::PREMIUM : (VERSION === "\155\x6f\x5f\163\164\141\156\x64\x61\x72\x64\137\166\145\x72\x73\x69\x6f\156" ? self::STANDARD : self::FREE))))));
    }
    public function get_plan_type_versi()
    {
        switch ($this->get_versi()) {
            case self::ALL_INCLUSIVE_MULTISITE:
                return "\101\x4c\114\137\x49\x4e\x43\114\x55\123\x49\126\x45\x5f\115\x55\x4c\124\111\x53\x49\x54\x45";
            case self::MULTISITE_PREMIUM:
                return "\115\x55\x4c\x54\111\x53\x49\124\105\x5f\x50\122\x45\x4d\x49\125\x4d";
            case self::MULTISITE_ENTERPRISE:
                return "\115\125\x4c\x54\x49\123\111\124\x45\x5f\105\116\x54\x45\x52\120\122\111\123\105";
            case self::ALL_INCLUSIVE_SINGLE_SITE:
                return "\105\116\x54\105\122\x50\x52\x49\123\105";
            case self::ENTERPRISE:
                return "\x45\116\x54\105\122\120\122\x49\123\x45";
            case self::PREMIUM:
                return '';
            case self::STANDARD:
                return "\x53\x54\101\116\104\x41\x52\x44";
            case self::FREE:
            default:
                return "\x46\x52\105\105";
        }
        Me:
        da:
    }
    public function get_versi_str()
    {
        switch ($this->get_versi()) {
            case self::ALL_INCLUSIVE_MULTISITE:
                return "\x41\114\114\137\111\116\103\x4c\x55\x53\111\126\x45\137\x4d\x55\x4c\124\x49\123\x49\x54\105";
            case self::MULTISITE_PREMIUM:
                return "\x4d\125\114\x54\x49\x53\111\x54\105\137\x50\122\105\x4d\x49\125\x4d";
            case self::MULTISITE_ENTERPRISE:
                return "\115\x55\x4c\124\x49\123\x49\124\105\x5f\105\x4e\x54\x45\122\x50\122\x49\123\105";
            case self::ALL_INCLUSIVE_SINGLE_SITE:
                return "\101\x4c\114\137\111\116\x43\114\125\123\111\126\x45\x5f\123\111\x4e\x47\x4c\105\137\x53\111\x54\105";
            case self::ENTERPRISE:
                return "\105\116\x54\105\x52\120\x52\x49\x53\105";
            case self::PREMIUM:
                return "\120\x52\x45\115\111\125\x4d";
            case self::STANDARD:
                return "\123\124\101\116\104\x41\122\104";
            case self::FREE:
            default:
                return "\x46\x52\105\x45";
        }
        Ym:
        o3:
    }
    public function mo_oauth_client_get_option($NZ, $vJ = false)
    {
        $mB = getenv(strtoupper($NZ));
        if (!$mB) {
            goto G2;
        }
        $mB = EnvVarResolver::resolve_var($NZ, $mB);
        goto df;
        G2:
        $mB = is_multisite() && $this->is_multisite ? get_site_option($NZ, $vJ) : get_option($NZ, $vJ);
        df:
        if (!(!$mB || $vJ == $mB)) {
            goto ns;
        }
        return $vJ;
        ns:
        return $mB;
    }
    public function mo_oauth_client_update_option($NZ, $mB)
    {
        return is_multisite() && $this->is_multisite ? update_site_option($NZ, $mB) : update_option($NZ, $mB);
    }
    public function mo_oauth_client_delete_option($NZ)
    {
        return is_multisite() && $this->is_multisite ? delete_site_option($NZ) : delete_option($NZ);
    }
    public function array_overwrite($s2, $Mm, $LW)
    {
        if ($LW) {
            goto Dr;
        }
        array_push($s2, $Mm);
        return array_unique($s2);
        Dr:
        foreach ($Mm as $NZ => $mB) {
            $s2[$NZ] = $mB;
            zp:
        }
        Qb:
        return $s2;
    }
    public function gen_rand_str($ql = 10)
    {
        $Tw = "\141\142\x63\144\145\146\147\150\x69\x6a\153\154\155\x6e\x6f\x70\161\162\x73\x74\165\166\x77\170\x79\x7a\101\102\103\104\105\106\x47\x48\111\x4a\113\114\115\x4e\117\120\121\122\123\x54\125\x56\127\x58\131\132";
        $D6 = strlen($Tw);
        $I_ = '';
        $wz = 0;
        oa:
        if (!($wz < $ql)) {
            goto Mb;
        }
        $I_ .= $Tw[rand(0, $D6 - 1)];
        Ke:
        $wz++;
        goto oa;
        Mb:
        return $I_;
    }
    public function parse_url($QR)
    {
        $RQ = array();
        $Rs = explode("\77", $QR);
        $RQ["\150\157\163\164"] = $Rs[0];
        $RQ["\x71\165\145\162\x79"] = isset($Rs[1]) && '' !== $Rs[1] ? $Rs[1] : '';
        if (!(empty($RQ["\161\x75\x65\162\171"]) || '' === $RQ["\x71\165\145\x72\171"])) {
            goto EZ;
        }
        return $RQ;
        EZ:
        $TL = [];
        foreach (explode("\x26", $RQ["\x71\x75\145\x72\x79"]) as $Jy) {
            $Rs = explode("\75", $Jy);
            if (!(is_array($Rs) && count($Rs) === 2)) {
                goto Ej;
            }
            $TL[str_replace("\141\x6d\x70\x3b", '', $Rs[0])] = $Rs[1];
            Ej:
            if (!(is_array($Rs) && "\x73\164\x61\x74\x65" === $Rs[0])) {
                goto ZY;
            }
            $Rs = explode("\163\x74\141\x74\x65\75", $Jy);
            $TL["\163\x74\141\x74\145"] = $Rs[1];
            ZY:
            LD:
        }
        BM:
        $RQ["\x71\x75\145\162\x79"] = is_array($TL) && !empty($TL) ? $TL : [];
        return $RQ;
    }
    public function generate_url($wf)
    {
        if (!(!is_array($wf) || empty($wf))) {
            goto H3;
        }
        return '';
        H3:
        if (isset($wf["\x68\x6f\163\164"])) {
            goto vi;
        }
        return '';
        vi:
        $QR = $wf["\x68\157\163\x74"];
        $GO = '';
        $wz = 0;
        foreach ($wf["\x71\165\x65\x72\x79"] as $f_ => $mB) {
            if (!($wz !== 0)) {
                goto un;
            }
            $GO .= "\46";
            un:
            $GO .= "{$f_}\75{$mB}";
            $wz += 1;
            k1:
        }
        B7:
        return $QR . "\77" . $GO;
    }
    public function getnestedattribute($We, $NZ)
    {
        if (!($NZ == '')) {
            goto ZR;
        }
        return '';
        ZR:
        if (!filter_var($NZ, FILTER_VALIDATE_URL)) {
            goto rU;
        }
        if (isset($We[$NZ])) {
            goto dR;
        }
        return '';
        goto bD;
        dR:
        return $We[$NZ];
        bD:
        rU:
        $T3 = explode("\x2e", $NZ);
        if (count($T3) > 1) {
            goto ea;
        }
        if (isset($We[$NZ]) && is_array($We[$NZ])) {
            goto XG;
        }
        $I6 = $T3[0];
        if (isset($We[$I6])) {
            goto jb;
        }
        return '';
        goto xD;
        jb:
        if (is_array($We[$I6])) {
            goto rb;
        }
        return $We[$I6];
        goto Qz;
        rb:
        return $We[$I6][0];
        Qz:
        xD:
        goto zC;
        XG:
        if (!(count($We[$NZ]) > 1)) {
            goto pn;
        }
        return $We[$NZ];
        pn:
        if (!isset($We[$NZ][0])) {
            goto TX;
        }
        return $We[$NZ][0];
        TX:
        if (!is_array($We[$NZ])) {
            goto LV;
        }
        return array_key_first($We[$NZ]);
        LV:
        zC:
        goto Cs;
        ea:
        $I6 = $T3[0];
        if (!isset($We[$I6])) {
            goto MI;
        }
        $T0 = array_count_values($T3);
        if (!($T0[$I6] > 1)) {
            goto Du;
        }
        $NZ = substr_replace($NZ, '', 0, strlen($I6));
        $NZ = trim($NZ, "\56");
        return $this->getnestedattribute($We[$I6], $NZ);
        Du:
        return $this->getnestedattribute($We[$I6], str_replace($I6 . "\56", '', $NZ));
        MI:
        Cs:
    }
    public function get_client_ip()
    {
        $Hr = '';
        if (getenv("\x48\x54\x54\120\137\103\x4c\x49\x45\116\x54\x5f\111\x50")) {
            goto kK;
        }
        if (getenv("\110\x54\124\x50\x5f\130\x5f\106\x4f\x52\127\x41\x52\104\x45\104\137\x46\x4f\122")) {
            goto Lv;
        }
        if (getenv("\110\x54\124\x50\x5f\130\x5f\106\117\122\127\x41\122\x44\105\104")) {
            goto zu;
        }
        if (getenv("\x48\x54\124\x50\137\106\117\122\x57\x41\122\x44\105\x44\x5f\x46\x4f\x52")) {
            goto q2;
        }
        if (getenv("\x48\x54\124\120\x5f\x46\117\x52\x57\x41\x52\104\105\x44")) {
            goto HP;
        }
        if (getenv("\122\x45\115\x4f\x54\105\137\x41\x44\104\122")) {
            goto Zi;
        }
        $Hr = "\125\116\x4b\116\117\127\x4e";
        goto kd;
        kK:
        $Hr = getenv("\x48\x54\124\120\137\x43\114\111\x45\116\124\137\x49\120");
        goto kd;
        Lv:
        $Hr = getenv("\110\124\x54\x50\x5f\130\137\x46\117\122\x57\x41\x52\104\105\104\x5f\106\x4f\122");
        goto kd;
        zu:
        $Hr = getenv("\110\x54\124\x50\137\130\137\x46\x4f\x52\127\x41\x52\104\105\104");
        goto kd;
        q2:
        $Hr = getenv("\x48\124\x54\x50\137\106\x4f\x52\x57\101\122\x44\105\104\137\x46\x4f\122");
        goto kd;
        HP:
        $Hr = getenv("\110\x54\124\x50\x5f\x46\117\122\x57\x41\x52\x44\x45\x44");
        goto kd;
        Zi:
        $Hr = getenv("\x52\105\x4d\x4f\x54\105\x5f\x41\x44\104\x52");
        kd:
        return $Hr;
    }
    public function get_current_url()
    {
        return (isset($_SERVER["\110\x54\x54\x50\123"]) ? "\150\x74\x74\160\x73" : "\150\x74\x74\160") . "\x3a\x2f\x2f{$_SERVER["\x48\x54\x54\120\137\110\x4f\123\x54"]}{$_SERVER["\122\105\121\x55\x45\123\x54\x5f\125\x52\x49"]}";
    }
    public function get_all_headers()
    {
        $P_ = [];
        foreach ($_SERVER as $O6 => $mB) {
            if (!(substr($O6, 0, 5) == "\110\124\x54\x50\137")) {
                goto jW;
            }
            $P_[str_replace("\x20", "\55", ucwords(strtolower(str_replace("\137", "\x20", substr($O6, 5)))))] = $mB;
            jW:
            D7:
        }
        qs:
        $P_ = array_change_key_case($P_, CASE_UPPER);
        return $P_;
    }
    public function store_info($XL = '', $mB = false)
    {
        if (!('' === $XL || !$mB)) {
            goto sN;
        }
        return;
        sN:
        setcookie($XL, $mB);
    }
    public function redirect_user($QR = false, $LE = false)
    {
        if (!(false === $QR)) {
            goto Ky;
        }
        return;
        Ky:
        if (!$LE) {
            goto co;
        }
        echo "\x9\11\x9\x3c\x73\x63\162\151\x70\x74\76\15\12\x9\x9\x9\x9\166\x61\x72\40\155\x79\x57\x69\x6e\144\x6f\x77\x20\75\x20\x77\151\156\144\x6f\x77\56\x6f\x70\x65\156\x28\x22";
        echo $QR;
        echo "\42\54\40\42\124\x65\x73\164\40\x43\157\156\x66\x69\x67\165\x72\141\164\151\x6f\x6e\x22\54\x20\x22\x77\x69\x64\x74\x68\x3d\66\x30\x30\x2c\x20\150\x65\x69\147\150\x74\x3d\66\x30\60\42\51\x3b\15\xa\x9\11\11\x9\167\x68\x69\x6c\x65\50\x31\51\40\173\xd\12\x9\11\x9\x9\11\x69\x66\50\155\x79\127\x69\156\144\x6f\167\56\x63\x6c\x6f\x73\145\x64\50\x29\51\x20\x7b\15\xa\x9\11\11\x9\11\x9\44\50\x64\157\x63\165\x6d\145\x6e\x74\51\x2e\x74\162\x69\x67\x67\x65\162\x28\42\143\x6f\x6e\x66\151\147\137\x74\x65\x73\x74\145\x64\42\51\x3b\15\xa\11\11\11\x9\x9\11\x62\x72\145\x61\153\73\15\xa\x9\x9\11\x9\x9\175\40\x65\x6c\x73\145\x20\x7b\x63\157\x6e\164\x69\x6e\x75\x65\x3b\175\15\xa\x9\11\x9\x9\x7d\xd\xa\x9\11\x9\x3c\57\163\143\162\x69\160\164\x3e\15\12\x9\11\x9";
        co:
        echo "\11\11\74\x73\143\x72\151\160\x74\76\xd\xa\x9\x9\x9\167\151\156\x64\x6f\167\56\154\x6f\143\x61\164\x69\157\156\56\162\145\160\x6c\x61\143\145\50\x22";
        echo $QR;
        echo "\42\x29\x3b\xd\xa\x9\x9\74\57\x73\143\162\151\x70\164\76\xd\xa\11\11";
        exit;
    }
    public function is_ajax_request()
    {
        return defined("\x44\x4f\x49\116\107\137\101\x4a\x41\x58") && DOING_AJAX;
    }
    public function deactivate_plugin()
    {
        $this->mo_oauth_client_delete_option("\x68\157\x73\x74\x5f\x6e\141\x6d\x65");
        $this->mo_oauth_client_delete_option("\x6e\145\x77\137\162\x65\147\x69\163\x74\x72\x61\164\x69\x6f\x6e");
        $this->mo_oauth_client_delete_option("\x6d\x6f\137\x6f\x61\165\x74\x68\x5f\141\x64\x6d\x69\x6e\x5f\x70\x68\157\x6e\145");
        $this->mo_oauth_client_delete_option("\x76\145\162\x69\146\x79\137\x63\x75\x73\x74\x6f\155\x65\x72");
        $this->mo_oauth_client_delete_option("\x6d\x6f\x5f\x6f\x61\x75\x74\x68\137\x61\x64\155\x69\x6e\137\143\165\x73\x74\x6f\x6d\145\x72\137\x6b\145\171");
        $this->mo_oauth_client_delete_option("\x6d\x6f\x5f\157\141\165\164\150\137\x61\x64\x6d\x69\156\137\141\160\151\137\153\145\171");
        $this->mo_oauth_client_delete_option("\x6d\x6f\137\x6f\141\x75\164\x68\x5f\156\x65\x77\x5f\143\165\x73\164\x6f\x6d\145\x72");
        $this->mo_oauth_client_delete_option("\x63\165\x73\x74\157\155\x65\x72\137\164\157\x6b\x65\x6e");
        $this->mo_oauth_client_delete_option(\MoOAuthConstants::PANEL_MESSAGE_OPTION);
        $this->mo_oauth_client_delete_option("\155\x6f\137\x6f\x61\x75\164\x68\x5f\x72\145\147\151\163\x74\162\141\x74\151\157\156\137\x73\164\141\164\165\x73");
        $this->mo_oauth_client_delete_option("\x6d\157\x5f\x6f\x61\165\x74\150\137\x6e\x65\x77\137\x63\x75\x73\x74\x6f\155\145\162");
        $this->mo_oauth_client_delete_option("\156\145\167\137\x72\145\x67\x69\163\164\x72\141\164\x69\x6f\x6e");
        $this->mo_oauth_client_delete_option("\x6d\x6f\137\x6f\141\x75\x74\x68\x5f\x6c\157\x67\151\156\x5f\x69\x63\x6f\156\137\143\x75\x73\x74\157\155\x5f\x68\x65\151\147\x68\x74");
        $this->mo_oauth_client_delete_option("\155\x6f\137\x6f\x61\165\x74\150\137\154\x6f\147\151\156\137\x69\x63\x6f\156\137\x63\165\x73\x74\157\155\137\x73\151\x7a\x65");
        $this->mo_oauth_client_delete_option("\x6d\157\x5f\157\141\x75\164\150\x5f\154\x6f\x67\x69\156\x5f\151\143\157\156\137\143\165\163\164\157\x6d\137\x63\157\x6c\x6f\x72");
        $this->mo_oauth_client_delete_option("\155\157\137\157\x61\165\164\x68\x5f\x6c\x6f\147\151\156\x5f\x69\x63\x6f\x6e\x5f\143\165\163\164\x6f\x6d\137\x62\x6f\x75\x6e\144\x61\162\171");
    }
    public function base64url_encode($s8)
    {
        return rtrim(strtr(base64_encode($s8), "\x2b\x2f", "\x2d\x5f"), "\75");
    }
    public function base64url_decode($s8)
    {
        return base64_decode(str_pad(strtr($s8, "\55\x5f", "\53\x2f"), strlen($s8) % 4, "\x3d", STR_PAD_RIGHT));
    }
    function export_plugin_config($f7 = false)
    {
        $WL = [];
        $Lc = [];
        $YF = [];
        $WL = $this->get_plugin_config();
        $Lc = get_site_option("\155\x6f\137\157\x61\x75\x74\150\137\141\x70\160\x73\137\154\151\x73\x74");
        if (empty($WL)) {
            goto rO;
        }
        $WL = $WL->get_current_config();
        rO:
        if (!is_array($Lc)) {
            goto tD;
        }
        foreach ($Lc as $pY => $xA) {
            if (!is_array($xA)) {
                goto RA;
            }
            $xA = new App($xA);
            RA:
            $yd = $xA->get_app_config('', false);
            if (!$f7) {
                goto YM;
            }
            unset($yd["\143\x6c\151\145\156\x74\x5f\x69\x64"]);
            unset($yd["\143\x6c\x69\x65\156\x74\x5f\163\145\143\x72\x65\164"]);
            YM:
            $YF[$pY] = $yd;
            ks:
        }
        wR:
        tD:
        $yL = ["\160\x6c\165\x67\x69\156\x5f\143\x6f\x6e\x66\x69\147" => $WL, "\141\x70\160\x5f\143\157\156\146\151\x67\163" => $YF];
        $yL = apply_filters("\x6d\x6f\137\164\x72\137\147\x65\x74\x5f\154\151\143\x65\156\x73\145\137\x63\x6f\156\146\x69\147", $yL);
        return $yL;
    }
    private function verify_lk()
    {
        $pZ = new \MoOauthClient\Standard\Customer();
        $It = $this->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\x75\x74\x68\x5f\154\151\143\145\156\163\x65\x5f\x6b\x65\171");
        if (!empty($It)) {
            goto jy;
        }
        return 0;
        jy:
        $td = $pZ->XfskodsfhHJ($It);
        $td = json_decode($td, true);
        return isset($td["\163\x74\141\164\165\x73"]) && "\123\x55\103\x43\105\123\x53" === $td["\163\x74\141\164\165\163"];
    }
    public function is_valid_jwt($CS = '')
    {
        $Rs = explode("\x2e", $CS);
        if (!(count($Rs) === 3)) {
            goto fp;
        }
        return true;
        fp:
        return false;
    }
    public function validate_appslist($FO)
    {
        if (is_array($FO)) {
            goto Y_;
        }
        return false;
        Y_:
        foreach ($FO as $NZ => $Zy) {
            if (!$Zy instanceof \MoOauthClient\App) {
                goto zs;
            }
            goto KY;
            zs:
            return false;
            KY:
        }
        Tl:
        return true;
    }
    public function handle_error($lM)
    {
        do_action("\155\157\137\164\162\137\x6c\157\x67\x69\x6e\137\x65\162\x72\x6f\x72\x73", $lM);
    }
    public function set_transient($NZ, $mB, $Pc)
    {
        return is_multisite() && $this->is_multisite ? set_site_transient($NZ, $mB, $Pc) : set_transient($NZ, $mB, $Pc);
    }
    public function get_transient($NZ)
    {
        return is_multisite() && $this->is_multisite ? get_site_transient($NZ) : get_transient($NZ);
    }
    public function delete_transient($NZ)
    {
        return is_multisite() && $this->is_multisite ? delete_site_transient($NZ) : delete_transient($NZ);
    }
}
