<?php


namespace MoOauthClient\Premium;

use MoOauthClient\Mo_Oauth_Debug;
class MappingHandler
{
    private $user_id = 0;
    private $app_config = array();
    private $group_name = '';
    private $is_new_user = false;
    public function __construct($b2 = 0, $xA = array(), $X_ = '', $CJ = false)
    {
        if (!(!array($xA) || empty($xA))) {
            goto JJ1;
        }
        return;
        JJ1:
        $kC = is_array($X_) ? $X_ : $this->get_group_array($X_);
        $this->group_name = $kC;
        $this->user_id = $b2;
        $this->app_config = $xA;
        $this->is_new_user = $CJ;
    }
    private function get_group_array($eO)
    {
        $qZ = json_decode($eO, true);
        return is_array($qZ) && json_last_error() === JSON_ERROR_NONE ? $qZ : explode("\x3b", $eO);
    }
    public function apply_custom_attribute_mapping($SR)
    {
        if (!(!isset($this->app_config["\x63\165\163\164\157\x6d\137\141\164\x74\162\x73\x5f\x6d\141\x70\160\x69\156\147"]) || empty($this->app_config["\x63\165\163\164\157\x6d\137\141\164\x74\x72\163\137\155\141\x70\x70\151\x6e\147"]))) {
            goto G5l;
        }
        return;
        G5l:
        global $mx;
        $wz = -1;
        $v_ = $this->app_config["\143\x75\163\x74\157\155\x5f\x61\164\164\x72\x73\x5f\155\x61\160\x70\x69\x6e\x67"];
        $Cx = [];
        foreach ($v_ as $NZ => $mB) {
            $DA = [];
            $uJ = '';
            if (strpos($mB, "\73") !== false) {
                goto tD9;
            }
            $uJ = $mx->getnestedattribute($SR, $mB);
            goto hyj;
            tD9:
            $DA = array_map("\164\162\x69\155", explode("\73", $mB));
            foreach ($DA as $lm => $fb) {
                $O1 = $mx->getnestedattribute($SR, $fb);
                $uJ .= "\x20" . $O1;
                emr:
            }
            wZN:
            hyj:
            $Cx[$NZ] = $uJ;
            update_user_meta($this->user_id, $NZ, $uJ);
            do_action("\x6d\x6f\x5f\x73\164\141\x74\x69\x63\137\x61\x74\164\162\137\x6d\x61\x70\x70\x69\156\x67", $this->user_id, $NZ, $mB);
            p22:
        }
        TjA:
        update_user_meta($this->user_id, "\x6d\x6f\x5f\x6f\141\165\x74\150\137\143\165\x73\164\157\155\137\141\164\164\162\151\142\x75\x74\x65\x73", $Cx);
    }
    public function apply_role_mapping($SR)
    {
        if (!has_filter("\155\157\137\163\x75\142\x73\151\164\x65\137\143\x68\145\x63\x6b\137\141\154\x6c\157\x77\137\154\157\147\x69\x6e")) {
            goto wAy;
        }
        $ic = apply_filters("\155\x6f\x5f\x73\x75\142\x73\x69\164\145\x5f\143\x68\x65\x63\x6b\137\x65\170\151\x73\x74\x69\156\x67\137\162\x6f\154\145\163", $this->app_config, $this->is_new_user);
        if (!($ic === true)) {
            goto RJV;
        }
        return;
        RJV:
        goto Oai;
        wAy:
        if (!(!$this->is_new_user && isset($this->app_config["\x6b\145\145\x70\137\x65\170\x69\x73\164\x69\x6e\x67\x5f\165\x73\x65\162\137\162\x6f\x6c\x65\163"]) && 1 === intval($this->app_config["\x6b\145\x65\160\x5f\x65\x78\151\163\x74\x69\x6e\147\x5f\x75\163\145\162\x5f\162\157\x6c\145\x73"]))) {
            goto fnW;
        }
        return;
        fnW:
        Oai:
        $Ib = new \WP_User($this->user_id);
        if (!($this->is_new_user && isset($this->app_config["\x65\156\141\x62\154\145\x5f\x72\x6f\154\x65\137\155\141\160\160\x69\x6e\147"]) && !boolval($this->app_config["\x65\156\x61\x62\x6c\145\x5f\x72\157\154\x65\137\x6d\141\x70\x70\151\156\147"]))) {
            goto MuW;
        }
        $Ib->set_role('');
        return;
        MuW:
        if (!has_filter("\155\x6f\137\157\141\x75\x74\x68\137\x72\x61\166\x65\x6e\x5f\x62\171\x5f\160\141\163\x73\x5f\162\157\154\x65\137\x6d\141\160\x70\151\156\147")) {
            goto zv2;
        }
        $GL = apply_filters("\155\157\137\157\x61\x75\x74\150\x5f\x72\141\x76\145\156\137\x62\x79\137\160\141\163\163\x5f\162\x6f\154\145\x5f\x6d\141\160\x70\x69\156\x67", $Ib);
        if (!($GL === true)) {
            goto RtF;
        }
        return;
        RtF:
        zv2:
        $DP = 0;
        $gD = false;
        if (!has_filter("\x6d\157\137\163\145\x74\x5f\x63\165\162\162\145\x6e\164\x5f\163\x69\x74\x65\137\162\157\154\x65\x73")) {
            goto mBV;
        }
        $L_ = apply_filters("\155\x6f\137\163\145\164\x5f\x63\x75\162\162\145\x6e\164\x5f\163\151\164\x65\137\162\157\154\x65\x73", $this->app_config, $this->group_name, $DP, $Ib);
        goto Lck;
        mBV:
        $x2 = isset($this->app_config["\162\157\154\145\137\x6d\x61\x70\160\x69\156\147\137\x63\x6f\165\156\x74"]) ? intval($this->app_config["\162\157\x6c\x65\137\155\141\x70\160\151\x6e\147\x5f\143\157\165\x6e\164"]) : 0;
        $aw = [];
        $wz = 1;
        hAA:
        if (!($wz <= $x2)) {
            goto fGG;
        }
        $Sf = isset($this->app_config["\137\x6d\141\160\160\x69\156\147\x5f\x6b\x65\171\x5f" . $wz]) ? $this->app_config["\137\x6d\141\x70\x70\x69\x6e\x67\x5f\x6b\x65\x79\137" . $wz] : '';
        array_push($aw, $Sf);
        foreach ($this->group_name as $JW) {
            $Lo = explode("\54", $Sf);
            $xP = isset($this->app_config["\x5f\x6d\x61\x70\x70\x69\x6e\147\137\x76\141\154\165\x65\137" . $wz]) ? $this->app_config["\137\155\141\x70\160\x69\156\147\137\x76\x61\154\165\x65\137" . $wz] : '';
            $gD = apply_filters("\x6d\157\x5f\157\141\165\x74\150\x5f\143\x6c\151\145\156\164\137\x64\171\156\141\155\151\x63\137\x76\x61\154\x75\145\x5f\162\157\x6c\x65\137\155\x61\160\x70\x69\156\147", $Lo, $JW, $xP);
            $Y8 = explode("\x2c", $JW);
            if (!($Y8 == $Lo || true === $gD)) {
                goto MZF;
            }
            if (!$xP) {
                goto oSy;
            }
            if (!(0 === $DP)) {
                goto ckA;
            }
            $Ib->set_role('');
            ckA:
            $Ib->add_role($xP);
            $DP++;
            oSy:
            MZF:
            CkL:
        }
        BZM:
        mEu:
        $wz++;
        goto hAA;
        fGG:
        Lck:
        if (empty($this->group_name[0])) {
            goto QSC;
        }
        $bj = '';
        $Lc = get_site_option("\155\x6f\137\x6f\x61\x75\x74\150\x5f\x61\x70\160\163\x5f\154\x69\x73\x74");
        $oz = isset($this->app_config["\165\156\151\161\x75\145\137\x61\160\160\x69\x64"]) ? $this->app_config["\165\x6e\151\x71\165\x65\137\141\x70\160\x69\x64"] : '';
        if (!is_array($Lc)) {
            goto z26;
        }
        foreach ($Lc as $pY => $n2) {
            $p_ = $n2->get_app_config();
            if (!($this->app_config["\x61\x70\160\x49\144"] == $p_["\141\160\160\x49\144"] && $oz === $pY)) {
                goto jBj;
            }
            MO_Oauth_Debug::mo_oauth_log("\x63\x6f\x6d\160\x61\162\145\144\x20\x61\156\x64\x20\x66\157\x75\x6e\144\x20\143\165\x72\x72\145\156\164\x20\141\160\160\x20\x2d\40" . $pY);
            $bj = $pY;
            jBj:
            Z2B:
        }
        Msy:
        z26:
        global $mx;
        $Ui = isset($this->app_config["\x75\x73\x65\x72\156\141\155\145\137\x61\x74\164\162"]) ? $this->app_config["\x75\x73\x65\162\x6e\x61\x6d\x65\x5f\x61\x74\164\162"] : '';
        $aQ = isset($this->app_config["\145\x6d\141\151\154\x5f\141\x74\164\162"]) ? $this->app_config["\x65\155\x61\x69\154\x5f\x61\164\x74\162"] : '';
        $lC = isset($this->app_config["\146\151\x72\x73\164\156\x61\155\145\137\x61\164\x74\162"]) ? $this->app_config["\x66\151\x72\x73\164\x6e\141\x6d\x65\137\x61\x74\164\162"] : '';
        $Zp = isset($this->app_config["\x6c\x61\x73\164\156\x61\155\145\137\141\x74\164\162"]) ? $this->app_config["\154\x61\163\x74\x6e\x61\155\x65\137\x61\164\164\x72"] : '';
        $Lj = $mx->getnestedattribute($SR, $Ui);
        $UU = $mx->getnestedattribute($SR, $aQ);
        $m3 = $mx->getnestedattribute($SR, $lC);
        $sF = $mx->getnestedattribute($SR, $Zp);
        Mo_Oauth_Debug::mo_oauth_log("\x53\x65\156\x74\40\144\x65\164\141\151\x6c\163\x20\x74\157\x20\x6c\145\141\162\x6e\x64\141\x73\x68\x3a\x20");
        Mo_Oauth_Debug::mo_oauth_log($Lj);
        Mo_Oauth_Debug::mo_oauth_log(json_encode($this->group_name));
        Mo_Oauth_Debug::mo_oauth_log($bj);
        do_action("\155\x6f\137\x6f\x61\165\164\150\x5f\141\164\164\162\x69\x62\165\x74\145\x73", $Lj, $UU, $m3, $sF, $this->group_name, $bj);
        QSC:
        if (!has_filter("\x6d\x6f\x5f\163\x65\x74\137\x63\165\162\x72\x65\x6e\x74\137\163\x69\164\145\137\x72\157\154\x65\163")) {
            goto SJ4;
        }
        $blog_id = get_current_blog_id();
        if (0 === $L_["\x72\157\x6c\x65\163"] && isset($this->app_config["\155\157\137\163\x75\142\x73\151\x74\145\137\162\157\154\145\137\155\141\160\160\151\x6e\147"][$blog_id]["\137\155\141\160\160\x69\x6e\x67\137\166\141\154\x75\x65\x5f\144\145\146\x61\165\154\x74"]) && '' !== $this->app_config["\155\x6f\137\163\165\x62\163\151\x74\x65\137\162\157\x6c\x65\137\155\x61\x70\x70\151\156\147"][$blog_id]["\x5f\x6d\141\160\x70\x69\x6e\x67\137\x76\141\154\165\145\137\144\x65\146\x61\x75\x6c\x74"]) {
            goto Piw;
        }
        if (!(0 === $L_["\162\157\154\145\x73"] && empty($this->app_config["\x6d\157\137\163\165\x62\x73\151\164\145\137\x72\x6f\154\145\x5f\155\x61\x70\x70\x69\156\147"][$blog_id]["\137\155\x61\160\160\151\156\147\x5f\166\x61\x6c\165\145\x5f\144\145\146\x61\x75\154\164"]))) {
            goto jga;
        }
        $Ib->set_role("\163\x75\142\x73\x63\x72\x69\x62\x65\x72");
        jga:
        goto uKZ;
        Piw:
        $Ib->set_role($this->app_config["\x6d\157\x5f\x73\165\142\163\151\x74\145\137\162\157\154\145\x5f\x6d\x61\x70\160\x69\x6e\x67"][$blog_id]["\137\x6d\141\160\x70\x69\156\x67\x5f\166\x61\x6c\165\x65\x5f\144\x65\x66\x61\165\x6c\164"]);
        uKZ:
        goto nWA;
        SJ4:
        if (!(0 === $DP && isset($this->app_config["\x5f\x6d\141\160\160\151\x6e\147\x5f\166\x61\154\x75\145\x5f\144\145\146\x61\x75\x6c\164"]) && '' !== $this->app_config["\x5f\x6d\141\x70\x70\x69\x6e\x67\x5f\166\x61\154\x75\x65\x5f\144\x65\x66\141\x75\x6c\x74"])) {
            goto ZcB;
        }
        $Ib->set_role($this->app_config["\x5f\155\x61\160\160\x69\x6e\147\x5f\x76\x61\x6c\165\x65\137\x64\145\146\141\165\x6c\164"]);
        ZcB:
        nWA:
        if (!has_filter("\x6d\x6f\x5f\163\165\x62\163\x69\164\x65\137\143\x68\x65\143\x6b\137\x61\154\x6c\157\167\137\154\157\147\x69\x6e")) {
            goto Zs3;
        }
        $sv = apply_filters("\x6d\157\x5f\x73\x75\x62\x73\151\x74\145\137\x63\x68\x65\x63\153\137\x61\x6c\x6c\x6f\167\137\x6c\x6f\x67\x69\156", $this->app_config, $this->group_name, $L_["\155\141\160\160\145\x64\137\162\157\154\x65\x73"]);
        goto OrQ;
        Zs3:
        $zw = 0;
        if (!(isset($this->app_config["\x72\145\163\x74\x72\151\143\x74\x5f\154\x6f\x67\x69\156\x5f\146\x6f\x72\x5f\155\x61\160\160\145\144\137\x72\157\154\145\163"]) && boolval($this->app_config["\162\x65\x73\x74\162\151\143\164\137\154\157\x67\x69\x6e\137\146\157\162\137\x6d\141\x70\160\145\x64\x5f\x72\x6f\154\145\163"]))) {
            goto iR8;
        }
        foreach ($this->group_name as $v2) {
            if (!(in_array($v2, $aw, true) || true === $gD)) {
                goto eRz;
            }
            $zw = 1;
            eRz:
            O24:
        }
        zet:
        if (!($zw !== 1)) {
            goto U9y;
        }
        require_once ABSPATH . "\x77\160\55\141\x64\155\151\156\57\151\156\x63\x6c\x75\144\x65\x73\57\x75\163\145\162\x2e\160\150\x70";
        \wp_delete_user($this->user_id);
        global $mx;
        $lM = "\131\x6f\x75\x20\144\157\40\x6e\x6f\164\x20\x68\x61\166\145\40\160\145\162\x6d\151\163\x73\151\x6f\156\163\40\x74\x6f\40\x6c\157\x67\151\156\x20\167\x69\x74\150\x20\x79\157\x75\162\x20\x63\165\162\x72\145\x6e\164\x20\162\x6f\154\145\x73\56\40\x50\x6c\145\x61\x73\x65\x20\x63\157\156\x74\141\143\x74\40\164\x68\145\x20\101\x64\x6d\x69\x6e\151\163\x74\x72\141\x74\x6f\x72\56";
        $mx->handle_error($lM);
        wp_die($lM);
        U9y:
        iR8:
        OrQ:
    }
}
