<?php


namespace MoOauthClient\Premium;

use MoOauthClient\Standard\StandardSettings;
use MoOauthClient\Premium\AppSettings;
use MoOauthClient\Premium\SignInSettingsSettings;
class PremiumSettings
{
    private $standard_settings;
    public function __construct()
    {
        $this->standard_settings = new StandardSettings();
        add_action("\x61\x64\x6d\151\156\x5f\151\x6e\x69\x74", array($this, "\x6d\x6f\137\157\141\165\164\x68\137\x63\154\151\x65\156\x74\x5f\x70\162\x65\x6d\151\165\x6d\x5f\x73\145\x74\x74\x69\156\x67\163"));
    }
    public function mo_oauth_client_premium_settings()
    {
        $YD = new SignInSettingsSettings();
        $tN = new AppSettings();
        $tN->save_app_settings();
        $tN->save_advanced_grant_settings();
        $YD->mo_oauth_save_settings();
        if (!is_multisite()) {
            goto xph;
        }
        $hP = new MultisiteSettings();
        $hP->save_multisite_settings();
        xph:
    }
}
