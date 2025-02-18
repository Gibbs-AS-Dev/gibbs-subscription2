<?php


namespace MoOauthClient\Backup;

use MoOauthClient\App;
use MoOauthClient\Config;
class BackupHandler
{
    private $plugin_config;
    private $apps_list;
    public static function restore_settings($LN = '')
    {
        if (!(!is_array($LN) || empty($LN))) {
            goto VL;
        }
        return false;
        VL:
        $k9 = false;
        $WL = isset($LN["\x70\154\165\x67\151\156\x5f\x63\x6f\156\x66\x69\x67"]) ? $LN["\160\x6c\x75\147\151\x6e\x5f\x63\x6f\156\x66\x69\147"] : false;
        $Lc = isset($LN["\141\x70\x70\x5f\x63\x6f\156\x66\151\147\163"]) ? $LN["\141\x70\160\137\x63\157\x6e\x66\151\x67\x73"] : false;
        if (!$WL) {
            goto L1;
        }
        $k9 = self::restore_plugin_config($WL);
        L1:
        if (!$Lc) {
            goto on;
        }
        return $k9 && self::restore_apps_config($Lc);
        on:
        return false;
    }
    private static function restore_plugin_config($WL)
    {
        global $mx;
        if (!empty($WL)) {
            goto po;
        }
        return false;
        po:
        $n2 = new Config($WL);
        if (empty($n2)) {
            goto cl;
        }
        $mx->mo_oauth_client_update_option("\x6d\157\x5f\x6f\141\165\x74\x68\137\x63\154\151\145\x6e\x74\137\143\x6f\x6e\146\x69\147", $n2);
        return true;
        cl:
        return false;
    }
    private static function restore_apps_config($Lc)
    {
        global $mx;
        if (!(!is_array($Lc) && empty($Lc))) {
            goto Ca;
        }
        return false;
        Ca:
        $Ye = [];
        foreach ($Lc as $pY => $xA) {
            $Zy = new App($xA);
            $Zy->set_app_name($pY);
            $Ye[$pY] = $Zy;
            Q4:
        }
        Oe:
        $mx->mo_oauth_client_update_option("\155\157\x5f\x6f\141\x75\x74\x68\137\x61\160\160\163\x5f\x6c\x69\x73\164", $Ye);
        return true;
    }
    public static function get_backup_json()
    {
        global $mx;
        $W_ = $mx->export_plugin_config();
        return json_encode($W_, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
