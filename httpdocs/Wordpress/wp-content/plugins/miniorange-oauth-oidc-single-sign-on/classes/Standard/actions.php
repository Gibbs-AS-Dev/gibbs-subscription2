<?php


use MoOauthClient\MO_Oauth_Debug;
function mo_oauth_client_auto_redirect_external_after_wp_logout($b2)
{
    MO_Oauth_Debug::mo_oauth_log("\x49\x6e\x73\x69\144\145\40\x77\160\40\154\157\147\157\165\x74");
    global $mx;
    $n2 = $mx->get_plugin_config();
    if (!(!empty($n2->get_config("\141\x66\164\145\162\x5f\154\157\x67\157\x75\164\137\165\162\x6c")) && (isset($_COOKIE["\155\157\137\x6f\141\x75\x74\150\137\x6c\157\147\151\x6e\x5f\x61\160\x70\137\163\x65\163\x73\x69\x6f\156"]) && $_COOKIE["\155\x6f\137\157\x61\x75\x74\x68\137\154\x6f\147\151\156\137\x61\x70\x70\x5f\x73\x65\x73\x73\151\x6f\x6e"] != "\156\x6f\x6e\145"))) {
        goto a5K;
    }
    $user = get_userdata($b2);
    $VA = $n2->get_config("\x61\146\164\145\162\x5f\x6c\x6f\147\157\165\x74\137\x75\x72\x6c");
    MO_Oauth_Debug::mo_oauth_log("\x75\x73\145\x72\40\75\x3d\76\40");
    MO_Oauth_Debug::mo_oauth_log($b2);
    $JX = get_user_meta($b2, "\x6d\157\137\157\141\165\164\x68\137\x63\154\151\145\x6e\x74\137\x6c\x61\163\164\137\x69\144\137\x74\x6f\153\x65\x6e", true);
    MO_Oauth_Debug::mo_oauth_log("\x69\144\x20\164\x6f\x6b\x65\156\40\75\x3d\x3e\40");
    MO_Oauth_Debug::mo_oauth_log($JX);
    $VA = str_replace("\43\43\x69\144\x5f\x74\157\x6b\x65\156\43\43", $JX, $VA);
    $VA = str_replace("\43\x23\x6d\x6f\137\160\x6f\x73\x74\137\x6c\x6f\147\157\x75\x74\137\165\162\x69\43\43", site_url(), $VA);
    do_action("\155\157\137\157\x61\165\164\x68\x5f\x72\145\144\x69\162\145\x63\x74\137\157\141\x75\x74\150\137\165\x73\145\x72\163", $user, $VA);
    wp_redirect($VA);
    exit;
    a5K:
    setcookie("\x6d\x6f\137\157\x61\165\x74\150\137\x6c\157\x67\151\156\x5f\x61\160\x70\x5f\x73\145\x73\x73\x69\157\x6e", "\x6e\x6f\156\145");
}
function mo_oauth_client_auto_redirect_external_after_logout($pG, $Ol, $user)
{
    $mx = new \MoOauthClient\Standard\MOUtils();
    $n2 = $mx->get_plugin_config();
    if (!(!empty($n2->get_config("\x61\146\164\145\162\x5f\154\157\x67\x6f\x75\x74\137\x75\162\x6c")) && (isset($_COOKIE["\x6d\x6f\x5f\157\x61\x75\x74\x68\x5f\154\x6f\x67\x69\156\x5f\x61\x70\160\x5f\163\145\x73\x73\151\x6f\x6e"]) && $_COOKIE["\x6d\157\x5f\x6f\x61\x75\x74\150\x5f\x6c\x6f\147\x69\x6e\137\x61\x70\160\137\163\x65\x73\163\x69\x6f\x6e"] != "\x6e\x6f\x6e\145"))) {
        goto qzU;
    }
    $VA = $n2->get_config("\141\146\164\145\x72\137\x6c\x6f\x67\157\165\x74\137\x75\x72\x6c");
    $b2 = $user->ID;
    $JX = get_user_meta($b2, "\155\157\x5f\157\x61\x75\x74\x68\x5f\143\x6c\x69\145\x6e\164\137\154\141\163\x74\x5f\x69\144\137\x74\x6f\x6b\145\x6e", true);
    $VA = str_replace("\x23\x23\x69\144\x5f\164\x6f\x6b\x65\156\43\x23", $JX, $VA);
    $VA = str_replace("\43\x23\155\157\137\x70\157\x73\164\x5f\x6c\x6f\147\157\165\x74\x5f\165\x72\151\43\43", site_url(), $VA);
    do_action("\x6d\x6f\x5f\157\141\x75\164\x68\137\x72\145\x64\151\x72\x65\x63\164\137\157\141\165\164\150\137\165\x73\x65\x72\x73", $user, $VA);
    wp_redirect($VA);
    exit;
    qzU:
    setcookie("\155\x6f\137\x6f\x61\x75\x74\150\137\154\157\147\151\156\x5f\141\x70\160\137\x73\x65\163\163\151\157\x6e", "\x6e\157\156\x65");
    return $pG;
}
add_filter("\167\160\x5f\154\157\147\x6f\165\164", "\155\x6f\137\x6f\141\x75\164\x68\137\143\154\x69\145\x6e\x74\137\141\165\164\157\x5f\x72\x65\144\151\x72\x65\x63\164\137\x65\170\164\x65\162\156\141\154\137\141\x66\x74\145\x72\137\x77\160\x5f\154\x6f\x67\157\165\x74", 10, 1);
add_filter("\154\157\x67\x6f\165\x74\x5f\x72\x65\144\151\162\x65\x63\x74", "\155\x6f\137\157\141\165\x74\150\137\143\x6c\151\145\156\164\137\141\165\x74\157\137\x72\x65\144\151\x72\145\143\164\137\x65\x78\164\145\x72\x6e\x61\154\x5f\x61\x66\164\x65\162\137\154\157\147\x6f\165\x74", 10, 3);
