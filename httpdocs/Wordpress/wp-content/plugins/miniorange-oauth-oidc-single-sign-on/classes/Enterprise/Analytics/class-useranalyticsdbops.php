<?php


namespace MoOauthClient\Enterprise;

class UserAnalyticsDBOps
{
    const USER_TRANSACTIONS_TABLE = "\x77\160\156\163\x5f\x74\162\x61\156\163\x61\143\164\x69\157\x6e\x73";
    public function __construct()
    {
    }
    public function make_table_if_not_exists()
    {
        global $wpdb;
        $uc = "\103\x52\x45\x41\x54\x45\x20\124\101\102\x4c\x45\x20\x49\x46\x20\x4e\117\124\x20\x45\x58\x49\x53\124\x53\x20" . $wpdb->base_prefix . self::USER_TRANSACTIONS_TABLE . "\40\x28\15\12\x9\11\x9\140\151\x64\140\40\142\151\147\x69\156\x74\x20\x4e\117\124\x20\x4e\x55\x4c\x4c\x20\101\125\x54\117\137\x49\x4e\103\x52\105\x4d\105\x4e\x54\40\x50\122\x49\115\101\x52\x59\40\x4b\x45\131\54\40\x20\140\165\163\145\162\x6e\x61\x6d\145\140\x20\155\x65\x64\151\x75\155\x74\145\x78\x74\40\x4e\x4f\x54\40\x4e\x55\x4c\x4c\x20\x2c\x60\163\164\141\x74\x75\163\140\40\x6d\x65\x64\x69\x75\155\164\x65\x78\164\40\x4e\117\x54\x20\116\x55\114\x4c\40\54\x60\x61\160\x70\x6e\x61\x6d\x65\140\x20\155\145\x64\x69\165\155\x74\x65\x78\x74\x20\x4e\x4f\x54\40\116\x55\114\x4c\54\40\x60\145\x6d\x61\x69\154\x60\x20\x6d\145\x64\x69\165\155\164\145\170\x74\40\x4e\x4f\x54\40\116\125\x4c\x4c\x2c\x20\x60\143\x6c\x69\145\156\164\151\160\140\40\155\x65\144\151\x75\155\164\145\170\x74\40\x4e\117\x54\x20\x4e\x55\x4c\114\x2c\x20\x60\156\x61\166\x69\147\x61\x74\x69\x6f\x6e\x75\162\x6c\140\40\x6d\145\144\151\x75\155\x74\145\170\164\40\x4e\117\124\x20\x4e\125\x4c\114\54\x20\140\143\x72\145\141\x74\x65\x64\x5f\x74\x69\x6d\145\x73\164\x61\x6d\x70\140\x20\124\x49\115\x45\x53\x54\101\x4d\x50\40\104\105\106\x41\x55\114\x54\x20\x43\x55\122\122\105\x4e\124\x5f\124\x49\115\x45\x53\124\x41\x4d\x50\x2c\x20\125\x4e\x49\x51\125\x45\x20\x4b\x45\x59\40\x69\144\40\x28\151\144\51\51\x3b";
        require_once ABSPATH . "\x77\160\x2d\x61\x64\x6d\151\156\57\151\156\143\154\x75\144\145\x73\x2f\x75\160\x67\162\x61\144\145\x2e\x70\x68\x70";
        dbDelta($uc);
        $this->add_col_if_not_exists(self::USER_TRANSACTIONS_TABLE, "\145\155\x61\x69\x6c");
        $this->add_col_if_not_exists(self::USER_TRANSACTIONS_TABLE, "\x63\x6c\x69\x65\x6e\x74\x69\x70");
        $this->add_col_if_not_exists(self::USER_TRANSACTIONS_TABLE, "\x6e\x61\x76\x69\x67\141\164\x69\157\156\x75\162\x6c");
    }
    public function check_col_exists($Y7 = '', $or = '')
    {
        if (!('' === $Y7 || '' === $or)) {
            goto GR;
        }
        return false;
        GR:
        global $wpdb;
        $nD = $wpdb->get_results($wpdb->prepare("\123\x45\x4c\105\103\124\40\x2a\x20\x46\122\x4f\115\x20\x49\116\x46\x4f\x52\115\101\x54\111\x4f\x4e\x5f\x53\x43\110\x45\x4d\x41\56\103\x4f\114\125\115\116\x53\x20\127\x48\105\x52\105\40\x54\101\x42\x4c\105\x5f\x53\103\x48\x45\115\101\x20\75\40\45\163\x20\101\116\104\40\x54\x41\102\114\105\137\x4e\101\115\x45\40\75\x20\x25\x73\x20\101\116\104\40\103\117\x4c\125\x4d\116\137\x4e\101\115\105\x20\x3d\x20\45\163\x20", DB_NAME, $wpdb->base_prefix . $Y7, $or));
        if (empty($nD)) {
            goto wB;
        }
        return true;
        wB:
        return false;
    }
    public function add_col_if_not_exists($Y7 = '', $or = '', $pa = true)
    {
        if (!('' === $Y7 || '' === $or)) {
            goto K4;
        }
        return false;
        K4:
        if (!$this->check_col_exists($Y7, $or)) {
            goto ab;
        }
        return true;
        ab:
        global $wpdb;
        $NW = $pa ? "\x4e\117\124\x20\116\x55\x4c\x4c" : '';
        $wpdb->query("\x41\114\124\105\122\x20\124\101\102\114\105\40" . $wpdb->base_prefix . self::USER_TRANSACTIONS_TABLE . "\x20\x41\x44\104\40" . $or . "\x20\155\x65\144\x69\x75\x6d\164\145\x78\x74\x20" . $NW);
    }
    private function get_all_transactions()
    {
        global $wpdb;
        $QI = $wpdb->get_results("\x53\105\114\x45\103\x54\x20\x75\163\x65\162\156\x61\155\x65\x2c\40\163\164\x61\x74\x75\x73\x20\54\141\x70\160\156\x61\155\x65\40\x2c\143\x72\x65\x61\x74\145\x64\x5f\x74\151\155\145\163\164\141\x6d\160\x2c\x20\145\x6d\141\151\x6c\x2c\x20\143\154\x69\145\156\164\x69\160\x2c\40\x6e\x61\x76\151\x67\x61\164\151\x6f\156\x75\x72\154\x20\106\x52\117\115\40" . $wpdb->base_prefix . self::USER_TRANSACTIONS_TABLE);
        return $QI;
    }
    public function get_entries($x1 = true)
    {
        $bs = $this->get_all_transactions();
        if ($bs) {
            goto vk;
        }
        return [];
        vk:
        if (!(true === $x1)) {
            goto Xx;
        }
        return $bs;
        Xx:
        return [];
    }
    public function add_transact($x1 = array(), $zP = false)
    {
        if (!$zP) {
            goto Hw;
        }
        $this->add_to_wpdb();
        return true;
        Hw:
        $RQ = $this->add_to_wpdb($Lj = isset($x1["\165\x73\x65\162\x6e\141\155\145"]) ? $x1["\165\163\145\x72\156\x61\155\145"] : "\55", $k9 = isset($x1["\x73\x74\x61\164\x75\x73"]) ? $x1["\x73\x74\x61\x74\165\x73"] : false, $It = isset($x1["\x63\x6f\x64\x65"]) ? $x1["\x63\157\x64\145"] : "\55", $bj = isset($x1["\141\160\160\154\x69\143\x61\x74\151\x6f\x6e"]) ? $x1["\141\x70\160\154\x69\143\141\x74\151\157\156"] : "\55", $UU = isset($x1["\x65\x6d\141\151\x6c"]) ? $x1["\x65\155\x61\151\154"] : "\x2d", $Dp = isset($x1["\143\154\151\145\156\164\137\x69\160"]) ? $x1["\143\154\151\x65\x6e\x74\x5f\151\x70"] : "\x2d", $Sj = isset($x1["\x6e\x61\166\151\x67\x61\164\151\157\x6e\x75\162\154"]) ? $x1["\x6e\x61\166\151\x67\141\x74\151\x6f\x6e\165\162\x6c"] : "\x2d");
        return \boolval($RQ);
    }
    private function add_to_wpdb($Lj = '', $k9 = false, $It = '', $bj = '', $UU = '', $Dp = '', $Sj = '')
    {
        $oA = '';
        if (!('' === $It && false === $k9)) {
            goto JH;
        }
        $oA = "\x4e\x2f\x41";
        JH:
        $oA = $this->get_status_message($It, $k9);
        $x1 = ["\x75\163\145\x72\156\x61\x6d\145" => isset($Lj) && '' !== $Lj ? $Lj : "\x2d", "\163\164\141\x74\165\x73" => isset($oA) && '' !== $oA ? $oA : "\x2d", "\141\x70\160\156\141\x6d\145" => isset($bj) && '' !== $bj ? $bj : "\55", "\145\x6d\141\151\x6c" => isset($UU) && '' !== $UU ? $UU : "\x2d", "\143\x6c\x69\145\156\x74\x69\160" => isset($Dp) && '' !== $Dp ? $Dp : "\x2d", "\156\141\x76\151\147\x61\164\x69\x6f\156\165\x72\154" => isset($Sj) && '' !== $Sj ? $Sj : "\x2d"];
        $x1 = apply_filters("\x6d\x6f\x5f\160\147\x5f\x61\144\144\x5f\x73\x75\x62\163\151\164\145\137\143\157\x6c\137\166\141\154\x75\145", $x1);
        global $wpdb;
        return $wpdb->insert($wpdb->base_prefix . self::USER_TRANSACTIONS_TABLE, $x1);
    }
    private function get_status_message($It = '', $k9 = '')
    {
        if (!(true === $k9)) {
            goto z3;
        }
        return "\x53\125\x43\103\x45\123\x53";
        z3:
        switch ($It) {
            case "\x45\x4d\101\111\x4c":
                return "\106\101\x49\x4c\x45\104\56\40\x49\156\x76\141\154\151\144\40\105\x6d\141\x69\x6c\x20\x52\x65\x63\x65\151\x76\x65\x64\x2e";
            case "\125\116\101\x4d\105":
                return "\x46\101\x49\114\105\104\x2e\40\x49\x6e\x76\x61\x6c\x69\x64\x20\x55\163\x65\162\x6e\x61\155\145\x20\122\x65\143\x65\151\x65\x76\145\144\56";
            case "\x52\x45\107\x49\123\x54\122\101\124\x49\117\x4e\x5f\104\x49\x53\x41\x42\x4c\105\104":
                return "\106\101\111\114\x45\104\x2e\40\122\145\147\x69\163\x74\162\x61\164\151\157\x6e\40\x64\x69\163\141\142\154\x65\144\x2e";
            default:
                return "\x46\101\111\x4c\105\104";
        }
        Na:
        ce:
    }
    public function drop_table()
    {
        global $wpdb;
        if (!($wpdb->get_var("\123\x48\x4f\x57\x20\x54\101\x42\114\x45\123\x20\114\x49\x4b\x45\x20\x27" . $wpdb->prefix . self::USER_TRANSACTIONS_TABLE . "\x27") === $wpdb->prefix . self::USER_TRANSACTIONS_TABLE)) {
            goto Wo;
        }
        $ly = $wpdb->get_results("\x44\122\x4f\x50\x20\x54\x41\102\x4c\x45\40" . $wpdb->prefix . self::USER_TRANSACTIONS_TABLE);
        Wo:
    }
}
