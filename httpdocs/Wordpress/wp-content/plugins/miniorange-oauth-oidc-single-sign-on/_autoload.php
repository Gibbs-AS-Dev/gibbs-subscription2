<?php


if (defined("\x41\x42\x53\x50\x41\x54\x48")) {
    goto wCn;
}
exit;
wCn:
define("\x4d\117\103\x5f\104\x49\x52", plugin_dir_path(__FILE__));
define("\115\x4f\103\137\x55\122\114", plugin_dir_url(__FILE__));
define("\115\117\x5f\125\111\x44", "\x44\x46\70\x56\x4b\112\117\x35\x46\x44\x48\132\x41\122\x42\122\x35\x5a\104\123\62\126\x35\112\x36\66\125\x32\116\104\122");
define("\126\105\122\x53\111\117\116", "\155\157\137\141\x6c\154\137\151\156\143\x6c\x75\163\151\x76\145\137\166\145\162\163\151\157\156");
mo_oauth_include_file(MOC_DIR . "\57\x63\154\141\163\x73\145\x73\57\143\157\x6d\x6d\157\156");
mo_oauth_include_file(MOC_DIR . "\57\x63\x6c\141\x73\x73\x65\x73\57\106\x72\145\x65");
mo_oauth_include_file(MOC_DIR . "\57\x63\x6c\141\x73\163\145\x73\x2f\x53\164\141\x6e\144\x61\162\x64");
mo_oauth_include_file(MOC_DIR . "\57\143\x6c\141\x73\163\145\163\57\x50\162\x65\155\x69\x75\155");
mo_oauth_include_file(MOC_DIR . "\x2f\x63\x6c\141\163\163\145\x73\57\x45\156\164\x65\x72\x70\x72\151\163\145");
function mo_oauth_get_dir_contents($cV, &$ly = array())
{
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cV, RecursiveDirectoryIterator::KEY_AS_PATHNAME), RecursiveIteratorIterator::CHILD_FIRST) as $sk => $f5) {
        if (!($f5->isFile() && $f5->isReadable())) {
            goto AHH;
        }
        $ly[$sk] = realpath($f5->getPathname());
        AHH:
        ESL:
    }
    OB2:
    return $ly;
}
function mo_oauth_get_sorted_files($cV)
{
    $Lu = mo_oauth_get_dir_contents($cV);
    $cr = array();
    $FI = array();
    foreach ($Lu as $sk => $Dw) {
        if (!(strpos($Dw, "\56\160\x68\x70") !== false)) {
            goto LF0;
        }
        if (strpos($Dw, "\111\x6e\x74\145\162\146\x61\x63\x65") !== false) {
            goto Jnh;
        }
        $FI[$sk] = $Dw;
        goto m2x;
        Jnh:
        $cr[$sk] = $Dw;
        m2x:
        LF0:
        fKw:
    }
    iJA:
    return array("\151\x6e\164\145\162\x66\141\x63\x65\163" => $cr, "\143\x6c\141\163\163\145\163" => $FI);
}
function mo_oauth_include_file($cV)
{
    if (is_dir($cV)) {
        goto l2b;
    }
    return;
    l2b:
    $cV = mo_oauth_sane_dir_path($cV);
    $SP = realpath($cV);
    if (!(false !== $SP && !is_dir($cV))) {
        goto igU;
    }
    return;
    igU:
    $UE = mo_oauth_get_sorted_files($cV);
    mo_oauth_require_all($UE["\151\156\x74\145\162\x66\141\x63\145\x73"]);
    mo_oauth_require_all($UE["\143\x6c\141\x73\163\145\163"]);
}
function mo_oauth_require_all($Lu)
{
    foreach ($Lu as $sk => $Dw) {
        require_once $Dw;
        t7o:
    }
    rO5:
}
function mo_oauth_is_valid_file($DW)
{
    return '' !== $DW && "\56" !== $DW && "\x2e\56" !== $DW;
}
function mo_oauth_get_valid_html($x1 = array())
{
    $RQ = array("\x73\164\x72\x6f\156\147" => array(), "\145\x6d" => array(), "\x62" => array(), "\151" => array(), "\141" => array("\x68\x72\145\x66" => array(), "\164\x61\x72\x67\x65\164" => array()));
    if (empty($x1)) {
        goto WDW;
    }
    return array_merge($x1, $RQ);
    WDW:
    return $RQ;
}
function mo_oauth_get_version_number()
{
    $c3 = get_file_data(MOC_DIR . "\57\x6d\157\x5f\x6f\x61\x75\x74\x68\137\163\x65\164\x74\x69\156\147\163\x2e\160\x68\x70", ["\126\145\162\163\151\x6f\156"], "\x70\x6c\x75\x67\x69\x6e");
    $xd = isset($c3[0]) ? $c3[0] : '';
    return $xd;
}
function mo_oauth_sane_dir_path($cV)
{
    return str_replace("\57", DIRECTORY_SEPARATOR, $cV);
}
if (!function_exists("\x6d\x6f\137\x6f\x61\165\164\x68\137\x69\x73\x5f\x72\x65\163\x74")) {
    function mo_oauth_is_rest()
    {
        $gM = rest_get_url_prefix();
        if (!(defined("\x52\105\x53\124\137\x52\x45\121\x55\x45\x53\x54") && REST_REQUEST || isset($_GET["\162\x65\x73\x74\137\162\157\x75\164\145"]) && strpos(trim($_GET["\x72\145\163\164\x5f\162\157\x75\x74\145"], "\x5c\57"), $gM, 0) === 0)) {
            goto qVW;
        }
        return true;
        qVW:
        global $he;
        if (!($he === null)) {
            goto mP5;
        }
        $he = new WP_Rewrite();
        mP5:
        $cm = wp_parse_url(trailingslashit(rest_url()));
        $XI = wp_parse_url(add_query_arg(array()));
        return strpos($XI["\160\x61\164\x68"], $cm["\160\x61\x74\x68"], 0) === 0;
    }
}
