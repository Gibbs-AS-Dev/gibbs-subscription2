<?php


namespace MoOauthClient\Premium;

use MoOauthClient\Standard\LoginHandler as StandardLoginHandler;
use MoOauthClient\GrantTypes\Implicit;
use MoOauthClient\GrantTypes\Password;
use MoOauthClient\GrantTypes\JWSVerify;
use MoOauthClient\GrantTypes\JWTUtils;
use MoOauthClient\Premium\MappingHandler;
use MoOauthClient\StorageManager;
use MoOauthClient\MO_Oauth_Debug;
class LoginHandler extends StandardLoginHandler
{
    private $implicit_handler;
    private $app_name = '';
    private $group_mapping_attr = false;
    private $resource_owner = false;
    public function __construct()
    {
        global $mx;
        parent::__construct();
        add_filter("\x6d\x6f\x5f\141\x75\164\150\x5f\165\x72\154\137\x69\x6e\164\145\x72\x6e\x61\x6c", array($this, "\x6d\157\x5f\157\x61\x75\164\150\x5f\143\x6c\151\x65\156\164\x5f\x67\x65\156\x65\x72\141\164\145\137\141\x75\x74\150\x6f\x72\151\x7a\141\164\x69\157\156\137\x75\x72\154"), 5, 2);
        add_action("\x77\x70\137\x66\157\x6f\x74\x65\162", array($this, "\x6d\157\x5f\x6f\x61\165\164\150\137\x63\x6c\151\x65\x6e\164\137\x69\155\160\x6c\x69\x63\151\x74\137\x66\162\141\147\155\145\x6e\164\137\x68\141\x6e\144\x6c\145\162"));
        add_action("\155\157\x5f\157\x61\x75\164\x68\137\162\x65\163\x74\x72\x69\143\x74\137\145\155\141\151\154\x73", array($this, "\x6d\x6f\x5f\x6f\x61\165\x74\x68\x5f\143\x6c\151\x65\156\164\137\x72\x65\x73\x74\x72\x69\143\x74\x5f\x65\155\141\151\x6c\x73"), 10, 2);
        add_action("\x6d\157\x5f\157\x61\x75\x74\150\x5f\143\x6c\151\145\x6e\164\x5f\x6d\141\x70\137\162\157\x6c\x65\163", array($this, "\155\x6f\137\157\x61\165\x74\x68\x5f\x63\154\x69\x65\156\164\137\155\141\x70\137\x72\x6f\154\145\x73"), 10, 1);
        $Zb = $mx->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\165\164\x68\x5f\x65\x6e\x61\x62\154\x65\137\157\141\x75\x74\150\137\x77\160\137\x6c\x6f\147\151\x6e");
        if (!$Zb) {
            goto kxb;
        }
        remove_filter("\141\x75\164\150\x65\156\164\151\x63\x61\x74\x65", "\167\x70\137\141\x75\164\150\145\x6e\x74\x69\x63\x61\164\145\x5f\165\163\x65\162\156\x61\x6d\145\x5f\160\x61\163\163\167\x6f\162\144", 20, 3);
        $MU = new Password(true);
        add_filter("\x61\165\164\150\145\156\x74\151\x63\x61\x74\x65", array($MU, "\155\157\x5f\x6f\x61\x75\164\150\x5f\167\160\x5f\x6c\157\x67\151\156"), 20, 3);
        kxb:
    }
    public function mo_oauth_client_restrict_emails($UU, $n2)
    {
        global $mx;
        $lz = isset($n2["\x72\145\163\x74\162\x69\x63\x74\145\x64\x5f\144\x6f\155\141\151\x6e\163"]) ? $n2["\162\145\163\x74\x72\x69\143\x74\145\144\x5f\x64\157\x6d\x61\x69\x6e\163"] : '';
        if (!empty($lz)) {
            goto KD_;
        }
        return;
        KD_:
        $Se = isset($n2["\141\154\x6c\157\x77\137\x72\145\x73\x74\162\151\143\x74\145\x64\137\x64\x6f\155\x61\151\x6e\x73"]) ? $n2["\141\154\x6c\157\x77\137\162\145\163\164\162\151\x63\164\145\144\x5f\x64\157\155\141\151\x6e\163"] : '';
        if (!empty($Se)) {
            goto bxi;
        }
        $Se = false;
        bxi:
        $Se = intval($Se);
        $lz = array_map("\164\x72\151\155", explode("\x2c", $lz));
        $Vm = substr($UU, strpos($UU, "\100") + 1);
        $w_ = in_array($Vm, $lz, false);
        $w_ = $Se ? !$w_ : $w_;
        $Y4 = !empty($lz) && $w_;
        if (!$Y4) {
            goto gRE;
        }
        $lM = "\x59\x6f\165\x20\144\x6f\40\156\157\x74\x20\x68\x61\166\x65\x20\162\151\x67\150\164\163\x20\164\x6f\40\141\143\x63\145\x73\x73\40\x74\150\151\x73\x20\160\141\147\145\56\40\120\x6c\x65\x61\163\145\x20\x63\x6f\156\x74\141\143\164\x20\x74\150\x65\x20\x61\144\155\151\156\x69\x73\164\x72\x61\164\157\x72\56";
        $mx->handle_error($lM);
        wp_die($lM);
        gRE:
    }
    public function mo_oauth_client_generate_authorization_url($nz, $pY)
    {
        global $mx;
        $uX = $mx->parse_url($nz);
        $n2 = $mx->get_app_by_name($pY)->get_app_config();
        $rJ = md5(rand());
        setcookie("\x6d\x6f\x5f\x6f\141\x75\164\150\137\156\157\156\x63\x65", $rJ, time() + 120, "\x2f", null, true, true);
        if (isset($n2["\147\162\x61\x6e\164\x5f\x74\171\160\145"]) && "\x49\x6d\x70\154\151\143\x69\x74\40\107\x72\141\x6e\164" === $n2["\x67\162\141\x6e\164\137\164\x79\160\x65"]) {
            goto ZMX;
        }
        if (!(isset($n2["\147\x72\x61\x6e\164\x5f\x74\171\x70\x65"]) && "\110\171\x62\x72\151\144\40\x47\x72\141\x6e\x74" === $n2["\147\x72\x61\156\164\x5f\164\171\x70\145"])) {
            goto B8C;
        }
        MO_Oauth_Debug::mo_oauth_log("\x47\x72\x61\x6e\x74\x3a\x20\x48\x79\x62\162\x69\x64\x20\107\x72\x61\156\164");
        $uX["\161\x75\x65\x72\x79"]["\x72\145\163\160\157\156\163\145\137\164\171\160\145"] = "\164\157\x6b\x65\156\45\62\x30\x69\x64\x5f\164\157\153\145\156\x25\x32\60\143\x6f\x64\145";
        return $mx->generate_url($uX);
        B8C:
        goto qkU;
        ZMX:
        $uX["\x71\x75\145\162\x79"]["\x6e\157\x6e\x63\145"] = $rJ;
        $uX["\x71\165\145\x72\171"]["\x72\145\x73\160\x6f\x6e\163\145\x5f\164\x79\x70\x65"] = "\x74\157\153\x65\x6e";
        $Kq = isset($n2["\x6d\x6f\137\157\x61\x75\164\150\x5f\162\145\163\160\157\x6e\x73\x65\x5f\x74\x79\160\x65"]) && !empty($n2["\155\157\x5f\x6f\x61\165\x74\150\137\x72\145\163\160\x6f\x6e\x73\x65\137\164\171\x70\x65"]) ? $n2["\x6d\157\137\157\141\x75\x74\x68\x5f\162\145\163\160\157\x6e\163\145\x5f\164\x79\160\x65"] : "\x74\x6f\153\x65\x6e";
        $uX["\161\x75\145\x72\x79"]["\162\145\x73\x70\x6f\x6e\x73\145\137\x74\x79\160\x65"] = $Kq;
        return $mx->generate_url($uX);
        qkU:
        return $nz;
    }
    public function mo_oauth_client_map_roles($x1)
    {
        $xA = isset($x1["\x61\160\x70\x5f\143\x6f\x6e\146\151\x67"]) && !empty($x1["\x61\160\160\x5f\x63\157\x6e\x66\x69\x67"]) ? $x1["\141\x70\x70\137\143\157\x6e\146\x69\147"] : [];
        $sY = isset($xA["\147\x72\157\165\x70\x6e\141\155\x65\x5f\141\x74\164\x72\151\x62\165\164\x65"]) && '' !== $xA["\x67\162\x6f\165\x70\156\x61\x6d\145\137\x61\164\164\x72\x69\x62\165\164\x65"] ? $xA["\147\162\157\165\160\x6e\x61\155\145\137\141\164\164\162\151\x62\165\x74\145"] : false;
        global $mx;
        $zw = false;
        if (isset($xA["\145\156\x61\142\x6c\145\x5f\162\x6f\154\x65\x5f\x6d\x61\160\160\x69\156\147"])) {
            goto q4Q;
        }
        $xA["\x65\156\141\142\154\145\137\x72\157\154\x65\x5f\x6d\141\x70\x70\x69\156\x67"] = true;
        $zw = true;
        q4Q:
        if (isset($xA["\x5f\x6d\141\160\160\151\x6e\147\137\166\141\x6c\x75\x65\x5f\144\145\x66\x61\x75\154\164"])) {
            goto Lwl;
        }
        $xA["\137\x6d\141\160\160\151\156\x67\x5f\x76\x61\x6c\x75\145\x5f\144\145\x66\141\165\x6c\164"] = "\x73\x75\142\x73\x63\162\151\142\145\162";
        $zw = true;
        Lwl:
        if (!boolval($zw)) {
            goto Fbg;
        }
        if (!(isset($xA["\143\154\151\x65\x6e\164\x5f\x63\162\x65\x64\163\137\145\156\143\162\x70\171\164\145\144"]) && boolval($xA["\x63\154\x69\145\156\164\x5f\143\162\145\144\x73\x5f\x65\156\x63\x72\160\x79\164\145\144"]))) {
            goto eAu;
        }
        $xA["\143\154\x69\145\x6e\x74\x5f\151\144"] = $mx->mooauthencrypt($xA["\143\x6c\x69\145\x6e\x74\x5f\151\x64"]);
        $xA["\x63\x6c\x69\145\156\x74\x5f\x73\x65\x63\x72\x65\x74"] = $mx->mooauthencrypt($xA["\x63\x6c\x69\x65\x6e\164\137\x73\145\x63\x72\x65\x74"]);
        eAu:
        $mx->set_app_by_name($x1["\x61\x70\160\137\x6e\141\x6d\145"], $xA);
        Fbg:
        $this->resource_owner = isset($x1["\162\x65\x73\157\165\162\143\145\137\157\x77\156\x65\162"]) && !empty($x1["\162\145\163\157\165\x72\x63\145\137\157\167\x6e\145\x72"]) ? $x1["\x72\145\x73\x6f\x75\162\x63\x65\x5f\x6f\x77\x6e\x65\x72"] : [];
        $this->group_mapping_attr = $this->get_group_mapping_attribute($this->resource_owner, false, $sY);
        if (!(isset($xA["\x65\170\x74\x72\x61\x63\x74\137\x65\155\x61\x69\154\x5f\x64\157\x6d\x61\151\x6e\x5f\x66\157\x72\x5f\x72\x6f\x6c\x65\155\141\160\160\x69\156\x67"]) && boolval($xA["\145\170\x74\162\141\143\x74\x5f\145\155\141\x69\154\137\144\x6f\x6d\x61\151\156\x5f\146\157\x72\137\x72\x6f\154\x65\155\141\160\x70\x69\x6e\x67"]))) {
            goto ezA;
        }
        if (!is_array($this->group_mapping_attr) && is_email($this->group_mapping_attr)) {
            goto dOk;
        }
        MO_Oauth_Debug::mo_oauth_log("\105\155\x61\151\x6c\40\x61\144\x64\x72\145\163\x73\40\156\157\x74\x20\x72\x65\x63\x65\151\166\x65\x64\40\x69\156\40\164\150\145\40\143\157\156\x66\151\x67\x75\x72\x65\x64\40\147\x72\x6f\165\160\x20\x61\164\164\x72\151\x62\165\164\x65\x20\x6e\x61\x6d\145\x20\x61\x73\40\x74\150\x65\x20\x6f\x70\164\151\157\x6e\x20\151\163\40\x65\x6e\141\142\154\x65\144\40\x74\x6f\40\145\x78\164\162\141\143\164\x20\144\x6f\155\141\151\x6e\40\x77\x68\145\x6e\x20\x65\155\x61\x69\154\x20\151\163\x20\x6d\141\x70\x70\x65\x64\x20\x66\157\162\40\x72\x6f\154\x65\x20\155\141\x70\160\151\156\147\56\40\103\150\145\143\x6b\x20\171\x6f\165\x72\40\x52\157\154\x65\x20\x4d\141\160\x70\151\x6e\147\40\143\x6f\156\146\151\147\165\162\141\x74\x69\157\x6e\56");
        wp_die("\105\155\x61\151\154\x20\144\x6f\155\x61\151\x6e\x20\x6e\157\164\40\162\x65\x63\145\151\166\145\x64\x2e\40\x43\150\x65\x63\x6b\x20\171\157\x75\x72\x20\74\163\164\x72\x6f\x6e\147\x3e\x52\157\154\145\x20\115\141\x70\160\x69\x6e\147\74\x2f\x73\164\162\157\156\x67\x3e\x20\143\157\156\x66\x69\147\x75\162\141\x74\x69\157\x6e\56");
        goto n4E;
        dOk:
        $this->group_mapping_attr = substr($this->group_mapping_attr, strpos($this->group_mapping_attr, "\100") + 1);
        n4E:
        ezA:
        MO_Oauth_Debug::mo_oauth_log("\107\x72\157\x75\160\x20\115\141\160\160\x69\x6e\147\x20\101\164\x74\162\x69\142\165\164\145\163\40\75\76\x20" . $sY);
        $Hp = new MappingHandler(isset($x1["\x75\x73\x65\162\137\151\x64"]) && is_numeric($x1["\165\x73\145\162\137\151\x64"]) ? intval($x1["\165\163\145\162\x5f\x69\x64"]) : 0, $xA, $this->group_mapping_attr ? $this->group_mapping_attr : '', isset($x1["\156\145\167\137\165\x73\x65\162"]) ? \boolval($x1["\x6e\x65\x77\137\165\163\145\x72"]) : true);
        $n2 = $x1["\143\x6f\156\x66\x69\147"];
        $QN = $x1["\156\145\x77\137\165\x73\145\162"];
        if (!($QN || (!isset($n2["\x6b\145\145\160\x5f\x65\x78\x69\163\x74\151\x6e\147\x5f\x75\163\145\162\163"]) || 1 !== intval($n2["\x6b\x65\145\160\x5f\x65\170\151\163\164\x69\156\147\137\x75\163\145\162\x73"])))) {
            goto Bcg;
        }
        $Hp->apply_custom_attribute_mapping(is_array($this->resource_owner) ? $this->resource_owner : []);
        Bcg:
        $SH = false;
        $SH = apply_filters("\155\x6f\137\157\x61\165\x74\x68\137\x63\154\151\x65\156\x74\x5f\165\x70\144\x61\x74\x65\137\x61\x64\x6d\x69\x6e\x5f\x72\x6f\154\x65", $SH);
        if (!$SH) {
            goto jNk;
        }
        MO_Oauth_Debug::mo_oauth_log("\101\x64\155\x69\x6e\x20\x52\157\154\x65\x20\167\x69\x6c\x6c\x20\x62\x65\x20\165\160\x64\x61\164\145\144");
        jNk:
        if (!(user_can($x1["\x75\x73\x65\x72\x5f\x69\144"], "\x61\144\x6d\x69\x6e\151\x73\x74\162\141\x74\157\x72") && !$SH)) {
            goto ktE;
        }
        return;
        ktE:
        $Hp->apply_role_mapping(is_array($this->resource_owner) ? $this->resource_owner : []);
    }
    public function mo_oauth_client_implicit_fragment_handler()
    {
        echo "\x9\x9\x9\74\x73\x63\x72\x69\160\x74\x3e\15\xa\x9\x9\11\x9\x66\x75\x6e\x63\164\151\157\x6e\x20\143\157\x6e\x76\x65\x72\164\x5f\x74\157\x5f\x75\x72\x6c\50\157\x62\x6a\51\40\173\xd\12\11\x9\11\11\11\x72\x65\164\165\x72\x6e\x20\x4f\x62\x6a\x65\143\164\xd\xa\11\11\11\11\11\56\153\145\x79\x73\x28\157\142\152\51\xd\12\11\11\11\x9\11\x2e\x6d\x61\160\50\153\x20\75\76\40\140\44\173\145\156\x63\x6f\144\145\125\x52\x49\103\x6f\x6d\x70\157\x6e\145\156\164\50\153\51\x7d\75\44\x7b\x65\156\143\157\144\145\125\x52\111\103\x6f\x6d\x70\157\156\x65\x6e\164\x28\157\x62\x6a\x5b\153\135\51\175\x60\x29\15\12\11\11\x9\x9\11\x2e\x6a\157\x69\156\x28\x27\x26\47\51\x3b\15\12\11\11\x9\x9\x7d\xd\xa\xd\12\x9\11\11\x9\146\165\x6e\143\x74\x69\157\x6e\x20\x70\x61\x73\163\137\164\x6f\137\x62\x61\x63\153\x65\x6e\x64\x28\51\x20\x7b\xd\12\x9\11\x9\11\11\151\x66\50\167\x69\x6e\x64\x6f\x77\56\154\x6f\143\x61\164\x69\157\156\56\x68\x61\163\150\51\x20\173\15\xa\11\x9\x9\11\x9\11\166\x61\162\40\x68\x61\x73\x68\40\75\x20\167\x69\x6e\x64\x6f\167\x2e\154\157\x63\141\164\x69\157\156\x2e\150\x61\x73\x68\x3b\15\xa\11\11\x9\11\x9\11\x76\141\162\40\145\154\x65\155\145\156\x74\x73\40\x3d\x20\x7b\175\73\15\12\11\11\x9\11\11\x9\150\141\163\150\x2e\163\160\x6c\x69\164\50\x22\43\x22\51\133\61\135\x2e\x73\x70\154\151\x74\x28\42\x26\42\51\x2e\x66\157\162\105\x61\x63\x68\50\x65\154\x65\155\x65\156\x74\40\x3d\76\40\x7b\xd\xa\11\x9\11\11\11\x9\11\x76\141\162\x20\166\x61\162\163\40\75\x20\145\154\145\x6d\145\x6e\164\56\163\160\154\x69\164\x28\42\x3d\x22\x29\x3b\15\12\x9\x9\x9\11\x9\x9\11\x65\x6c\x65\155\x65\156\164\163\x5b\166\141\162\163\x5b\60\135\x5d\x20\x3d\x20\x76\141\162\x73\x5b\x31\x5d\73\xd\12\11\x9\11\11\11\11\x7d\51\x3b\xd\xa\x9\x9\x9\x9\11\x9\151\146\50\50\x22\x61\143\x63\x65\x73\x73\x5f\164\x6f\x6b\145\156\x22\40\151\156\x20\x65\x6c\x65\155\x65\x6e\x74\x73\x29\40\x7c\x7c\x20\x28\42\x69\144\137\x74\x6f\153\145\156\42\x20\151\x6e\x20\x65\x6c\x65\x6d\x65\156\164\163\x29\x20\x7c\x7c\40\x28\42\x74\157\x6b\145\156\x22\40\x69\x6e\40\x65\154\145\x6d\145\x6e\164\x73\51\x29\x20\173\xd\12\11\x9\11\x9\x9\11\x9\151\146\50\x77\x69\x6e\x64\x6f\x77\x2e\154\157\143\141\164\151\157\156\56\150\162\x65\x66\x2e\151\x6e\144\145\x78\117\146\x28\42\x3f\42\x29\40\x21\75\75\x20\55\61\51\40\x7b\15\12\x9\11\x9\11\x9\11\11\x9\x77\151\x6e\144\157\167\x2e\154\x6f\143\141\164\151\x6f\156\x20\x3d\40\50\x77\x69\x6e\x64\x6f\167\56\x6c\x6f\x63\141\x74\151\x6f\156\x2e\150\x72\145\146\x2e\x73\160\154\x69\164\50\42\x3f\x22\x29\x5b\60\x5d\40\53\x20\x77\x69\x6e\144\x6f\x77\56\154\157\143\141\164\151\x6f\156\x2e\x68\141\x73\x68\51\x2e\x73\160\154\x69\164\x28\47\43\47\51\x5b\60\x5d\x20\x2b\x20\x22\77\42\40\53\x20\x63\157\x6e\x76\145\162\x74\137\x74\x6f\x5f\165\x72\x6c\x28\145\x6c\x65\155\x65\x6e\164\x73\x29\73\xd\xa\11\11\x9\x9\x9\x9\11\x7d\x20\145\154\x73\145\40\x7b\15\12\11\11\x9\11\x9\11\x9\x9\167\151\156\x64\157\167\x2e\x6c\x6f\x63\x61\164\x69\157\x6e\40\x3d\x20\167\x69\156\x64\157\x77\56\154\157\x63\141\x74\x69\x6f\156\56\150\162\x65\146\56\163\x70\154\151\164\x28\47\43\x27\51\x5b\x30\135\40\53\40\x22\x3f\x22\40\x2b\x20\x63\157\x6e\x76\145\x72\x74\x5f\x74\157\x5f\165\162\x6c\x28\x65\154\x65\x6d\145\156\x74\x73\51\73\15\xa\x9\11\11\x9\x9\x9\x9\175\xd\12\11\11\x9\x9\x9\x9\175\xd\12\x9\x9\11\x9\x9\x7d\15\xa\x9\11\11\11\x7d\15\12\xd\xa\x9\x9\11\x9\x70\141\163\163\137\164\x6f\137\142\141\143\153\145\156\x64\x28\x29\73\15\12\11\x9\x9\74\x2f\x73\x63\162\151\160\x74\x3e\15\xa\15\xa\11\11";
    }
    private function check_state($QP)
    {
        global $mx;
        $Ql = str_replace("\x25\63\144", "\75", urldecode($QP->get_query_param("\163\x74\141\x74\x65")));
        $Nh = new StorageManager($Ql);
        $vP = $Nh->get_value("\x61\160\x70\x6e\x61\x6d\x65");
        $xA = $mx->get_app_by_name($vP)->get_app_config();
        $bj = $xA["\x61\x70\x70\111\144"];
        $Zy = $mx->get_app_by_name($bj);
        if (empty($Ql)) {
            goto b9C;
        }
        $Ql = isset($_GET["\163\164\141\x74\145"]) ? wp_unslash($_GET["\x73\164\x61\x74\x65"]) : false;
        goto tTL;
        b9C:
        $Ql = $Nh->get_state();
        $Ql = apply_filters("\x73\164\x61\x74\145\137\151\156\164\145\x72\x6e\141\154", $Ql);
        setcookie("\x73\164\x61\164\x65\x5f\160\x61\x72\141\x6d", $Ql, time() + 60);
        $Nh = new StorageManager($Ql);
        tTL:
        if (!isset($_COOKIE["\163\x74\141\164\x65\x5f\x70\141\x72\141\x6d"])) {
            goto mBS;
        }
        $Ql = $_COOKIE["\x73\x74\141\164\145\137\160\141\162\141\x6d"];
        mBS:
        if (!is_wp_error($Nh)) {
            goto m2B;
        }
        wp_die(wp_kses($Nh->get_error_message(), \mo_oauth_get_valid_html()));
        m2B:
        $EL = $Nh->get_value("\165\151\144");
        if (!($EL && MO_UID === $EL)) {
            goto o5R;
        }
        $this->appname = $Nh->get_value("\x61\160\160\x6e\x61\x6d\145");
        return $Nh;
        o5R:
        return false;
    }
    public function mo_oauth_login_validate()
    {
        if (isset($_REQUEST["\155\x6f\x5f\x6c\x6f\147\x69\156\137\160\x6f\x70\x75\x70"]) && 1 == sanitize_text_field($_REQUEST["\155\x6f\137\x6c\157\147\151\x6e\x5f\x70\x6f\160\165\x70"])) {
            goto tF_;
        }
        parent::mo_oauth_login_validate();
        global $mx;
        if (!(isset($_REQUEST["\164\x6f\x6b\145\156"]) && !empty($_REQUEST["\164\157\153\x65\x6e"]) || isset($_REQUEST["\x69\x64\x5f\164\157\x6b\145\x6e"]) && !empty($_REQUEST["\151\144\137\x74\x6f\153\x65\x6e"]))) {
            goto WhQ;
        }
        if (!(isset($_REQUEST["\x74\157\x6b\145\156"]) && !empty($_REQUEST["\x74\157\x6b\145\x6e"]))) {
            goto lyj;
        }
        $ER = $mx->is_valid_jwt(urldecode($_REQUEST["\x74\157\x6b\x65\156"]));
        if ($ER) {
            goto GTb;
        }
        return;
        GTb:
        lyj:
        if (!(isset($_REQUEST["\x6e\x6f\156\143\x65"]) && (isset($_COOKIE["\x6d\157\137\157\141\x75\x74\150\137\x6e\157\x6e\x63\145"]) && $_COOKIE["\x6d\x6f\137\157\x61\165\164\x68\137\x6e\157\156\x63\145"] != $_REQUEST["\x6e\157\x6e\143\145"]))) {
            goto zOn;
        }
        wp_die("\x4e\157\x6e\143\x65\x20\166\145\x72\151\x66\x69\x63\x61\164\151\x6f\156\40\151\x73\x20\146\141\x69\154\x65\x64\x2e\x20\x50\154\145\x61\163\145\40\x63\157\156\164\x61\143\164\40\x74\x6f\40\171\x6f\165\x72\x20\x61\144\x6d\151\x6e\x69\163\164\162\141\x74\x6f\x72\56");
        exit;
        zOn:
        $QP = new Implicit(isset($_SERVER["\121\x55\x45\122\x59\x5f\123\124\x52\111\116\107"]) ? $_SERVER["\x51\x55\x45\122\131\137\123\x54\122\111\116\x47"] : '');
        if (!is_wp_error($QP)) {
            goto mOJ;
        }
        $mx->handle_error($QP->get_error_message());
        wp_die(wp_kses($QP->get_error_message(), \mo_oauth_get_valid_html()));
        MO_Oauth_Debug::mo_oauth_log("\x50\x6c\145\x61\x73\x65\40\164\x72\171\x20\114\157\147\147\151\156\x67\x20\x69\x6e\x20\141\x67\x61\151\x6e\x2e");
        exit("\120\154\x65\141\163\145\x20\x74\162\x79\40\x4c\x6f\147\x67\151\156\x67\40\x69\156\40\x61\x67\141\151\x6e\56");
        mOJ:
        $CS = $QP->get_jwt_from_query_param();
        if (!is_wp_error($CS)) {
            goto ARv;
        }
        $mx->handle_error($CS->get_error_message());
        MO_Oauth_Debug::mo_oauth_log($CS->get_error_message());
        wp_die(wp_kses($CS->get_error_message(), \mo_oauth_get_valid_html()));
        ARv:
        MO_Oauth_Debug::mo_oauth_log("\112\x57\124\40\x54\x6f\153\145\156\x20\x75\163\x65\144\x20\146\157\162\x20\x6f\142\x74\x61\x69\x6e\x69\x6e\x67\x20\x72\x65\163\x6f\165\x72\143\145\x20\157\x77\x6e\x65\x72\x20\x3d\x3e\40");
        MO_Oauth_Debug::mo_oauth_log($CS);
        $Nh = $this->check_state($QP);
        if ($Nh) {
            goto HpG;
        }
        $zR = "\x53\164\141\164\x65\40\120\141\x72\x61\155\145\164\x65\x72\40\144\x69\144\40\x6e\x6f\x74\x20\x76\145\x72\x69\146\x79\56\x20\x50\x6c\x65\141\163\x65\40\x54\162\171\x20\114\157\x67\x67\151\156\147\x20\x69\x6e\40\141\147\141\x69\156\x2e";
        $mx->handle_error($zR);
        MO_Oauth_Debug::mo_oauth_log("\x53\164\x61\164\x65\x20\x50\141\x72\x61\x6d\x65\x74\145\x72\40\144\151\144\x20\156\157\164\40\166\145\162\x69\x66\171\x2e\40\120\154\x65\x61\163\145\40\124\x72\171\40\114\x6f\x67\x67\151\156\x67\40\151\156\x20\x61\147\141\x69\156\61\56");
        wp_die($zR);
        HpG:
        $xA = $mx->get_app_by_name($this->app_name);
        $xA = $xA ? $xA->get_app_config() : false;
        $SR = $this->handle_jwt($CS);
        MO_Oauth_Debug::mo_oauth_log("\x52\145\163\x6f\165\162\x63\x65\40\117\167\x6e\145\162\40\75\76\x20");
        MO_Oauth_Debug::mo_oauth_log($SR);
        if (!is_wp_error($SR)) {
            goto afk;
        }
        $mx->handle_error($SR->get_error_message());
        wp_die(wp_kses($SR->get_error_message(), \mo_oauth_get_valid_html()));
        afk:
        if ($xA) {
            goto XBQ;
        }
        $YB = "\x53\164\x61\164\145\40\x50\141\x72\x61\155\x65\x74\145\x72\x20\x64\151\144\40\156\x6f\164\x20\x76\145\162\x69\x66\x79\56\x20\120\154\x65\x61\163\145\40\124\x72\x79\40\x4c\x6f\x67\147\x69\156\x67\x20\x69\x6e\x20\141\x67\141\x69\x6e\62\x2e";
        $mx->handle_error($YB);
        MO_Oauth_Debug::mo_oauth_log("\x53\164\x61\x74\x65\40\x50\x61\x72\x61\x6d\145\164\x65\x72\40\x64\x69\144\x20\156\x6f\x74\x20\166\145\162\x69\146\x79\x2e\40\x50\154\x65\141\x73\145\x20\124\x72\171\40\114\157\x67\147\151\156\x67\x20\151\156\x20\x61\147\x61\151\156\56");
        wp_die($YB);
        XBQ:
        if ($SR) {
            goto Zgu;
        }
        $CX = "\x4a\x57\124\x20\x53\151\x67\x6e\141\164\165\x72\x65\40\144\x69\144\40\x6e\x6f\x74\40\166\x65\162\151\146\x79\x2e\40\x50\154\145\141\x73\x65\40\124\x72\171\x20\x4c\x6f\x67\147\x69\x6e\147\40\151\x6e\40\141\x67\141\x69\156\x2e";
        $mx->handle_error($CX);
        MO_Oauth_Debug::mo_oauth_log("\x4a\x57\x54\x20\123\151\x67\x6e\x61\x74\x75\x72\x65\x20\144\x69\144\x20\156\x6f\x74\x20\x76\145\x72\x69\146\x79\x2e\x20\120\154\145\x61\x73\145\x20\124\162\171\40\x4c\157\147\147\151\156\x67\x20\x69\x6e\40\141\x67\x61\151\x6e\x2e");
        wp_die($CX);
        Zgu:
        $cz = $Nh->get_value("\x74\x65\163\x74\137\x63\157\156\x66\151\147");
        $this->resource_owner = $SR;
        $this->handle_group_details($QP->get_query_param("\141\143\143\145\x73\163\x5f\x74\157\153\x65\x6e"), isset($xA["\147\162\157\x75\x70\144\145\x74\x61\151\154\x73\x75\162\154"]) ? $xA["\147\x72\x6f\x75\160\x64\145\164\141\x69\154\x73\x75\162\154"] : '', isset($xA["\147\x72\157\x75\160\156\x61\x6d\145\137\x61\x74\164\x72\x69\142\165\164\145"]) ? $xA["\147\x72\157\165\160\x6e\141\x6d\x65\137\x61\x74\x74\x72\x69\142\x75\x74\145"] : '', $cz);
        $gu = [];
        $BW = $this->dropdownattrmapping('', $SR, $gu);
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\x6f\x61\165\164\x68\137\141\x74\x74\162\x5f\x6e\x61\x6d\x65\x5f\x6c\x69\x73\164" . $xA["\141\x70\x70\111\x64"], $BW);
        if (!($cz && '' !== $cz)) {
            goto cXz;
        }
        $this->render_test_config_output($SR);
        exit;
        cXz:
        MO_Oauth_Debug::mo_oauth_log("\102\145\146\x6f\162\145\40\150\x61\156\x64\154\145\40\163\163\157\x31");
        $this->handle_sso($this->app_name, $xA, $SR, $Nh->get_state(), $QP->get_query_param());
        WhQ:
        if (!(isset($_REQUEST["\150\165\142\154\145\x74"]) || isset($_REQUEST["\x70\x6f\162\164\141\x6c\137\x64\x6f\x6d\x61\151\x6e"]))) {
            goto O2s;
        }
        return;
        O2s:
        if (!(isset($_REQUEST["\x61\143\x63\145\x73\163\x5f\164\157\x6b\x65\156"]) && '' !== $_REQUEST["\x61\x63\x63\145\x73\163\x5f\164\x6f\x6b\x65\156"])) {
            goto MLc;
        }
        $QP = new Implicit(isset($_SERVER["\x51\125\x45\122\131\137\123\x54\122\111\x4e\x47"]) ? $_SERVER["\121\125\105\x52\x59\x5f\123\x54\122\111\x4e\107"] : '');
        $Nh = $this->check_state($QP);
        if ($Nh) {
            goto TmX;
        }
        $zR = "\x53\x74\141\164\x65\x20\x50\141\162\x61\155\145\164\145\162\x20\144\151\x64\x20\156\x6f\x74\x20\x76\145\x72\x69\x66\171\x2e\40\120\x6c\145\141\x73\x65\x20\x54\162\x79\x20\x4c\157\x67\x67\151\x6e\147\x20\151\x6e\40\x61\x67\141\x69\156\x2e";
        $mx->handle_error($zR);
        MO_Oauth_Debug::mo_oauth_log("\x53\x74\x61\164\145\x20\120\x61\162\141\x6d\145\x74\145\162\40\x64\151\144\40\x6e\157\x74\40\x76\x65\x72\151\146\171\x2e\x20\120\154\x65\x61\163\x65\40\x54\x72\171\40\114\157\147\x67\x69\x6e\x67\40\151\x6e\40\141\147\x61\151\156\x32\56");
        wp_die($zR);
        TmX:
        $xA = $mx->get_app_by_name($Nh->get_value("\141\160\x70\x6e\x61\155\x65"));
        $xA = $xA->get_app_config();
        $SR = [];
        if (!(isset($xA["\162\145\163\157\x75\162\x63\x65\x6f\x77\156\x65\x72\144\x65\164\x61\x69\x6c\163\165\162\154"]) && !empty($xA["\x72\145\163\x6f\x75\162\x63\x65\x6f\167\156\145\x72\x64\145\x74\141\151\154\163\165\162\154"]))) {
            goto U73;
        }
        $SR = $this->oauth_handler->get_resource_owner($xA["\x72\x65\x73\x6f\x75\162\x63\145\x6f\x77\156\145\162\x64\x65\164\x61\151\154\x73\165\x72\x6c"], $QP->get_query_param("\141\143\x63\x65\163\163\x5f\164\x6f\153\145\156"));
        U73:
        MO_Oauth_Debug::mo_oauth_log("\x41\x63\143\145\x73\x73\40\124\157\x6b\x65\156\x20\x3d\x3e\x20");
        MO_Oauth_Debug::mo_oauth_log($QP->get_query_param("\141\x63\143\x65\163\163\x5f\x74\x6f\153\145\x6e"));
        $QW = [];
        if (!$mx->is_valid_jwt($QP->get_query_param("\x61\x63\143\x65\x73\x73\137\x74\157\153\145\156"))) {
            goto kPB;
        }
        $CS = $QP->get_jwt_from_query_param();
        $QW = $this->handle_jwt($CS);
        kPB:
        if (empty($QW)) {
            goto Iyz;
        }
        $SR = array_merge($SR, $QW);
        Iyz:
        if (!(empty($SR) && !$mx->is_valid_jwt($QP->get_query_param("\x61\143\143\145\x73\163\x5f\164\157\x6b\x65\156")))) {
            goto H0O;
        }
        $mx->handle_error("\x49\156\166\141\x6c\x69\144\40\x52\145\163\x70\157\x6e\x73\x65\40\x52\x65\x63\145\151\x76\x65\x64\x2e");
        MO_Oauth_Debug::mo_oauth_log("\111\x6e\166\x61\x6c\151\x64\x20\122\145\x73\160\x6f\156\163\x65\x20\x52\145\x63\x65\151\166\145\144");
        wp_die("\x49\156\166\x61\x6c\151\x64\x20\x52\145\x73\x70\157\156\163\145\40\x52\145\143\145\x69\x76\x65\144\x2e");
        exit;
        H0O:
        $this->resource_owner = $SR;
        MO_Oauth_Debug::mo_oauth_log("\x52\145\163\157\x75\162\143\x65\x20\117\x77\x6e\x65\162\40\75\x3e\40");
        MO_Oauth_Debug::mo_oauth_log($this->resource_owner);
        $cz = $Nh->get_value("\164\145\x73\x74\x5f\143\x6f\x6e\146\151\x67");
        $this->handle_group_details($QP->get_query_param("\x61\x63\x63\x65\x73\163\x5f\164\157\153\x65\156"), isset($xA["\147\162\157\165\x70\144\145\x74\141\151\154\163\x75\x72\x6c"]) ? $xA["\147\162\157\165\x70\x64\145\x74\x61\151\x6c\x73\165\x72\154"] : '', isset($xA["\147\x72\x6f\165\x70\x6e\141\155\x65\x5f\141\164\164\162\x69\142\165\x74\145"]) ? $xA["\147\x72\x6f\165\160\156\141\155\x65\x5f\141\164\164\x72\x69\x62\165\164\x65"] : '', $cz);
        $gu = [];
        $BW = $this->dropdownattrmapping('', $SR, $gu);
        $mx->mo_oauth_client_update_option("\x6d\x6f\137\x6f\x61\x75\164\x68\137\141\x74\164\x72\137\x6e\x61\x6d\145\137\x6c\151\x73\x74" . $xA["\x61\160\x70\x49\x64"], $BW);
        if (!($cz && '' !== $cz)) {
            goto ijn;
        }
        $this->render_test_config_output($SR);
        exit;
        ijn:
        $Ql = str_replace("\45\x33\104", "\x3d", rawurldecode($QP->get_query_param("\163\x74\141\164\x65")));
        $this->handle_sso($this->app_name, $xA, $SR, $Ql, $QP->get_query_param());
        MLc:
        if (!(isset($_REQUEST["\x6c\x6f\147\151\156"]) && "\x70\167\144\147\x72\x6e\164\x66\x72\x6d" === $_REQUEST["\x6c\x6f\147\x69\156"])) {
            goto NE_;
        }
        $MU = new Password();
        $Ok = isset($_REQUEST["\x63\141\154\x6c\145\x72"]) && !empty($_REQUEST["\143\x61\x6c\x6c\145\x72"]) ? $_REQUEST["\x63\x61\154\x6c\x65\x72"] : false;
        $jM = isset($_REQUEST["\x74\157\157\154"]) && !empty($_REQUEST["\x74\x6f\x6f\154"]) ? $_REQUEST["\x74\x6f\157\154"] : false;
        $pY = isset($_REQUEST["\x61\x70\160\x5f\x6e\141\x6d\145"]) && !empty($_REQUEST["\141\x70\x70\x5f\156\141\155\145"]) ? $_REQUEST["\x61\160\160\x5f\x6e\x61\x6d\145"] : '';
        if (!($pY == '')) {
            goto nvy;
        }
        $l5 = "\116\157\x20\163\x75\143\x68\40\141\160\x70\40\x66\157\x75\156\x64\x20\x63\x6f\x6e\146\x69\147\x75\162\x65\144\x2e\40\x50\x6c\145\x61\163\145\x20\x63\150\145\x63\x6b\40\151\x66\40\171\157\x75\x20\141\162\x65\40\x73\x65\156\144\x69\x6e\147\x20\x74\x68\x65\x20\x63\x6f\162\x72\145\x63\164\40\141\x70\x70\154\x69\143\x61\164\x69\x6f\x6e\x20\156\141\x6d\x65";
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        nvy:
        $FO = $mx->mo_oauth_client_get_option("\155\157\x5f\157\x61\165\x74\150\137\141\x70\160\x73\137\154\151\163\164");
        if (is_array($FO) && isset($FO[$pY])) {
            goto B8o;
        }
        $l5 = "\116\157\40\163\165\x63\150\x20\x61\x70\160\x20\146\x6f\165\156\x64\40\143\157\156\146\151\147\165\162\x65\x64\x2e\40\x50\154\x65\x61\163\x65\x20\143\150\x65\143\153\40\x69\x66\40\x79\x6f\x75\x20\141\162\145\40\163\145\156\144\151\156\x67\x20\164\x68\145\x20\143\157\162\162\x65\143\164\40\x61\x70\x70\137\156\x61\155\x65";
        $mx->handle_error($l5);
        wp_die(wp_kses($l5, \mo_oauth_get_valid_html()));
        exit;
        B8o:
        $mk = isset($_REQUEST["\x6c\x6f\x63\x61\x74\x69\157\x6e"]) && !empty($_REQUEST["\x6c\x6f\143\x61\x74\151\x6f\x6e"]) ? $_REQUEST["\154\x6f\143\141\164\x69\x6f\156"] : site_url();
        $uv = isset($_REQUEST["\x74\x65\163\x74"]) && !empty($_REQUEST["\164\x65\163\164"]);
        if (!(!$Ok || !$jM || !$pY)) {
            goto yam;
        }
        $mx->redirect_user(urldecode($mk));
        yam:
        do_action("\155\157\137\157\141\165\164\150\x5f\x63\165\x73\164\157\x6d\137\163\x73\157", $Ok, $jM, $pY, $mk, $uv);
        $MU->behave($Ok, $jM, $pY, $mk, $uv);
        NE_:
        goto Lee;
        tF_:
        echo "\11\x9\x9\74\x73\x63\x72\151\160\x74\x20\x74\171\160\145\x3d\x22\x74\x65\170\164\x2f\x6a\141\x76\x61\163\x63\x72\151\160\x74\42\x3e\xd\12\11\11\x9\166\141\162\x20\142\x61\x73\145\137\165\x72\x6c\40\x3d\x20\x22";
        echo site_url();
        echo "\42\x3b\xd\xa\11\x9\11\166\x61\162\x20\141\160\x70\137\x6e\x61\x6d\x65\40\75\40\42";
        echo sanitize_text_field($_REQUEST["\141\160\x70\x5f\156\141\155\x65"]);
        echo "\x22\73\15\12\x9\x9\11\x9\x76\141\x72\40\155\x79\x57\x69\x6e\144\x6f\x77\40\x3d\x20\167\151\x6e\144\x6f\x77\x2e\157\x70\x65\x6e\x28\x20\142\141\163\145\x5f\165\x72\x6c\x20\x2b\40\47\x2f\x3f\x6f\x70\x74\x69\x6f\156\x3d\x6f\141\x75\x74\150\x72\145\144\151\x72\145\x63\x74\x26\x61\x70\x70\137\156\141\155\x65\x3d\47\x20\53\40\x61\x70\x70\x5f\156\x61\155\145\x2c\x20\x27\47\x2c\x20\47\x77\x69\144\x74\x68\x3d\65\x30\60\x2c\x68\145\151\147\x68\164\75\65\x30\60\x27\51\73\15\12\11\11\x9\x9\x3c\57\x73\143\x72\x69\160\164\76\xd\xa\11\x9\x9\x9";
        Lee:
    }
    public function handle_group_details($j6 = '', $xL = '', $X_ = '', $cz = false)
    {
        $io = [];
        if (!('' === $j6 || '' === $X_)) {
            goto hcU;
        }
        return;
        hcU:
        if (!('' !== $xL)) {
            goto m2z;
        }
        $io = $this->oauth_handler->get_resource_owner($xL, $j6);
        if (!(isset($_COOKIE["\155\157\137\157\141\x75\164\150\137\x74\x65\163\164"]) && $_COOKIE["\155\x6f\x5f\x6f\141\x75\164\x68\x5f\164\145\x73\164"])) {
            goto pty;
        }
        if (!(is_array($io) && !empty($io))) {
            goto KTo;
        }
        $this->render_test_config_output($io, true);
        KTo:
        return;
        pty:
        m2z:
        $sY = $this->get_group_mapping_attribute($this->resource_owner, $io, $X_);
        $this->group_mapping_attr = '' !== $sY ? false : $sY;
    }
    public function get_group_mapping_attribute($SR = array(), $io = array(), $X_ = '')
    {
        global $mx;
        $M_ = '';
        if (!('' === $X_)) {
            goto dJn;
        }
        return '';
        dJn:
        if (isset($io) && !empty($io)) {
            goto okn;
        }
        if (isset($SR) && !empty($SR)) {
            goto fno;
        }
        goto ziQ;
        okn:
        $M_ = $mx->getnestedattribute($io, $X_);
        goto ziQ;
        fno:
        $M_ = $mx->getnestedattribute($SR, $X_);
        ziQ:
        return !empty($M_) ? $M_ : '';
    }
    public function handle_jwt($CS)
    {
        global $mx;
        $Zy = $mx->get_app_by_name($this->app_name);
        $aY = $Zy->get_app_config("\x6a\167\164\x5f\163\165\x70\160\157\x72\x74");
        if ($aY) {
            goto z_3;
        }
        return $CS->get_decoded_payload();
        z_3:
        $Lz = $Zy->get_app_config("\152\167\164\137\141\154\147\157");
        if ($CS->check_algo($Lz)) {
            goto ets;
        }
        return new \WP_Error("\x69\x6e\166\141\154\x69\x64\137\163\x69\147\x6e", __("\112\x57\124\40\x53\151\x67\156\151\x6e\x67\40\x61\x6c\x67\x6f\162\151\164\x68\155\x20\x69\163\x20\x6e\157\164\40\141\154\154\x6f\x77\145\x64\x20\x6f\162\x20\x75\x6e\163\165\160\160\157\x72\x74\x65\x64\56"));
        ets:
        $Ys = "\122\x53\x41" === $Lz ? $Zy->get_app_config("\x78\65\60\x39\x5f\x63\x65\x72\x74") : $Zy->get_app_config("\143\x6c\151\145\x6e\164\x5f\163\145\x63\x72\x65\164");
        $Xj = $Zy->get_app_config("\x6a\x77\x6b\163\165\162\x6c");
        $uL = $Xj ? $CS->verify_from_jwks($Xj) : $CS->verify($Ys);
        return !$uL ? $uL : $CS->get_decoded_payload();
    }
    public function get_resource_owner_from_app($JX, $Zy)
    {
        global $mx;
        $this->app_name = $Zy;
        $CS = new JWTUtils($JX);
        if (!is_wp_error($CS)) {
            goto LNU;
        }
        $mx->handle_error($CS->get_error_message());
        wp_die($CS);
        LNU:
        $SR = $this->handle_jwt($CS);
        if (!is_wp_error($SR)) {
            goto qQy;
        }
        $mx->handle_error($SR->get_error_message());
        wp_die($SR);
        qQy:
        if (!(false === $SR)) {
            goto oGY;
        }
        $lM = "\106\x61\151\154\145\144\x20\164\157\x20\x76\x65\x72\x69\146\171\40\x4a\x57\x54\40\124\157\153\145\x6e\x2e\x20\x50\x6c\145\141\163\x65\x20\x63\150\x65\143\153\40\171\157\x75\x72\x20\143\x6f\x6e\x66\x69\147\165\162\x61\x74\x69\x6f\156\40\157\x72\x20\x63\157\156\164\x61\143\164\x20\171\157\165\x72\40\101\144\155\x69\x6e\x69\x73\164\x72\x61\x74\x6f\x72\56";
        $mx->handle_error($lM);
        MO_Oauth_Debug::mo_oauth_log("\x46\x61\151\x6c\145\x64\x20\164\x6f\40\x76\145\162\x69\x66\x79\x20\112\127\124\40\124\x6f\153\145\x6e\x2e\x20\x50\154\x65\141\163\x65\40\143\150\145\x63\x6b\40\x79\x6f\165\162\x20\x63\x6f\156\x66\151\x67\165\162\141\x74\151\157\156\40\157\x72\x20\x63\x6f\x6e\x74\141\143\164\x20\171\157\x75\162\x20\101\x64\x6d\151\x6e\x69\163\164\x72\141\x74\x6f\162\56");
        wp_die($lM);
        oGY:
        return $SR;
    }
}
