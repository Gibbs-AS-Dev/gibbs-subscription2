<?php


namespace MoOauthClient\Backup;

use MoOauthClient\App;
class EnvVarResolver
{
    public static function resolve_var($NZ, $mB)
    {
        switch ($NZ) {
            case "\155\x6f\x5f\x6f\x61\165\164\x68\137\141\x70\160\x73\x5f\x6c\x69\x73\x74":
                $mB = self::resolve_apps_list($mB);
                goto tv;
            default:
                goto tv;
        }
        vG:
        tv:
        return $mB;
    }
    private static function resolve_apps_list($mB)
    {
        if (!is_array($mB)) {
            goto U4;
        }
        return $mB;
        U4:
        $mB = json_decode($mB, true);
        if (!(json_last_error() !== JSON_ERROR_NONE)) {
            goto hP;
        }
        return [];
        hP:
        $zd = [];
        foreach ($mB as $pY => $xA) {
            if (!$xA instanceof App) {
                goto Xy;
            }
            $zd[$pY] = $xA;
            goto vm;
            Xy:
            if (!(!isset($xA["\x63\x6c\151\x65\x6e\x74\x5f\151\x64"]) || empty($xA["\143\x6c\x69\x65\x6e\x74\x5f\151\x64"]))) {
                goto TC;
            }
            $xA["\143\154\151\x65\x6e\164\137\151\x64"] = isset($xA["\143\x6c\x69\x65\x6e\x74\151\144"]) ? $xA["\x63\154\151\145\x6e\x74\x69\144"] : '';
            TC:
            if (!(!isset($xA["\x63\x6c\151\x65\156\164\x5f\163\145\x63\x72\x65\164"]) || empty($xA["\x63\x6c\x69\145\x6e\164\x5f\163\145\x63\x72\145\x74"]))) {
                goto nq;
            }
            $xA["\143\154\x69\145\x6e\x74\x5f\163\x65\x63\x72\x65\164"] = isset($xA["\x63\x6c\151\x65\x6e\164\163\x65\143\162\145\x74"]) ? $xA["\x63\x6c\151\x65\x6e\164\163\145\143\162\x65\164"] : '';
            nq:
            unset($xA["\143\154\151\145\x6e\x74\151\x64"]);
            unset($xA["\143\154\151\x65\156\x74\x73\x65\143\162\x65\x74"]);
            $Zy = new App();
            $Zy->migrate_app($xA, $pY);
            $zd[$pY] = $Zy;
            vm:
        }
        O_:
        return $zd;
    }
}
