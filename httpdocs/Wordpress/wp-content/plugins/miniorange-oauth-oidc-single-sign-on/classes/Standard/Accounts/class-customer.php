<?php


namespace MoOauthClient\Standard;

use MoOauthClient\Customer as NormalCustomer;
class Customer extends NormalCustomer
{
    public $email;
    public $phone;
    private $default_customer_key = "\x31\66\65\x35\x35";
    private $default_api_key = "\x66\106\x64\x32\x58\143\166\x54\107\104\145\155\x5a\166\142\x77\61\x62\143\x55\x65\163\x4e\112\x57\105\x71\113\142\x62\x55\161";
    public function check_customer_ln()
    {
        global $mx;
        $QR = $mx->mo_oauth_client_get_option("\x68\157\163\164\137\x6e\141\x6d\145") . "\57\155\x6f\141\163\x2f\162\x65\163\164\57\x63\x75\x73\164\x6f\155\145\162\x2f\x6c\x69\143\x65\156\x73\145";
        $Y5 = $mx->mo_oauth_client_get_option("\x6d\157\x5f\x6f\141\x75\164\x68\137\141\x64\x6d\x69\156\137\143\165\x73\164\x6f\155\x65\162\137\x6b\x65\171");
        $Jb = $mx->mo_oauth_client_get_option("\155\157\x5f\x6f\141\x75\x74\150\137\141\144\x6d\x69\156\137\141\x70\151\x5f\153\x65\171");
        $Lj = $mx->mo_oauth_client_get_option("\155\157\x5f\x6f\x61\x75\164\x68\137\141\x64\155\x69\156\x5f\x65\155\x61\x69\154");
        $rH = $mx->mo_oauth_client_get_option("\155\157\137\157\141\165\164\150\x5f\x61\x64\x6d\x69\x6e\137\x70\x68\x6f\x6e\145");
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\x73\x68\141\65\61\62", $iU);
        $Zm = "\x43\x75\x73\x74\x6f\x6d\x65\162\55\113\x65\x79\x3a\x20" . $Y5;
        $zA = "\x54\151\155\145\x73\x74\x61\x6d\160\x3a\40" . $AT;
        $BH = "\x41\165\x74\150\x6f\x72\151\172\x61\x74\x69\x6f\156\72\40" . $Lm;
        $uj = '';
        $uj = array("\143\x75\163\164\x6f\x6d\145\x72\111\x64" => $Y5, "\x61\x70\x70\x6c\151\143\141\164\151\x6f\156\116\141\x6d\145" => "\167\x70\137\x6f\x61\x75\x74\150\x5f\x63\154\151\145\156\x74\x5f" . \strtolower($mx->get_versi_str()) . "\137\160\154\x61\x6e");
        $ax = wp_json_encode($uj);
        $P_ = array("\x43\x6f\x6e\164\145\x6e\x74\55\x54\x79\x70\x65" => "\x61\160\x70\154\x69\x63\x61\164\151\157\156\x2f\x6a\x73\157\156");
        $P_["\x43\x75\163\164\x6f\155\x65\162\55\x4b\x65\171"] = $Y5;
        $P_["\x54\151\x6d\145\163\164\x61\155\x70"] = $AT;
        $P_["\101\165\x74\150\x6f\x72\x69\172\x61\x74\151\157\x6e"] = $Lm;
        $x1 = array("\155\x65\x74\x68\x6f\x64" => "\x50\117\x53\x54", "\x62\157\144\x79" => $ax, "\164\x69\155\x65\x6f\165\x74" => "\x31\65", "\x72\x65\x64\x69\x72\x65\143\x74\x69\x6f\156" => "\x35", "\x68\x74\164\x70\x76\145\x72\x73\151\157\156" => "\61\x2e\x30", "\x62\154\x6f\143\x6b\x69\x6e\147" => true, "\150\x65\141\x64\145\162\x73" => $P_);
        $zF = wp_remote_post($QR, $x1);
        if (!is_wp_error($zF)) {
            goto GxG;
        }
        $sa = $zF->get_error_message();
        echo "\x53\x6f\x6d\145\x74\x68\151\x6e\147\x20\x77\x65\156\164\40\x77\x72\x6f\156\147\72\x20{$sa}";
        exit;
        GxG:
        return wp_remote_retrieve_body($zF);
    }
    public function XfskodsfhHJ($It)
    {
        global $mx;
        $QR = $mx->mo_oauth_client_get_option("\150\157\x73\x74\137\156\x61\x6d\145") . "\57\x6d\x6f\141\x73\57\x61\160\151\x2f\142\x61\x63\x6b\165\160\x63\157\144\x65\57\x76\x65\x72\x69\146\x79";
        $Y5 = $mx->mo_oauth_client_get_option("\x6d\x6f\137\157\x61\165\x74\x68\137\x61\144\x6d\151\156\137\143\165\163\164\157\x6d\x65\x72\x5f\x6b\145\171");
        $Jb = $mx->mo_oauth_client_get_option("\x6d\157\x5f\x6f\141\x75\164\x68\x5f\141\x64\155\x69\x6e\x5f\141\160\151\x5f\x6b\x65\x79");
        $Lj = $mx->mo_oauth_client_get_option("\155\157\137\157\x61\x75\164\150\x5f\x61\x64\155\151\x6e\x5f\145\x6d\x61\x69\x6c");
        $rH = $mx->mo_oauth_client_get_option("\x6d\157\x5f\x6f\141\165\164\x68\x5f\x61\x64\x6d\x69\x6e\x5f\160\150\157\156\145");
        $AT = self::get_timestamp();
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\163\150\141\65\61\62", $iU);
        $Zm = "\103\165\163\x74\x6f\x6d\145\162\55\113\145\171\x3a\x20" . $Y5;
        $zA = "\x54\151\x6d\145\163\x74\141\155\x70\x3a\40" . $AT;
        $BH = "\x41\165\x74\x68\x6f\162\151\172\141\x74\x69\x6f\156\x3a\x20" . $Lm;
        $uj = '';
        $uj = array("\143\157\144\145" => $It, "\x63\x75\163\x74\157\x6d\145\x72\x4b\145\171" => $Y5, "\141\x64\x64\151\164\x69\x6f\156\141\x6c\x46\151\145\x6c\144\163" => array("\146\151\x65\154\x64\x31" => site_url()));
        $ax = wp_json_encode($uj);
        $P_ = array("\x43\157\x6e\x74\x65\x6e\x74\x2d\124\x79\x70\x65" => "\141\160\x70\x6c\151\143\x61\164\151\x6f\x6e\57\152\x73\x6f\x6e");
        $P_["\x43\x75\163\164\x6f\x6d\145\162\x2d\x4b\145\x79"] = $Y5;
        $P_["\124\151\x6d\x65\163\164\141\x6d\x70"] = $AT;
        $P_["\x41\x75\164\x68\157\162\151\x7a\x61\x74\151\157\156"] = $Lm;
        $x1 = array("\x6d\x65\164\150\157\x64" => "\120\x4f\x53\124", "\142\x6f\x64\171" => $ax, "\x74\151\155\x65\157\x75\x74" => "\61\65", "\162\145\144\x69\162\145\143\x74\151\157\x6e" => "\x35", "\150\x74\x74\160\166\x65\162\x73\x69\x6f\156" => "\x31\56\x30", "\x62\x6c\x6f\x63\153\x69\x6e\x67" => true, "\150\145\141\144\x65\162\x73" => $P_);
        $zF = wp_remote_post($QR, $x1);
        if (!is_wp_error($zF)) {
            goto iyH;
        }
        $sa = $zF->get_error_message();
        echo "\123\x6f\155\145\x74\x68\x69\x6e\x67\x20\x77\145\156\164\40\167\162\x6f\156\147\x3a\40{$sa}";
        exit;
        iyH:
        return wp_remote_retrieve_body($zF);
    }
}
