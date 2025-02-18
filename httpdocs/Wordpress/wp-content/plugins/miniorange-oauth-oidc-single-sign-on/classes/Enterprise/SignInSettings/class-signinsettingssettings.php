<?php


namespace MoOauthClient\Enterprise;

use MoOauthClient\Config;
use MoOauthClient\Premium\SignInSettingsSettings as PremiumSignInSettingsSettings;
use MoOauthClient\Enterprise\UserAnalyticsDBOps;
class SignInSettingsSettings extends PremiumSignInSettingsSettings
{
    public function __construct()
    {
        $this->db_ops = new UserAnalyticsDBOps();
    }
    public function change_current_config($post, $n2)
    {
        $n2 = parent::change_current_config($post, $n2);
        $n2->add_config("\x64\x79\156\141\x6d\x69\x63\137\143\x61\154\154\142\141\143\153\x5f\165\162\154", isset($post["\x64\171\156\141\x6d\151\x63\137\x63\x61\x6c\154\142\141\143\153\x5f\x75\x72\154"]) ? stripslashes(wp_unslash($post["\144\x79\x6e\x61\155\151\x63\137\143\141\x6c\154\142\x61\143\153\x5f\165\x72\154"])) : '');
        $n2->add_config("\x61\143\x74\x69\x76\141\164\x65\137\165\x73\145\x72\137\x61\x6e\x61\154\x79\x74\151\x63\x73", isset($post["\x6d\x6f\137\141\x63\x74\151\x76\x61\164\x65\x5f\165\x73\145\x72\x5f\x61\x6e\x61\x6c\171\x74\x69\143\163"]) ? stripslashes(wp_unslash($post["\155\x6f\x5f\x61\x63\164\x69\x76\x61\x74\145\x5f\165\x73\x65\162\x5f\x61\156\141\154\x79\164\x69\x63\x73"])) : '');
        $n2->add_config("\144\151\x73\x61\142\154\145\x5f\167\x70\137\x6c\x6f\x67\151\x6e", isset($post["\x6d\x6f\x5f\x6f\x63\137\144\151\x73\141\x62\154\145\x5f\167\x70\x5f\x6c\157\x67\151\x6e"]) ? stripslashes(wp_unslash($post["\155\157\137\157\x63\137\144\x69\163\141\x62\154\x65\x5f\x77\x70\x5f\154\157\147\x69\x6e"])) : '');
        $n2->add_config("\141\x63\164\x69\x76\x61\x74\x65\x5f\163\x69\156\147\x6c\x65\137\x6c\x6f\147\x69\156\137\146\x6c\x6f\167", isset($post["\x6d\157\x5f\x61\x63\x74\x69\166\141\164\x65\137\163\x69\156\x67\x6c\x65\137\154\x6f\147\x69\156\137\x66\154\x6f\167"]) ? stripslashes(wp_unslash($post["\x6d\x6f\137\141\143\x74\x69\x76\x61\164\145\x5f\163\151\x6e\147\x6c\145\x5f\154\x6f\147\x69\x6e\x5f\x66\154\157\x77"])) : '');
        $n2->add_config("\143\157\155\155\x6f\x6e\137\154\x6f\x67\151\x6e\x5f\142\x75\164\x74\x6f\x6e\x5f\x64\151\x73\160\x6c\141\171\x5f\156\141\155\145", isset($post["\x63\x6f\x6d\155\x6f\x6e\137\154\x6f\147\151\x6e\x5f\x62\x75\x74\x74\x6f\x6e\x5f\x64\x69\163\x70\x6c\141\171\137\156\x61\155\x65"]) ? stripslashes(wp_unslash($post["\x63\157\x6d\x6d\x6f\156\137\154\x6f\147\151\156\137\x62\165\164\x74\157\x6e\137\x64\151\163\x70\154\141\x79\x5f\156\x61\x6d\x65"])) : '');
        global $mx;
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\x6f\x61\165\x74\150\137\x63\x6c\x69\x65\156\x74\137\x64\151\x73\x61\142\x6c\145\x5f\x77\x70\x5f\154\x6f\x67\x69\x6e", isset($post["\155\x6f\x5f\157\x63\x5f\144\151\163\141\x62\x6c\x65\137\x77\160\137\154\x6f\147\x69\156"]));
        $mx->mo_oauth_client_update_option("\155\x6f\x5f\x6f\141\165\x74\x68\x5f\143\x6c\151\x65\x6e\164\137\154\157\x61\144\137\x61\156\x61\154\x79\x74\x69\143\163", isset($post["\x6d\x6f\137\x61\x63\x74\151\x76\x61\x74\x65\x5f\x75\x73\145\x72\137\x61\x6e\141\x6c\x79\x74\x69\143\163"]));
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\157\x61\165\x74\x68\x5f\x61\143\164\x69\x76\141\x74\145\137\163\151\x6e\x67\154\x65\137\154\157\x67\x69\x6e\x5f\x66\154\x6f\x77", isset($post["\155\x6f\x5f\x61\x63\x74\151\x76\141\164\145\137\163\x69\156\x67\x6c\145\x5f\154\x6f\147\x69\156\x5f\x66\x6c\x6f\167"]));
        $mx->mo_oauth_client_update_option("\x6d\157\137\x6f\141\x75\x74\x68\x5f\143\157\155\x6d\157\156\137\x6c\x6f\x67\x69\x6e\137\142\x75\x74\x74\x6f\x6e\x5f\x64\151\x73\x70\x6c\141\x79\137\156\141\x6d\x65", isset($post["\143\x6f\x6d\155\157\x6e\137\x6c\157\x67\151\x6e\x5f\x62\x75\164\164\157\156\x5f\144\x69\163\x70\154\x61\171\137\x6e\x61\155\145"]) ? stripslashes(wp_unslash($post["\143\157\x6d\x6d\x6f\156\x5f\x6c\x6f\147\x69\156\x5f\x62\x75\164\164\157\x6e\x5f\x64\151\163\160\x6c\x61\171\x5f\156\141\x6d\145"])) : '');
        $this->db_ops->make_table_if_not_exists();
        do_action("\x6d\157\137\x70\x67\137\141\x64\x64\137\x73\x75\142\x73\x69\164\x65\137\143\x6f\x6c\x5f\x74\157\137\165\x73\x65\162\x5f\141\156\141\154\x79\x74\151\x63\163");
        return $n2;
    }
}
