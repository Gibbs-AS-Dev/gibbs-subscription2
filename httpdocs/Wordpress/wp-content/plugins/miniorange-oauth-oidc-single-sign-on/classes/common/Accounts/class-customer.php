<?php


namespace MoOauthClient;

class Customer
{
    public $email;
    public $phone;
    private $default_customer_key = "\x31\66\65\x35\x35";
    private $default_api_key = "\x66\106\x64\62\x58\x63\x76\x54\107\x44\x65\155\x5a\166\x62\x77\x31\x62\143\x55\145\x73\116\112\x57\105\x71\x4b\142\142\125\161";
    private $host_name = '';
    private $host_key = '';
    public function __construct()
    {
        global $mx;
        $this->host_name = $mx->mo_oauth_client_get_option("\x68\x6f\x73\x74\137\x6e\141\x6d\145");
        $this->email = $mx->mo_oauth_client_get_option("\x6d\157\x5f\157\x61\165\164\x68\137\141\144\155\151\156\x5f\x65\155\x61\151\154");
        $this->phone = $mx->mo_oauth_client_get_option("\x6d\157\x5f\157\141\x75\164\150\137\141\x64\155\x69\x6e\137\x70\x68\157\x6e\145");
        $this->host_key = $mx->mo_oauth_client_get_option("\x70\141\163\x73\167\157\162\144");
    }
    public function create_customer()
    {
        global $mx;
        $QR = $this->host_name . "\57\x6d\157\141\163\x2f\162\145\163\x74\57\x63\x75\163\x74\157\x6d\145\x72\x2f\141\144\144";
        $oa = $this->host_key;
        $m3 = $mx->mo_oauth_client_get_option("\155\x6f\x5f\157\x61\165\164\x68\x5f\141\144\x6d\151\x6e\x5f\146\x6e\x61\x6d\x65");
        $sF = $mx->mo_oauth_client_get_option("\155\157\x5f\157\x61\165\164\x68\137\x61\x64\x6d\x69\156\137\x6c\x6e\x61\155\145");
        $NL = $mx->mo_oauth_client_get_option("\155\157\x5f\x6f\x61\165\x74\x68\x5f\x61\x64\x6d\x69\156\x5f\143\x6f\155\160\141\x6e\171");
        $uj = array("\x63\157\155\160\141\156\x79\116\141\x6d\x65" => $NL, "\x61\x72\145\141\x4f\146\111\156\x74\x65\x72\x65\x73\x74" => "\127\x50\40\x4f\101\165\164\150\40\103\x6c\x69\x65\x6e\x74", "\146\x69\x72\x73\x74\x6e\141\x6d\x65" => $m3, "\154\x61\x73\164\156\x61\155\145" => $sF, \MoOAuthConstants::EMAIL => $this->email, "\x70\150\x6f\156\x65" => $this->phone, "\160\141\x73\x73\x77\157\x72\x64" => $oa);
        $ax = wp_json_encode($uj);
        return $this->send_request([], false, $ax, [], false, $QR);
    }
    public function get_customer_key()
    {
        global $mx;
        $QR = $this->host_name . "\x2f\155\x6f\x61\x73\x2f\x72\x65\163\x74\57\143\165\x73\164\157\x6d\x65\162\57\153\145\x79";
        $UU = $this->email;
        $oa = $this->host_key;
        $uj = array(\MoOAuthConstants::EMAIL => $UU, "\160\x61\x73\x73\x77\x6f\x72\x64" => $oa);
        $ax = wp_json_encode($uj);
        return $this->send_request([], false, $ax, [], false, $QR);
    }
    public function add_oauth_application($O6, $pY)
    {
        global $mx;
        $QR = $this->host_name . "\x2f\155\157\x61\x73\57\x72\x65\163\164\x2f\141\x70\160\154\x69\x63\141\164\151\157\x6e\57\x61\x64\144\x6f\141\165\x74\150";
        $Y5 = $mx->mo_oauth_client_get_option("\x6d\157\x5f\x6f\141\x75\x74\150\x5f\141\144\155\x69\x6e\x5f\143\165\163\x74\157\155\x65\x72\137\153\145\x79");
        $Y1 = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\165\x74\150\137" . $O6 . "\137\x73\143\x6f\x70\145");
        $Sk = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\x6f\141\165\164\150\137" . $O6 . "\x5f\x63\154\x69\x65\x6e\x74\137\x69\144");
        $xR = $mx->mo_oauth_client_get_option("\155\157\x5f\x6f\141\x75\x74\150\137" . $O6 . "\x5f\143\154\x69\x65\156\164\x5f\163\145\x63\162\145\164");
        if (false !== $Y1) {
            goto fV;
        }
        $uj = array("\x61\160\x70\154\151\143\x61\x74\x69\157\x6e\116\141\x6d\x65" => $pY, "\143\165\163\164\x6f\155\145\x72\111\x64" => $Y5, "\143\154\151\x65\156\164\x49\x64" => $Sk, "\x63\154\x69\145\x6e\x74\x53\x65\143\x72\x65\164" => $xR);
        goto ZP;
        fV:
        $uj = array("\141\x70\x70\154\x69\x63\141\164\151\157\x6e\116\x61\155\145" => $pY, "\163\143\x6f\160\145" => $Y1, "\x63\165\x73\x74\x6f\155\x65\x72\111\x64" => $Y5, "\143\x6c\151\145\x6e\x74\x49\144" => $Sk, "\143\x6c\151\x65\x6e\164\123\145\143\x72\145\x74" => $xR);
        ZP:
        $ax = wp_json_encode($uj);
        return $this->send_request([], false, $ax, [], false, $QR);
    }
    public function submit_contact_us($UU, $rH, $mO, $mT = true)
    {
        global $current_user;
        global $mx;
        wp_get_current_user();
        $WL = $mx->export_plugin_config(true);
        $yL = json_encode($WL, JSON_UNESCAPED_SLASHES);
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $AT = time();
        $QR = $this->host_name . "\x2f\155\157\141\x73\57\141\x70\151\57\x6e\157\164\x69\x66\x79\57\x73\145\x6e\144";
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\163\x68\x61\x35\61\62", $iU);
        $Td = $UU;
        $cx = \ucwords(\strtolower($mx->get_versi_str())) . "\x20\x2d\40" . \mo_oauth_get_version_number();
        $Hk = "\121\165\145\162\171\x3a\x20\127\x6f\162\x64\x50\x72\145\x73\163\x20\x4f\x41\x75\x74\x68\x20" . $cx . "\40\120\154\x75\147\x69\x6e";
        $mO = "\133\127\x50\x20\117\101\x75\x74\150\40\x43\154\151\x65\156\164\40" . $cx . "\x5d\x20" . $mO;
        if (!$mT) {
            goto aZ;
        }
        $mO .= "\74\142\162\x3e\x3c\x62\x72\76\103\x6f\x6e\x66\x69\147\x20\x53\164\162\151\156\147\72\74\142\162\76\74\160\x72\145\40\x73\164\x79\x6c\x65\75\42\x62\157\x72\x64\145\162\72\x31\x70\x78\40\x73\x6f\154\x69\x64\x20\43\64\x34\x34\x3b\160\141\x64\x64\x69\x6e\147\x3a\61\x30\160\x78\x3b\x22\x3e\74\143\157\x64\145\x3e" . $yL . "\x3c\x2f\x63\157\x64\x65\76\74\57\160\162\x65\76";
        aZ:
        $Pu = isset($_SERVER["\123\105\122\x56\x45\122\137\116\101\x4d\105"]) ? sanitize_text_field(wp_unslash($_SERVER["\x53\x45\122\126\105\x52\137\x4e\101\115\105"])) : '';
        $Bn = "\x3c\x64\151\166\40\76\110\145\x6c\154\157\54\40\74\x62\162\76\x3c\x62\162\76\x46\x69\x72\163\x74\x20\116\141\x6d\x65\x20\x3a" . $current_user->user_firstname . "\74\142\162\x3e\x3c\x62\162\x3e\x4c\141\163\x74\40\40\x4e\x61\x6d\145\40\x3a" . $current_user->user_lastname . "\40\40\40\x3c\x62\x72\x3e\74\x62\162\x3e\x43\157\155\160\x61\x6e\x79\40\72\74\141\x20\x68\x72\145\x66\x3d\x22" . $Pu . "\42\40\164\141\162\x67\145\x74\75\x22\x5f\x62\154\x61\156\153\x22\40\76" . $Pu . "\x3c\57\x61\x3e\74\142\x72\x3e\x3c\142\x72\76\x50\x68\x6f\x6e\145\x20\116\x75\x6d\142\x65\x72\40\72" . $rH . "\x3c\142\x72\76\74\x62\162\76\105\x6d\141\x69\154\40\72\x3c\x61\40\150\x72\x65\x66\75\42\155\x61\151\154\164\x6f\72" . $Td . "\42\40\164\x61\162\x67\x65\x74\75\42\x5f\142\154\x61\x6e\x6b\42\x3e" . $Td . "\x3c\x2f\x61\x3e\x3c\142\x72\76\74\x62\x72\76\121\165\145\162\x79\40\72" . $mO . "\x3c\x2f\x64\x69\x76\x3e";
        $uj = array("\143\165\x73\x74\x6f\155\145\x72\x4b\145\x79" => $Y5, "\163\x65\x6e\144\x45\x6d\x61\x69\154" => true, \MoOAuthConstants::EMAIL => array("\143\165\163\x74\157\x6d\145\162\113\145\171" => $Y5, "\146\162\x6f\155\105\x6d\x61\151\x6c" => $Td, "\142\x63\143\x45\x6d\x61\x69\154" => "\151\156\x66\157\x40\170\x65\143\x75\162\x69\146\171\x2e\x63\x6f\x6d", "\146\162\157\x6d\x4e\141\155\145" => "\x6d\x69\156\151\x4f\162\141\156\x67\145", "\x74\x6f\105\155\141\x69\154" => "\x6f\141\x75\x74\150\163\x75\x70\160\157\162\x74\100\170\145\x63\x75\162\x69\146\x79\x2e\143\x6f\x6d", "\164\x6f\x4e\x61\x6d\145" => "\x6f\141\165\164\150\163\x75\160\160\x6f\162\x74\100\170\x65\143\165\162\151\146\171\56\143\157\155", "\x73\x75\142\152\145\143\164" => $Hk, "\x63\157\x6e\x74\145\x6e\164" => $Bn));
        $ax = json_encode($uj, JSON_UNESCAPED_SLASHES);
        $P_ = array("\x43\x6f\156\x74\145\156\164\55\124\171\x70\145" => "\141\x70\160\154\151\x63\x61\164\x69\x6f\x6e\x2f\152\x73\157\x6e");
        $P_["\x43\165\163\x74\157\x6d\145\162\x2d\113\145\171"] = $Y5;
        $P_["\124\x69\x6d\x65\163\x74\141\155\x70"] = $AT;
        $P_["\x41\165\164\x68\157\162\x69\172\141\164\x69\157\x6e"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function submit_contact_us_upgrade($UU, $r8, $WG, $Dx)
    {
        global $mx;
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $AT = time();
        $QR = $this->host_name . "\x2f\155\x6f\141\x73\57\x61\160\151\57\156\157\x74\x69\146\171\x2f\x73\145\156\144";
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\163\x68\141\x35\61\x32", $iU);
        $Td = $UU;
        $cx = \ucwords(\strtolower($mx->get_versi_str())) . "\40\55\40" . \mo_oauth_get_version_number();
        $Hk = "\x51\x75\x65\x72\x79\72\40\x57\157\x72\x64\x50\162\145\x73\x73\x20\117\101\165\x74\x68\40\125\x70\147\162\141\x64\145\40\120\x6c\x75\x67\x69\156";
        $Pu = isset($_SERVER["\123\105\122\x56\x45\x52\x5f\116\101\x4d\x45"]) ? sanitize_text_field(wp_unslash($_SERVER["\123\x45\x52\x56\105\122\137\116\101\115\x45"])) : '';
        $Bn = "\x3c\144\151\x76\40\x3e\110\x65\154\154\157\x2c\40\40\40\74\142\162\76\74\x62\162\76\x43\157\155\160\x61\x6e\x79\40\x3a\74\141\x20\150\x72\x65\x66\x3d\42" . $Pu . "\x22\40\164\x61\162\x67\145\x74\75\42\x5f\x62\154\x61\x6e\x6b\x22\40\76" . $Pu . "\74\57\x61\76\x3c\142\x72\76\74\x62\162\x3e\103\165\162\x72\x65\x6e\164\x20\x56\145\x72\x73\151\x6f\156\x20\x3a" . $r8 . "\x3c\x62\x72\76\x3c\142\162\x3e\x45\x6d\x61\151\154\x20\72\74\x61\x20\x68\x72\x65\x66\75\42\155\141\151\x6c\164\157\72" . $Td . "\42\x20\164\x61\x72\147\145\164\75\42\137\142\154\x61\x6e\x6b\42\x3e" . $Td . "\x3c\x2f\x61\76\74\x62\162\x3e\x3c\x62\x72\x3e\x56\145\162\x73\x69\157\x6e\x20\x74\157\40\125\160\x67\x72\x61\144\x65\x20\x3a" . $WG . "\74\142\162\76\74\x62\162\76\106\145\x61\164\165\162\x65\x73\40\x52\145\161\x75\x69\162\145\x64\x20\x3a" . $Dx . "\74\57\144\151\166\x3e";
        $uj = array("\x63\165\163\164\x6f\x6d\145\x72\x4b\x65\171" => $Y5, "\x73\145\156\x64\x45\x6d\x61\151\154" => true, \MoOAuthConstants::EMAIL => array("\143\165\163\x74\x6f\x6d\145\162\x4b\x65\171" => $Y5, "\146\162\x6f\155\x45\x6d\x61\x69\154" => $Td, "\142\x63\x63\105\155\141\151\x6c" => "\x69\x6e\146\157\x40\x78\x65\x63\165\162\x69\146\x79\56\x63\157\155", "\x66\x72\x6f\x6d\x4e\141\x6d\145" => "\155\151\x6e\151\x4f\x72\x61\x6e\147\x65", "\x74\x6f\x45\155\141\151\154" => "\157\x61\x75\164\150\163\x75\x70\x70\x6f\x72\164\100\x78\x65\x63\165\x72\151\146\171\x2e\143\157\x6d", "\x74\x6f\x4e\141\155\145" => "\157\x61\x75\x74\150\163\165\x70\160\x6f\x72\164\x40\170\x65\x63\165\162\151\x66\171\56\143\x6f\155", "\x73\x75\x62\x6a\145\143\164" => $Hk, "\x63\157\x6e\164\145\156\x74" => $Bn));
        $ax = json_encode($uj, JSON_UNESCAPED_SLASHES);
        $P_ = array("\x43\157\156\164\145\156\x74\55\124\171\x70\x65" => "\141\x70\x70\154\x69\143\141\164\151\x6f\156\57\152\x73\x6f\x6e");
        $P_["\x43\x75\x73\x74\157\x6d\x65\x72\x2d\x4b\145\x79"] = $Y5;
        $P_["\124\151\155\145\163\x74\x61\x6d\160"] = $AT;
        $P_["\101\165\x74\x68\x6f\x72\x69\x7a\141\164\x69\x6f\156"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function send_otp_token($UU = '', $rH = '', $u0 = true, $wt = false)
    {
        global $mx;
        $QR = $this->host_name . "\57\155\157\x61\163\57\x61\160\151\x2f\141\x75\x74\150\57\x63\150\141\x6c\x6c\x65\x6e\147\x65";
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $Lj = $this->email;
        $rH = $mx->mo_oauth_client_get_option("\155\x6f\137\x6f\x61\x75\x74\150\x5f\141\x64\155\x69\156\x5f\160\x68\157\156\x65");
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\x73\150\x61\65\x31\62", $iU);
        $Zm = "\x43\x75\x73\x74\157\155\145\x72\55\113\145\x79\72\40" . $Y5;
        $zA = "\124\151\155\145\x73\164\x61\x6d\160\72\40" . $AT;
        $BH = "\101\x75\164\x68\x6f\162\x69\x7a\141\164\151\x6f\x6e\72\x20" . $Lm;
        if ($u0) {
            goto P2;
        }
        $uj = array("\143\x75\163\164\x6f\x6d\x65\x72\x4b\x65\171" => $Y5, "\160\150\x6f\x6e\x65" => $rH, "\x61\x75\164\150\x54\171\160\145" => "\123\115\x53");
        goto eR;
        P2:
        $uj = array("\143\165\163\164\157\155\145\162\x4b\145\171" => $Y5, \MoOAuthConstants::EMAIL => $Lj, "\141\x75\164\x68\x54\171\160\x65" => "\105\x4d\101\111\x4c");
        eR:
        $ax = wp_json_encode($uj);
        $P_ = array("\103\x6f\156\164\x65\x6e\x74\55\124\171\x70\145" => "\141\x70\x70\x6c\x69\143\x61\164\151\157\156\x2f\x6a\163\x6f\156");
        $P_["\103\x75\163\164\x6f\155\145\162\x2d\113\x65\171"] = $Y5;
        $P_["\x54\151\155\x65\x73\x74\x61\155\160"] = $AT;
        $P_["\101\165\x74\150\x6f\x72\x69\172\x61\164\151\157\156"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function get_timestamp()
    {
        global $mx;
        $QR = $this->host_name . "\x2f\x6d\x6f\x61\x73\x2f\x72\x65\x73\164\57\155\x6f\142\151\x6c\145\57\147\145\164\x2d\x74\151\x6d\145\x73\x74\141\x6d\x70";
        return $this->send_request([], false, '', [], false, $QR);
    }
    public function validate_otp_token($zO, $xk)
    {
        global $mx;
        $QR = $this->host_name . "\x2f\155\x6f\141\163\57\141\x70\151\57\141\165\x74\x68\57\166\141\x6c\x69\144\141\x74\145";
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $Lj = $this->email;
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\163\150\141\65\61\62", $iU);
        $Zm = "\103\165\x73\x74\x6f\x6d\x65\162\55\113\145\x79\x3a\x20" . $Y5;
        $zA = "\x54\151\x6d\x65\x73\x74\141\x6d\x70\72\x20" . $AT;
        $BH = "\x41\165\164\150\157\x72\151\172\141\164\x69\157\x6e\x3a\x20" . $Lm;
        $ax = '';
        $uj = array("\x74\x78\111\144" => $zO, "\x74\157\x6b\145\x6e" => $xk);
        $ax = wp_json_encode($uj);
        $P_ = array("\x43\157\x6e\x74\145\x6e\x74\x2d\x54\171\x70\145" => "\141\x70\x70\x6c\151\x63\141\164\x69\157\156\57\x6a\x73\157\156");
        $P_["\x43\x75\x73\x74\x6f\155\145\162\55\x4b\x65\x79"] = $Y5;
        $P_["\124\x69\155\145\163\x74\x61\155\x70"] = $AT;
        $P_["\x41\165\x74\150\x6f\x72\151\x7a\x61\x74\151\157\x6e"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function check_customer()
    {
        global $mx;
        $QR = $this->host_name . "\x2f\155\157\141\163\57\162\x65\163\x74\57\x63\165\x73\164\157\x6d\145\162\57\143\150\x65\143\153\x2d\x69\146\x2d\x65\170\x69\163\x74\163";
        $UU = $this->email;
        $uj = array(\MoOAuthConstants::EMAIL => $UU);
        $ax = wp_json_encode($uj);
        return $this->send_request([], false, $ax, [], false, $QR);
    }
    public function mo_oauth_send_email_alert($UU, $rH, $Kv)
    {
        global $mx;
        if ($this->check_internet_connection()) {
            goto Rv;
        }
        return;
        Rv:
        $QR = $this->host_name . "\x2f\155\157\x61\x73\57\x61\x70\x69\x2f\x6e\157\164\151\x66\x79\57\163\x65\156\144";
        global $user;
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\163\x68\141\x35\61\x32", $iU);
        $Td = $UU;
        $Hk = "\x46\x65\x65\144\x62\x61\x63\x6b\72\40\x57\x6f\x72\x64\x50\x72\145\163\x73\x20\117\x41\165\x74\150\40\103\154\151\x65\x6e\164\40\120\154\165\x67\151\x6e";
        $Vk = site_url();
        $user = wp_get_current_user();
        $cx = \ucwords(\strtolower($mx->get_versi_str())) . "\40\x2d\40" . \mo_oauth_get_version_number();
        $mO = "\x5b\127\x50\x20\x4f\101\165\x74\x68\x20\x32\56\x30\x20\x43\154\151\x65\x6e\x74\x20" . $cx . "\x5d\x20\72\x20" . $Kv;
        $Pu = isset($_SERVER["\123\105\122\x56\105\122\137\116\x41\115\x45"]) ? sanitize_text_field(wp_unslash($_SERVER["\123\x45\122\x56\x45\122\137\x4e\101\x4d\x45"])) : '';
        $Bn = "\x3c\144\x69\x76\x20\x3e\x48\x65\154\154\157\54\x20\74\142\162\76\x3c\142\x72\x3e\106\x69\162\163\164\40\x4e\x61\x6d\145\x20\x3a" . $user->user_firstname . "\74\x62\162\76\x3c\142\x72\76\114\141\163\x74\x20\40\x4e\x61\x6d\x65\x20\x3a" . $user->user_lastname . "\40\x20\40\74\142\162\76\x3c\x62\162\76\103\157\x6d\x70\x61\156\171\40\x3a\74\141\40\150\x72\x65\x66\x3d\42" . $Pu . "\42\40\164\141\162\147\x65\x74\75\42\137\x62\x6c\141\156\x6b\x22\40\x3e" . $Pu . "\x3c\57\141\76\74\142\162\x3e\x3c\142\x72\x3e\x50\x68\x6f\x6e\145\x20\x4e\165\x6d\x62\x65\x72\40\72" . $rH . "\74\142\x72\76\74\x62\162\76\105\x6d\141\151\154\x20\x3a\x3c\x61\40\150\162\145\x66\x3d\42\x6d\x61\x69\x6c\x74\x6f\72" . $Td . "\42\40\x74\x61\162\147\x65\164\75\x22\x5f\142\154\x61\x6e\153\x22\76" . $Td . "\74\x2f\x61\76\74\x62\x72\76\74\142\162\76\121\x75\x65\x72\x79\40\x3a" . $mO . "\74\x2f\144\x69\x76\x3e";
        $uj = array("\143\165\163\164\157\155\145\162\x4b\145\171" => $Y5, "\163\x65\x6e\x64\x45\155\x61\151\154" => true, \MoOAuthConstants::EMAIL => array("\x63\x75\x73\164\157\x6d\x65\x72\113\x65\171" => $Y5, "\146\162\x6f\155\105\155\x61\x69\x6c" => $Td, "\142\143\143\105\155\x61\x69\x6c" => "\x6f\x61\x75\164\150\163\165\160\160\x6f\x72\164\x40\x6d\151\156\x69\x6f\x72\x61\156\x67\145\x2e\143\x6f\155", "\146\162\157\155\116\141\155\145" => "\x6d\151\156\151\117\162\141\x6e\x67\x65", "\164\157\x45\x6d\141\x69\x6c" => "\x6f\141\165\164\150\x73\165\160\160\157\x72\164\100\x6d\151\156\x69\157\x72\x61\156\x67\x65\56\143\157\155", "\164\x6f\116\141\x6d\145" => "\x6f\141\165\x74\150\163\165\x70\160\x6f\x72\x74\100\155\151\x6e\x69\x6f\x72\141\x6e\147\x65\56\x63\157\155", "\x73\x75\x62\152\145\143\x74" => $Hk, "\143\x6f\156\x74\x65\156\x74" => $Bn));
        $ax = wp_json_encode($uj);
        $P_ = array("\x43\157\x6e\164\145\x6e\164\55\124\x79\x70\x65" => "\x61\160\160\x6c\x69\x63\x61\164\x69\157\x6e\x2f\x6a\x73\157\x6e");
        $P_["\x43\165\163\x74\157\155\x65\x72\x2d\113\x65\x79"] = $Y5;
        $P_["\124\151\x6d\145\x73\x74\141\x6d\x70"] = $AT;
        $P_["\x41\165\164\x68\157\x72\x69\172\x61\x74\x69\157\x6e"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function mo_oauth_send_demo_alert($UU, $IT, $Kv, $Hk)
    {
        if ($this->check_internet_connection()) {
            goto e3;
        }
        return;
        e3:
        $QR = $this->host_name . "\57\x6d\157\x61\163\x2f\141\x70\151\x2f\156\x6f\x74\151\x66\171\57\x73\x65\x6e\144";
        $Y5 = $this->default_customer_key;
        $Jb = $this->default_api_key;
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\x73\150\141\65\61\x32", $iU);
        $Td = $UU;
        global $user;
        $user = wp_get_current_user();
        $Bn = "\x3c\x64\151\x76\x20\x3e\110\x65\154\154\157\54\40\74\x2f\x61\76\74\x62\162\x3e\x3c\142\162\76\x45\x6d\141\x69\x6c\40\x3a\74\141\x20\x68\162\x65\146\x3d\42\x6d\141\x69\x6c\164\x6f\72" . $Td . "\42\40\x74\141\162\147\x65\164\75\x22\137\142\x6c\141\x6e\153\42\x3e" . $Td . "\x3c\57\141\76\74\142\x72\x3e\74\142\162\x3e\122\145\161\165\145\x73\x74\x65\x64\40\x44\x65\155\157\40\x66\x6f\162\x20\x20\x20\40\x20\72\x20" . $IT . "\x3c\142\162\x3e\x3c\142\162\76\122\145\161\165\x69\162\145\x6d\x65\x6e\x74\163\40\40\x20\x20\x20\40\40\x20\x20\x20\x20\x3a\x20" . $Kv . "\74\x2f\144\151\166\76";
        $uj = array("\x63\165\163\164\x6f\x6d\145\x72\113\145\171" => $Y5, "\163\x65\156\144\105\155\x61\x69\x6c" => true, \MoOAuthConstants::EMAIL => array("\x63\x75\x73\164\157\155\145\x72\x4b\145\x79" => $Y5, "\x66\x72\157\155\x45\x6d\141\x69\154" => $Td, "\142\x63\x63\x45\x6d\141\x69\154" => "\157\x61\x75\x74\x68\x73\x75\160\x70\x6f\x72\x74\x40\155\151\x6e\x69\157\x72\141\x6e\x67\x65\56\x63\x6f\x6d", "\x66\x72\x6f\155\x4e\141\x6d\x65" => "\x6d\x69\x6e\x69\117\x72\x61\156\x67\145", "\164\x6f\105\x6d\141\151\154" => "\x6f\141\165\x74\150\x73\165\x70\160\157\x72\164\100\x6d\x69\156\151\157\x72\x61\156\x67\145\56\x63\157\155", "\164\x6f\116\x61\155\145" => "\x6f\141\x75\x74\x68\163\x75\160\160\x6f\162\x74\100\155\x69\156\151\157\162\141\156\147\x65\x2e\143\157\155", "\x73\165\x62\x6a\145\143\164" => $Hk, "\x63\157\156\x74\145\x6e\164" => $Bn));
        $ax = json_encode($uj);
        $P_ = array("\x43\157\x6e\164\145\x6e\x74\55\124\x79\160\145" => "\141\160\x70\154\151\143\141\x74\x69\x6f\x6e\x2f\152\163\157\156");
        $P_["\103\165\163\x74\157\x6d\x65\162\x2d\x4b\x65\171"] = $Y5;
        $P_["\x54\x69\155\x65\163\164\141\x6d\x70"] = $AT;
        $P_["\101\x75\164\x68\x6f\x72\x69\172\141\x74\x69\157\x6e"] = $Lm;
        $zF = $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function mo_oauth_forgot_password($UU)
    {
        global $mx;
        $QR = $this->host_name . "\x2f\155\x6f\x61\163\x2f\162\145\163\164\x2f\143\x75\x73\x74\x6f\155\145\162\x2f\x70\x61\163\x73\167\x6f\x72\144\55\x72\x65\x73\x65\164";
        $Y5 = $mx->mo_oauth_client_get_option("\x6d\157\x5f\157\141\165\164\150\x5f\141\144\155\x69\156\137\x63\165\163\x74\x6f\155\x65\x72\x5f\x6b\x65\171");
        $Jb = $mx->mo_oauth_client_get_option("\x6d\x6f\x5f\157\x61\x75\x74\x68\x5f\x61\144\x6d\x69\156\x5f\141\160\x69\137\153\x65\x79");
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\x73\x68\x61\65\x31\x32", $iU);
        $Zm = "\x43\165\163\x74\x6f\x6d\x65\x72\x2d\113\x65\171\x3a\x20" . $Y5;
        $zA = "\x54\x69\x6d\145\163\164\141\x6d\x70\72\40" . number_format($AT, 0, '', '');
        $BH = "\101\x75\x74\x68\157\x72\x69\x7a\x61\x74\x69\157\x6e\x3a\x20" . $Lm;
        $ax = '';
        $uj = array(\MoOAuthConstants::EMAIL => $UU);
        $ax = wp_json_encode($uj);
        $P_ = array("\x43\157\156\x74\x65\156\x74\55\x54\x79\160\145" => "\x61\x70\x70\154\151\143\x61\x74\x69\x6f\x6e\x2f\x6a\163\x6f\156");
        $P_["\x43\x75\x73\164\157\155\145\162\x2d\113\145\171"] = $Y5;
        $P_["\x54\151\155\145\x73\164\x61\155\160"] = $AT;
        $P_["\101\x75\x74\x68\x6f\162\x69\172\141\164\x69\157\x6e"] = $Lm;
        return $this->send_request($P_, true, $ax, [], false, $QR);
    }
    public function check_internet_connection()
    {
        return (bool) @fsockopen("\x6c\157\147\151\x6e\56\170\145\143\165\x72\151\x66\171\56\x63\157\155", 443, $hW, $iI, 5);
    }
    private function send_request($iT = false, $h0 = false, $ax = '', $CL = false, $bO = false, $QR = '')
    {
        $P_ = array("\x43\x6f\156\164\x65\156\164\55\124\x79\160\x65" => "\141\160\160\154\151\143\141\x74\151\x6f\x6e\57\152\x73\157\x6e", "\143\150\141\x72\x73\x65\x74" => "\x55\124\106\40\x2d\40\70", "\101\165\164\x68\x6f\x72\x69\x7a\141\164\x69\x6f\156" => "\x42\x61\163\x69\143");
        $P_ = $h0 && $iT ? $iT : array_unique(array_merge($P_, $iT));
        $x1 = array("\x6d\x65\164\x68\157\x64" => "\x50\117\x53\x54", "\x62\157\144\171" => $ax, "\x74\151\x6d\x65\x6f\165\x74" => "\x31\x35", "\162\x65\144\x69\x72\x65\x63\164\151\157\156" => "\65", "\x68\164\164\160\x76\x65\x72\x73\151\157\x6e" => "\x31\56\x30", "\x62\x6c\157\143\x6b\151\156\x67" => true, "\150\x65\141\x64\x65\x72\163" => $P_, "\163\x73\x6c\166\145\162\151\146\x79" => true);
        $x1 = $bO ? $CL : array_unique(array_merge($x1, $CL), SORT_REGULAR);
        $zF = wp_remote_post($QR, $x1);
        if (!is_wp_error($zF)) {
            goto mq;
        }
        $sa = $zF->get_error_message();
        echo wp_kses("\123\157\155\x65\x74\x68\x69\156\x67\40\x77\145\156\164\40\x77\x72\x6f\x6e\x67\72\40{$sa}", \mo_oauth_get_valid_html());
        exit;
        mq:
        return wp_remote_retrieve_body($zF);
    }
}
