<?php


namespace MoOauthClient\Enterprise;

use MoOauthClient\App;
use MoOauthClient\Premium\AppSettings as PremiumAppSettings;
class AppSettings extends PremiumAppSettings
{
    public function save_grant_settings($post, $Z6)
    {
        $Z6 = parent::save_grant_settings($post, $Z6);
        global $mx;
        $Z6["\160\x6b\x63\145\137\x66\x6c\157\167"] = isset($post["\160\x6b\143\x65\137\146\154\x6f\167"]) ? 1 : 0;
        $Z6["\160\x6b\x63\145\137\143\x6c\x69\x65\x6e\164\x5f\163\145\x63\162\145\x74"] = isset($post["\x70\153\143\x65\137\143\x6c\x69\x65\156\x74\x5f\163\145\143\x72\145\x74"]) ? 1 : 0;
        return $Z6;
    }
}
