<?php


namespace MoOauthClient\Enterprise;

use MoOauthClient\Premium\PremiumSettings;
use MoOauthClient\Enterprise\SignInSettingsSettings;
use MoOauthClient\Enterprise\AppSettings;
use MoOauthClient\Enterprise\UserAnalyticsDBOps as DBOps;
class EnterpriseSettings
{
    private $premium_settings;
    public function __construct()
    {
        $this->premium_settings = new PremiumSettings();
        add_action("\141\x64\x6d\x69\156\137\x69\x6e\x69\x74", array($this, "\155\157\137\157\x61\x75\x74\150\x5f\x63\x6c\x69\x65\x6e\164\137\145\x6e\x74\x65\162\160\x72\x69\163\x65\137\x73\145\164\x74\x69\x6e\x67\x73"));
    }
    public function mo_oauth_client_enterprise_settings()
    {
        $YD = new SignInSettingsSettings();
        $tN = new AppSettings();
        $YD->mo_oauth_save_settings();
        $tN->save_advanced_grant_settings();
        if (!(isset($_POST["\155\157\137\x77\160\156\x73\137\x6d\141\x6e\x75\x61\x6c\137\x63\x6c\x65\141\x72\137\156\157\x6e\x63\145"]) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST["\155\157\137\167\x70\x6e\x73\x5f\155\x61\x6e\165\x61\154\137\x63\x6c\x65\141\x72\x5f\x6e\x6f\156\x63\145"])), "\x6d\157\x5f\167\160\x6e\x73\137\155\x61\156\165\141\x6c\137\x63\154\145\141\162") && isset($_POST[\MoOAuthConstants::OPTION]) && "\x6d\x6f\137\167\x70\156\163\x5f\155\x61\x6e\x75\141\154\137\x63\154\145\141\x72" === $_POST[\MoOAuthConstants::OPTION])) {
            goto GK;
        }
        $vV = new DBOps();
        $vV->drop_table();
        GK:
    }
}
