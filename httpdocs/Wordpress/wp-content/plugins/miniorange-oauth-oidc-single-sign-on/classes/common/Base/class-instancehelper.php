<?php


namespace MoOauthClient\Base;

class InstanceHelper
{
    private $current_version = "\x46\x52\105\105";
    private $utils;
    public function __construct()
    {
        $this->utils = new \MoOauthClient\MOUtils();
        $this->current_version = $this->utils->get_versi_str();
    }
    public function get_sign_in_settings_instance()
    {
        if (class_exists("\x4d\x6f\x4f\141\165\164\150\103\x6c\151\x65\x6e\x74\x5c\x45\156\x74\x65\x72\x70\x72\151\163\x65\134\123\151\x67\x6e\x49\156\x53\x65\x74\x74\x69\156\147\x73") && $this->utils->check_versi(4)) {
            goto MD;
        }
        if (class_exists("\x4d\x6f\x4f\141\165\x74\150\103\x6c\151\x65\156\164\x5c\x50\162\145\x6d\x69\x75\155\134\123\151\147\156\111\x6e\123\x65\x74\164\x69\x6e\x67\163") && $this->utils->check_versi(2)) {
            goto zc;
        }
        if (class_exists("\115\157\x4f\x61\x75\x74\x68\x43\x6c\x69\145\156\x74\134\123\x74\141\x6e\144\x61\162\144\134\x53\151\147\x6e\111\x6e\x53\x65\164\164\151\x6e\147\163") && $this->utils->check_versi(1)) {
            goto ov;
        }
        if (class_exists("\x5c\115\x6f\x4f\141\165\x74\x68\103\x6c\x69\x65\x6e\164\x5c\x46\162\145\x65\134\x53\x69\147\156\111\x6e\x53\145\x74\x74\151\156\147\x73") && $this->utils->check_versi(0)) {
            goto Sg;
        }
        wp_die("\x50\x6c\145\141\x73\x65\40\103\150\141\156\x67\x65\40\124\150\145\x20\166\145\x72\x73\151\x6f\x6e\x20\x62\141\143\x6b\x20\164\157\40\x77\150\141\x74\40\151\x74\x20\x72\x65\x61\154\x6c\171\40\167\141\x73");
        exit;
        goto r6;
        MD:
        return new \MoOauthClient\Enterprise\SignInSettings();
        goto r6;
        zc:
        return new \MoOauthClient\Premium\SignInSettings();
        goto r6;
        ov:
        return new \MoOauthClient\Standard\SignInSettings();
        goto r6;
        Sg:
        return new \MoOauthClient\Free\SignInSettings();
        r6:
    }
    public function get_requestdemo_instance()
    {
        if (!class_exists("\134\x4d\x6f\x4f\141\165\x74\150\103\x6c\x69\x65\x6e\x74\134\106\162\145\x65\134\122\x65\161\165\145\163\x74\x66\x6f\x72\144\145\155\x6f")) {
            goto yF;
        }
        return new \MoOauthClient\Free\Requestfordemo();
        yF:
    }
    public function get_customization_instance()
    {
        if (class_exists("\x4d\x6f\x4f\141\165\164\x68\103\154\151\145\x6e\x74\134\105\156\x74\x65\162\160\162\151\x73\145\134\103\x75\x73\164\x6f\155\151\x7a\x61\x74\151\157\156") && $this->utils->check_versi(4)) {
            goto PF;
        }
        if (class_exists("\x4d\157\x4f\x61\165\164\x68\x43\154\151\x65\156\x74\x5c\120\x72\x65\155\151\165\155\134\103\165\163\164\157\x6d\x69\172\141\164\151\157\156") && $this->utils->check_versi(2)) {
            goto lR;
        }
        if (class_exists("\115\x6f\x4f\141\x75\164\150\103\x6c\151\145\x6e\x74\x5c\123\x74\141\x6e\144\x61\x72\x64\134\103\x75\163\164\x6f\x6d\151\172\x61\x74\151\157\x6e") && $this->utils->check_versi(1)) {
            goto Mn;
        }
        if (class_exists("\x5c\x4d\x6f\117\x61\165\x74\x68\103\154\x69\x65\156\x74\x5c\106\x72\x65\x65\134\x43\x75\x73\164\157\155\x69\172\141\x74\151\157\156") && $this->utils->check_versi(0)) {
            goto DE;
        }
        wp_die("\x50\154\145\141\163\x65\40\103\150\141\156\147\145\40\124\150\145\40\166\145\162\x73\151\157\x6e\40\x62\x61\143\153\x20\x74\x6f\x20\167\150\141\164\x20\151\164\40\x72\x65\x61\154\154\x79\x20\167\141\x73");
        exit;
        goto Ve;
        PF:
        return new \MoOauthClient\Enterprise\Customization();
        goto Ve;
        lR:
        return new \MoOauthClient\Premium\Customization();
        goto Ve;
        Mn:
        return new \MoOauthClient\Standard\Customization();
        goto Ve;
        DE:
        return new \MoOauthClient\Free\Customization();
        Ve:
    }
    public function get_clientappui_instance()
    {
        if (class_exists("\115\157\x4f\141\x75\164\x68\103\x6c\151\x65\156\164\x5c\x45\156\164\145\x72\160\162\x69\x73\145\x5c\x43\x6c\151\145\156\164\101\x70\x70\125\x49") && $this->utils->check_versi(4)) {
            goto BC;
        }
        if (class_exists("\x4d\x6f\117\141\x75\164\150\x43\x6c\151\x65\156\164\x5c\x50\162\145\155\x69\x75\x6d\134\x43\x6c\x69\x65\156\x74\101\160\160\x55\111") && $this->utils->check_versi(2)) {
            goto CU;
        }
        if (class_exists("\115\157\117\x61\165\164\x68\x43\x6c\151\x65\156\x74\x5c\123\164\141\156\144\141\162\144\x5c\x43\154\151\x65\x6e\x74\101\x70\x70\x55\x49") && $this->utils->check_versi(1)) {
            goto ss;
        }
        if (class_exists("\x5c\115\157\x4f\141\x75\x74\150\103\x6c\151\x65\156\x74\x5c\106\162\145\x65\x5c\x43\x6c\151\145\x6e\164\x41\160\x70\125\111") && $this->utils->check_versi(0)) {
            goto Rt;
        }
        wp_die("\120\x6c\145\x61\163\145\x20\x43\x68\141\156\x67\145\x20\124\150\145\40\x76\145\x72\x73\151\x6f\x6e\x20\x62\141\x63\x6b\40\164\157\40\x77\x68\x61\164\x20\151\x74\x20\162\145\x61\154\154\x79\40\167\x61\163");
        exit;
        goto S8;
        BC:
        return new \MoOauthClient\Enterprise\ClientAppUI();
        goto S8;
        CU:
        return new \MoOauthClient\Premium\ClientAppUI();
        goto S8;
        ss:
        return new \MoOauthClient\Standard\ClientAppUI();
        goto S8;
        Rt:
        return new \MoOauthClient\Free\ClientAppUI();
        S8:
    }
    public function get_login_handler_instance()
    {
        if (class_exists("\115\157\x4f\x61\165\x74\x68\x43\154\x69\x65\156\x74\134\x45\156\164\x65\x72\x70\x72\x69\163\145\x5c\114\157\147\151\x6e\x48\141\x6e\x64\x6c\145\162") && $this->utils->check_versi(4)) {
            goto CQ;
        }
        if (class_exists("\x4d\157\117\141\x75\164\150\103\x6c\x69\x65\156\164\x5c\120\x72\145\x6d\151\165\x6d\x5c\x4c\x6f\x67\151\x6e\110\141\156\x64\154\145\162") && $this->utils->check_versi(2)) {
            goto ta;
        }
        if (class_exists("\x4d\157\117\141\165\164\150\103\154\151\x65\x6e\x74\x5c\123\x74\x61\x6e\144\141\162\x64\134\x4c\x6f\147\151\156\x48\x61\x6e\x64\154\145\x72") && $this->utils->check_versi(1)) {
            goto Je;
        }
        if (class_exists("\134\115\157\x4f\141\165\x74\x68\103\154\151\145\156\x74\x5c\114\157\x67\x69\156\110\x61\x6e\144\154\x65\x72") && $this->utils->check_versi(0)) {
            goto ew;
        }
        wp_die("\120\154\145\x61\x73\x65\x20\x43\x68\141\x6e\x67\x65\x20\124\x68\x65\40\166\145\162\163\x69\x6f\x6e\40\142\x61\143\153\40\164\x6f\40\167\150\x61\164\x20\151\x74\40\x72\x65\x61\x6c\x6c\x79\x20\x77\141\163");
        exit;
        goto i4;
        CQ:
        return new \MoOauthClient\Enterprise\LoginHandler();
        goto i4;
        ta:
        return new \MoOauthClient\Premium\LoginHandler();
        goto i4;
        Je:
        return new \MoOauthClient\Standard\LoginHandler();
        goto i4;
        ew:
        return new \MoOauthClient\LoginHandler();
        i4:
    }
    public function get_settings_instance()
    {
        if (class_exists("\115\x6f\x4f\x61\x75\164\x68\x43\x6c\x69\145\156\x74\x5c\105\x6e\164\145\x72\x70\162\x69\163\145\134\105\156\x74\145\x72\160\x72\151\163\145\x53\145\164\x74\151\156\x67\163") && $this->utils->check_versi(4)) {
            goto gX;
        }
        if (class_exists("\x4d\x6f\x4f\x61\x75\164\x68\x43\154\x69\145\156\164\134\120\162\145\155\x69\165\x6d\x5c\x50\x72\145\x6d\x69\x75\155\x53\x65\x74\164\151\156\x67\x73") && $this->utils->check_versi(2)) {
            goto ff;
        }
        if (class_exists("\x4d\x6f\117\141\x75\x74\150\103\x6c\151\x65\156\x74\134\x53\x74\x61\156\144\141\x72\x64\x5c\x53\x74\141\x6e\x64\141\162\x64\x53\145\164\x74\x69\x6e\x67\163") && $this->utils->check_versi(1)) {
            goto EQ;
        }
        if (class_exists("\115\x6f\x4f\x61\x75\164\x68\x43\154\151\x65\x6e\x74\x5c\x46\162\145\x65\134\x46\162\x65\145\123\145\164\164\x69\156\x67\163") && $this->utils->check_versi(0)) {
            goto U_;
        }
        wp_die("\x50\x6c\x65\141\163\x65\40\x43\150\141\x6e\x67\x65\x20\x54\x68\x65\x20\166\x65\162\163\151\x6f\x6e\40\x62\x61\143\153\x20\164\x6f\40\167\150\141\x74\40\151\x74\40\x72\145\x61\x6c\154\x79\40\x77\141\163");
        exit;
        goto Cz;
        gX:
        return new \MoOauthClient\Enterprise\EnterpriseSettings();
        goto Cz;
        ff:
        return new \MoOauthClient\Premium\PremiumSettings();
        goto Cz;
        EQ:
        return new \MoOauthClient\Standard\StandardSettings();
        goto Cz;
        U_:
        return new \MoOauthClient\Free\FreeSettings();
        Cz:
    }
    public function get_accounts_instance()
    {
        if (class_exists("\x4d\157\117\x61\x75\164\150\x43\x6c\x69\145\x6e\x74\134\x50\x61\x69\144\134\101\x63\143\157\165\x6e\164\163") && $this->utils->check_versi(1)) {
            goto Dq;
        }
        return new \MoOauthClient\Accounts();
        goto kF;
        Dq:
        return new \MoOauthClient\Paid\Accounts();
        kF:
    }
    public function get_subsite_settings()
    {
        if (class_exists("\115\157\x4f\x61\x75\164\x68\x43\x6c\151\145\x6e\x74\134\x50\162\145\x6d\x69\x75\155\x5c\115\165\154\164\151\163\x69\164\145\x53\x65\164\x74\151\156\x67\x73") && $this->utils->is_multisite_versi(5)) {
            goto ZN;
        }
        wp_die("\120\154\145\141\x73\145\40\x43\x68\141\156\147\145\x20\124\150\145\x20\166\145\x72\x73\x69\x6f\x6e\40\142\141\143\x6b\x20\x74\157\x20\x77\150\x61\164\x20\151\164\x20\x72\x65\x61\x6c\154\171\x20\x77\141\x73");
        exit;
        goto rz;
        ZN:
        return new \MoOauthClient\Premium\MultisiteSettings();
        rz:
    }
    public function get_user_analytics()
    {
        if (class_exists("\x4d\x6f\x4f\x61\165\x74\150\x43\x6c\151\x65\x6e\x74\134\x45\156\x74\x65\162\160\x72\151\163\x65\134\x55\163\x65\162\x41\x6e\x61\x6c\x79\164\x69\143\163") && $this->utils->check_versi(4)) {
            goto C5;
        }
        wp_die("\120\154\145\x61\x73\x65\x20\103\x68\x61\156\x67\x65\40\x54\150\145\x20\166\x65\162\x73\151\157\156\40\x62\x61\x63\x6b\40\x74\x6f\40\x77\150\x61\x74\x20\x69\164\x20\162\x65\141\154\154\x79\40\x77\141\163");
        exit;
        goto fW;
        C5:
        return new \MoOauthClient\Enterprise\UserAnalytics();
        fW:
    }
    public function get_utils_instance()
    {
        if (!(class_exists("\115\157\x4f\141\x75\164\150\x43\154\151\145\x6e\164\134\123\x74\x61\156\144\141\x72\144\x5c\115\117\125\x74\151\x6c\x73") && $this->utils->check_versi(1))) {
            goto iX;
        }
        return new \MoOauthClient\Standard\MOUtils();
        iX:
        return $this->utils;
    }
}
