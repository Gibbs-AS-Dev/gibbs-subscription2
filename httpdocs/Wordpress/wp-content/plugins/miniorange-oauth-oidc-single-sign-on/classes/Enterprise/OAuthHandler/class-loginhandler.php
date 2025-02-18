<?php


namespace MoOauthClient\Enterprise;

use MoOauthClient\Premium\LoginHandler as PremiumLoginHandler;
use MoOauthClient\Enterprise\UserAnalyticsDBOps;
use MoOauthClient\MO_Oauth_Debug;
class LoginHandler extends PremiumLoginHandler
{
    public function mo_oauth_client_generate_authorization_url($nz, $pY)
    {
        global $mx;
        $nz = parent::mo_oauth_client_generate_authorization_url($nz, $pY);
        $uX = $mx->parse_url($nz);
        $n2 = $mx->get_plugin_config();
        $v9 = $n2->get_config("\144\x79\x6e\x61\x6d\151\143\x5f\143\141\x6c\x6c\142\x61\x63\x6b\x5f\165\162\154");
        if (!(isset($v9) && '' !== $v9)) {
            goto Jl;
        }
        $uX["\x71\x75\x65\162\x79"]["\162\x65\x64\151\162\x65\143\x74\x5f\x75\162\x69"] = $v9;
        return $mx->generate_url($uX);
        Jl:
        return $nz;
    }
    public function check_status($x1, $Aj)
    {
        global $mx;
        $vV = new UserAnalyticsDBOps();
        if (isset($x1["\x73\164\x61\164\x75\163"])) {
            goto gk;
        }
        if (!$Aj) {
            goto Q7;
        }
        $vV->add_transact($x1, true);
        Q7:
        $mx->handle_error("\123\x6f\155\x65\164\x68\151\x6e\147\x20\x77\x65\x6e\x74\40\x77\162\157\x6e\147\56\x20\120\154\x65\x61\x73\x65\x20\x74\x72\x79\x20\x4c\157\x67\x67\x69\x6e\x67\40\151\x6e\x20\141\x67\x61\x69\156\56");
        MO_Oauth_Debug::mo_oauth_log("\123\x6f\x6d\x65\164\x68\x69\x6e\147\x20\167\x65\156\x74\40\167\x72\157\156\147\56\x20\x50\154\145\141\x73\145\x20\x74\162\x79\x20\114\157\147\147\151\156\x67\x20\151\x6e\x20\141\147\141\x69\156\x2e");
        wp_die(wp_kses("\x53\x6f\155\x65\164\150\151\156\147\40\x77\145\x6e\164\40\167\162\x6f\x6e\x67\x2e\40\x50\x6c\145\141\x73\145\x20\164\x72\x79\x20\x4c\x6f\x67\147\x69\156\147\x20\151\x6e\x20\141\147\141\151\x6e\56", \mo_oauth_get_valid_html()));
        gk:
        if (!$Aj) {
            goto s0;
        }
        $vV->add_transact($x1);
        s0:
        if (!(true !== $x1["\163\164\141\x74\165\163"])) {
            goto Le;
        }
        $l5 = isset($x1["\155\163\147"]) && !empty($x1["\x6d\x73\x67"]) ? $x1["\155\163\147"] : "\x53\157\x6d\x65\x74\x68\151\156\147\40\167\x65\x6e\164\x20\x77\x72\x6f\156\147\56\40\120\154\x65\x61\163\x65\40\x74\162\x79\40\114\157\x67\147\151\x6e\147\x20\151\x6e\x20\x61\x67\x61\x69\x6e\x2e";
        $mx->handle_error($l5);
        MO_Oauth_Debug::mo_oauth_log($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        Le:
    }
}
