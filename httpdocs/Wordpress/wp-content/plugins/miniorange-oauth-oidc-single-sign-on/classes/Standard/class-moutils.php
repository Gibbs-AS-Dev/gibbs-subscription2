<?php


namespace MoOauthClient\Standard;

use MoOauthClient\MOUtils as CommonUtils;
class MOUtils extends CommonUtils
{
    private function manage_deactivate_cache()
    {
        global $mx;
        $zu = $mx->mo_oauth_client_get_option("\155\x6f\137\157\x61\x75\164\150\x5f\154\153");
        if (!(!$mx->mo_oauth_is_customer_registered() || false === $zu || empty($zu))) {
            goto Grm;
        }
        return;
        Grm:
        $oX = $mx->mo_oauth_client_get_option("\x68\157\x73\164\137\156\141\155\x65");
        $QR = $oX . "\x2f\x6d\x6f\x61\163\57\141\x70\151\57\x62\x61\143\x6b\165\160\x63\157\144\x65\x2f\165\160\x64\141\x74\145\163\x74\141\x74\x75\163";
        $Y5 = $mx->mo_oauth_client_get_option("\155\x6f\x5f\x6f\x61\x75\x74\150\137\141\144\155\151\156\137\x63\x75\x73\x74\x6f\x6d\145\x72\x5f\x6b\145\171");
        $Jb = $mx->mo_oauth_client_get_option("\155\157\137\157\141\x75\164\150\x5f\x61\x64\155\x69\x6e\x5f\141\x70\x69\x5f\153\145\171");
        $It = $mx->mooauthdecrypt($zu);
        $AT = round(microtime(true) * 1000);
        $AT = number_format($AT, 0, '', '');
        $iU = $Y5 . $AT . $Jb;
        $Lm = hash("\x73\x68\141\65\61\x32", $iU);
        $Zm = "\103\165\163\x74\157\x6d\x65\x72\55\113\145\x79\72\40" . $Y5;
        $zA = "\124\x69\155\x65\x73\164\141\155\160\72\40" . $AT;
        $BH = "\x41\165\164\150\x6f\x72\151\x7a\141\x74\151\x6f\x6e\x3a\40" . $Lm;
        $uj = '';
        $uj = array("\x63\x6f\144\x65" => $It, "\x63\x75\163\x74\x6f\x6d\145\x72\113\145\171" => $Y5, "\x61\x64\x64\x69\164\x69\157\x6e\141\154\x46\151\x65\x6c\x64\163" => array("\x66\x69\x65\154\144\61" => site_url()));
        $ax = wp_json_encode($uj);
        $P_ = array("\103\x6f\156\164\x65\x6e\164\55\x54\x79\160\145" => "\x61\x70\x70\154\x69\143\x61\x74\x69\x6f\x6e\x2f\x6a\x73\157\x6e");
        $P_["\103\x75\x73\164\157\x6d\x65\162\55\113\145\x79"] = $Y5;
        $P_["\x54\x69\155\x65\163\x74\141\x6d\x70"] = $AT;
        $P_["\x41\x75\164\150\x6f\x72\x69\x7a\x61\x74\x69\157\x6e"] = $Lm;
        $x1 = array("\x6d\x65\x74\150\157\144" => "\x50\x4f\x53\x54", "\142\157\x64\x79" => $ax, "\x74\151\x6d\x65\157\x75\164" => "\x31\65", "\x72\145\x64\x69\x72\145\143\x74\151\x6f\156" => "\65", "\x68\x74\164\x70\166\x65\x72\163\x69\157\156" => "\61\56\60", "\x62\x6c\157\x63\153\151\156\147" => true, "\150\x65\141\x64\145\162\163" => $P_);
        $zF = wp_remote_post($QR, $x1);
        if (!is_wp_error($zF)) {
            goto fov;
        }
        $sa = $zF->get_error_message();
        echo "\123\x6f\x6d\145\164\x68\x69\x6e\x67\40\x77\145\156\164\40\x77\x72\157\156\147\x3a\x20{$sa}";
        exit;
        fov:
        return wp_remote_retrieve_body($zF);
    }
    public function deactivate_plugin()
    {
        $this->manage_deactivate_cache();
        parent::deactivate_plugin();
        $this->mo_oauth_client_delete_option("\x6d\x6f\x5f\x6f\141\165\x74\x68\x5f\154\153");
        $this->mo_oauth_client_delete_option("\155\x6f\x5f\157\x61\x75\x74\x68\x5f\x6c\166");
    }
    public function is_url($QR)
    {
        $zF = [];
        if (empty($QR)) {
            goto eHN;
        }
        $zF = @get_headers($QR) ? @get_headers($QR) : [];
        eHN:
        $Wi = preg_grep("\57\50\x2e\x2a\x29\x32\x30\60\x20\117\113\x2f", $zF);
        return (bool) (sizeof($Wi) > 0);
    }
}
