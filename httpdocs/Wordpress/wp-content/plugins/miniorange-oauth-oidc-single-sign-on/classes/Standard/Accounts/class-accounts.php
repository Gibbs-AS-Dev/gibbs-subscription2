<?php


namespace MoOauthClient\Paid;

use MoOauthClient\Accounts as CommonAccounts;
class Accounts extends CommonAccounts
{
    public function mo_oauth_lp()
    {
        $It = isset($_POST["\155\x6f\x5f\x6f\x61\x75\164\150\137\143\x6c\151\145\x6e\164\x5f\x6c\x69\x63\x65\156\163\x65\x5f\x6b\x65\x79"]) ? $_POST["\x6d\x6f\137\x6f\141\x75\x74\150\x5f\x63\x6c\151\x65\x6e\164\x5f\x6c\151\143\x65\x6e\x73\145\x5f\x6b\145\x79"] : '';
        echo "\x9\x9\x3c\x64\151\166\40\143\154\x61\x73\x73\75\42\x6d\157\137\x74\141\x62\x6c\x65\x5f\154\141\x79\157\165\x74\42\76\15\12\11\x9\74\x62\162\76\15\xa\11\x9\x9\74\150\x33\x3e\126\145\x72\151\146\x79\40\171\x6f\x75\x72\40\154\x69\x63\x65\156\163\x65\x20\x5b\x20\74\163\160\x61\x6e\40\x73\x74\x79\x6c\x65\75\42\x66\157\156\164\x2d\163\x69\172\145\x3a\61\x33\x70\170\x3b\x66\157\156\x74\55\x73\164\x79\x6c\145\72\x6e\157\x72\155\x61\x6c\73\42\x3e\74\x61\x20\x73\x74\x79\x6c\145\x3d\42\x63\x75\162\x73\x6f\162\x3a\x70\157\151\x6e\164\145\162\x3b\x22\40\150\162\x65\146\75\x22\150\164\164\x70\163\72\x2f\x2f\x6c\x6f\x67\x69\156\56\170\145\x63\165\x72\151\146\171\56\143\x6f\x6d\x2f\155\x6f\141\163\57\x6c\x6f\147\x69\x6e\77\x72\x65\x64\151\x72\145\143\164\x55\162\x6c\75\x68\164\164\x70\x73\x3a\57\x2f\x6c\x6f\147\151\x6e\x2e\x78\145\x63\x75\162\151\146\x79\56\143\x6f\155\57\x6d\157\x61\x73\57\x61\144\155\x69\156\57\x63\165\163\164\157\x6d\145\162\57\x76\151\x65\x77\154\151\x63\x65\x6e\x73\145\153\145\x79\163\42\40\x74\x61\162\147\145\164\x3d\x22\x5f\142\x6c\x61\156\x6b\42\40\157\x6e\x63\x6c\x69\x63\153\75\42\147\145\x74\154\x69\143\145\x6e\163\x65\153\145\x79\x73\x28\x29\42\40\76\x43\154\151\x63\153\x20\150\145\162\x65\40\x74\x6f\40\166\151\145\167\40\171\157\165\162\40\154\151\143\145\x6e\163\x65\x20\x6b\145\x79\74\x2f\x61\x3e\x3c\x2f\163\x70\x61\156\x3e\x20\135\x3c\57\150\x33\x3e\xd\12\40\x20\x20\40\x20\x20\40\x20\40\x20\40\40\x3c\x68\x72\76\15\12\11\11\11\74\146\157\x72\x6d\40\x6e\141\x6d\x65\75\42\146\x22\40\x6d\x65\164\x68\x6f\144\x3d\42\x70\x6f\x73\x74\x22\x20\x61\x63\x74\x69\157\156\x3d\42\42\76\xd\xa\x9\x9\x9\x9\x3c\x69\156\160\x75\x74\40\x74\x79\x70\145\x3d\x22\150\x69\x64\144\x65\156\42\x20\156\x61\x6d\145\x3d\42\157\x70\164\x69\157\x6e\42\40\166\141\x6c\165\145\x3d\42\155\x6f\137\157\141\165\164\150\x5f\x63\x6c\151\145\156\x74\x5f\166\145\162\151\146\171\137\x6c\x69\x63\x65\x6e\163\x65\x22\x20\x2f\x3e\15\12\x9\11\x9\x9";
        wp_nonce_field("\x6d\157\137\x6f\x61\165\x74\150\137\x63\x6c\151\x65\156\x74\x5f\x76\x65\x72\151\x66\x79\x5f\x6c\x69\x63\x65\x6e\163\x65", "\155\x6f\x5f\x6f\x61\165\x74\x68\x5f\x63\154\x69\145\156\x74\x5f\166\145\162\x69\146\x79\x5f\x6c\151\143\x65\156\x73\x65\x5f\x6e\157\156\x63\145");
        echo "\x9\x9\x9\x9\x3c\x74\x61\x62\x6c\x65\x20\x63\x6c\x61\x73\x73\x3d\42\x6d\157\137\x73\x65\164\x74\x69\156\x67\x73\137\164\x61\x62\154\145\x22\76\15\12\11\11\x9\x9\11\x3c\x74\162\76\15\12\x20\40\40\x20\40\40\x20\x20\x20\x20\40\x20\x20\40\x20\x20\x20\x20\40\40\40\40\40\40\74\160\76\x3c\142\76\x3c\x66\x6f\156\164\40\143\157\154\x6f\162\75\x22\43\106\x46\x30\60\x30\x30\42\76\x2a\74\57\146\x6f\156\164\76\x45\156\x74\145\162\40\x79\157\165\x72\x20\x6c\151\143\145\156\163\x65\40\x6b\145\171\x20\x74\157\x20\x61\143\x74\151\166\141\x74\x65\40\x74\150\145\40\160\154\165\147\151\x6e\72\x3c\x2f\142\76\74\142\162\76\74\x62\x72\76\15\12\40\x20\x20\x20\x20\x20\x20\x20\40\x20\x20\40\40\x20\x20\40\40\40\x20\40\x20\x20\40\x20\x20\x20\40\x20\x3c\x69\156\x70\165\x74\x20\143\154\141\163\x73\75\x22\155\x6f\137\x74\141\142\154\145\x5f\x74\x65\170\164\142\157\170\42\x20\162\x65\161\x75\151\x72\145\x64\40\164\x79\x70\x65\x3d\42\x74\x65\170\164\x22\40\x73\x74\171\x6c\145\x3d\42\x6d\x61\x72\147\x69\x6e\55\154\x65\x66\164\x3a\x34\60\x70\x78\x3b\x77\x69\x64\164\150\x3a\63\60\x30\160\170\73\142\x6f\x72\x64\x65\162\x2d\163\164\171\x6c\x65\x3a\x73\157\x6c\151\144\73\x62\157\162\x64\145\x72\x2d\143\x6f\x6c\x6f\x72\x3a\154\151\147\150\x74\x67\x72\x61\x79\x22\x20\156\x61\155\145\75\42\155\157\x5f\x6f\141\165\x74\150\137\143\154\x69\x65\x6e\x74\137\154\151\x63\x65\x6e\163\x65\x5f\x6b\x65\171\42\40\160\x6c\141\x63\x65\150\157\x6c\x64\x65\x72\x3d\x22\105\x6e\164\145\162\x20\171\157\x75\x72\x20\154\x69\143\145\x6e\163\x65\40\x6b\145\x79\x20\164\157\x20\141\x63\164\151\166\x61\164\145\x20\x74\150\x65\x20\160\154\165\x67\151\x6e\x22\x20\x76\141\x6c\x75\x65\75\42";
        echo $It;
        echo "\42\x20\x2f\x3e\74\57\x74\x64\x3e\15\12\x9\x9\x9\11\11\x3c\x2f\164\x72\x3e\xd\xa\40\40\x20\40\x20\40\40\x20\x20\40\x20\x20\40\40\x20\40\40\x20\40\x20\40\40\40\x20\x3c\x2f\160\76\15\12\xd\12\40\x20\x20\40\40\x20\x20\40\40\40\x20\40\x20\40\x20\40\x20\40\40\40\x20\x20\x20\40\x3c\x6f\x6c\76\15\12\x20\x20\x20\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\40\40\40\40\x20\x20\40\40\40\40\x20\x20\40\x20\74\x6c\x69\x3e\x4c\x69\x63\145\x6e\163\x65\x20\153\x65\x79\x20\x79\x6f\165\x20\x68\x61\x76\x65\40\x65\156\x74\x65\x72\x65\x64\40\x68\145\162\x65\40\x69\163\x20\141\163\163\x6f\x63\151\141\x74\x65\x64\x20\x77\151\x74\x68\40\164\x68\151\163\40\163\x69\164\x65\40\x69\x6e\x73\x74\x61\x6e\x63\145\x2e\x20\111\x6e\x20\146\x75\x74\x75\x72\x65\x2c\40\151\x66\x20\x79\x6f\165\x20\x61\162\145\40\x72\x65\x2d\x69\156\163\164\141\x6c\x6c\151\x6e\147\x20\164\150\145\40\x70\x6c\x75\x67\151\x6e\x20\157\x72\40\171\157\x75\x72\x20\x73\151\x74\145\x20\146\x6f\162\x20\141\x6e\171\40\x72\145\141\x73\x6f\156\x2c\40\171\157\165\40\x73\150\x6f\x75\154\x64\x20\x64\x65\x61\x63\x74\151\x76\x61\164\145\40\x74\x68\x65\40\160\x6c\x75\147\x69\x6e\x20\x66\x72\x6f\x6d\40\164\x68\145\40\143\165\x72\162\145\156\x74\x20\167\157\162\x64\160\x72\x65\163\x73\x20\x64\x6f\x6d\x61\x69\x6e\x2e\40\x49\164\40\x77\x6f\x75\154\x64\x20\146\162\145\145\40\x79\157\x75\x72\x20\114\151\x63\x65\156\163\x65\40\x4b\145\171\40\x61\x6e\x64\x20\141\x6c\154\157\167\40\x79\157\165\40\x74\157\x20\141\x63\164\151\x76\141\x74\x65\x20\164\150\151\x73\x20\160\x6c\165\x67\x69\x6e\40\x6f\x6e\x20\x6f\164\150\x65\x72\40\x64\x6f\x6d\141\x69\156\x2f\x73\x69\x74\145\56\74\x2f\x6c\x69\x3e\74\x62\162\x3e\15\xa\x20\40\40\x20\x20\x20\x20\x20\40\x20\40\x20\x20\x20\40\x20\x20\40\40\40\40\40\40\x20\x20\40\x20\40\74\154\151\76\74\142\x3e\x54\150\x69\x73\x20\x69\x73\x20\156\157\x74\40\141\40\x64\x65\x76\145\x6c\x6f\160\x65\x72\47\x73\40\x6c\x69\143\x65\x6e\x73\145\56\x3c\57\x62\x3e\40\x59\157\165\40\x6d\x61\171\x20\x6e\x6f\164\40\155\157\144\x69\x66\171\40\164\150\x65\40\x63\157\x6e\x74\x65\x6e\164\40\157\x72\x20\141\x6e\x79\x20\x70\141\x72\164\x20\164\x68\145\x72\145\157\146\x2c\40\x65\x78\x63\x65\x70\x74\40\141\163\40\x65\170\x70\x6c\x69\143\151\x74\154\x79\x20\160\x65\x72\x6d\x69\164\x74\145\x64\40\165\156\x64\x65\162\40\164\x68\x69\x73\40\x70\x6c\x75\x67\151\x6e\x2e\x20\x4d\x61\x6b\x69\156\147\x20\x61\156\x79\40\153\151\156\x64\40\x6f\146\40\x63\x68\x61\x6e\147\x65\40\x74\157\40\x74\x68\145\x20\x70\154\165\147\151\x6e\47\163\x20\143\157\x64\x65\40\155\141\x79\40\x6d\141\153\x65\x20\x74\150\145\40\x70\154\165\x67\151\156\40\x75\x6e\x75\163\x61\x62\154\x65\x2e\74\57\x6c\151\76\15\xa\x20\x20\40\40\x20\40\40\x20\40\x20\40\x20\40\x20\x20\40\x20\x20\40\40\40\x20\x20\x20\74\x2f\157\154\x3e\xd\xa\40\40\x20\40\40\x20\40\x20\x20\x20\40\40\40\40\40\40\x20\x20\40\x20\40\40\x20\40\74\x70\x3e\74\142\x3e\46\x6e\142\163\160\x3b\x3c\x69\156\x70\x75\x74\x20\163\164\x79\154\x65\75\42\155\x61\162\x67\151\x6e\55\154\x65\x66\164\x3a\x32\x30\160\170\73\x22\40\162\145\x71\165\151\x72\x65\x64\x20\x74\171\x70\x65\x3d\x22\x63\x68\145\143\x6b\x62\157\170\x22\x20\x6e\141\155\x65\75\42\154\x69\x63\x65\156\x73\x65\x5f\x63\157\156\x64\151\164\151\157\x6e\x73\x22\x20\x27\73\xd\xa\x20\40\x20\x20\x20\40\40\40\40\x20\40\40\40\x20\x20\x20\40\40\40\40\40\40\40\40\145\x63\150\157\40\x27\x2f\x3e\46\x6e\x62\163\x70\x3b\46\x6e\142\x73\160\73\111\x20\141\x63\143\x65\160\164\40\x74\150\x65\40\x61\x62\x6f\x76\x65\40\124\145\162\155\x73\x20\x61\x6e\144\x20\103\157\156\x64\x69\164\151\x6f\x6e\x73\x2e\74\57\160\76\15\12\40\x20\x20\x20\40\x20\40\40\x20\x20\40\x20\x20\40\40\40\x20\40\40\x20\40\40\x20\40\74\142\x72\x3e\15\12\11\x9\11\x9\x9\74\164\x72\x3e\15\12\11\11\x9\x9\11\11\x3c\x74\144\x3e\46\156\x62\x73\160\73\74\57\164\144\76\xd\xa\11\x9\11\x9\x9\11\x3c\164\x64\76\xd\xa\11\11\x9\11\x9\x9\x9\x3c\x69\x6e\x70\165\164\40\x74\171\160\x65\75\42\x73\x75\x62\155\151\x74\42\x20\156\141\155\x65\75\x22\163\x75\x62\155\x69\x74\42\x20\x76\141\154\x75\x65\75\42\x41\x63\164\x69\x76\x61\164\x65\40\114\151\143\x65\156\163\145\x22\40\143\154\x61\x73\x73\x3d\42\x62\165\x74\164\x6f\x6e\40\142\x75\164\x74\157\x6e\x2d\160\162\x69\155\x61\x72\171\40\x62\165\164\x74\157\x6e\55\154\141\x72\x67\145\x22\40\57\x3e\x26\x6e\142\x73\x70\x3b\x26\x6e\x62\x73\160\x3b\46\156\x62\x73\160\x3b\46\x6e\x62\163\160\73\46\156\x62\163\160\73\x26\x6e\142\x73\160\x3b\x3c\57\146\x6f\x72\x6d\x3e\xd\12\x9\x9\11\x9\11\11\11\15\12\11\x9\11\x9\11\11\11\x3c\151\156\x70\x75\164\x20\164\x79\160\145\75\42\x62\x75\x74\164\157\x6e\x22\x20\156\141\155\145\x3d\x22\143\150\x61\x6e\x67\145\55\141\x63\143\x6f\165\x6e\164\55\x62\x75\x74\x74\x6f\156\42\40\x69\x64\x3d\x22\155\157\x5f\x6f\x61\x75\x74\150\x5f\143\150\x61\156\147\x65\137\x61\143\x63\157\x75\156\x74\x5f\142\165\x74\164\x6f\156\x22\40\157\156\x63\154\x69\143\x6b\75\x22\144\x6f\143\165\155\145\156\164\x2e\x67\145\164\x45\x6c\145\155\145\156\x74\102\x79\x49\144\x28\47\155\x6f\137\x6f\141\x75\x74\150\x5f\147\157\x74\157\x5f\154\x6f\147\151\x6e\x5f\x66\157\162\155\47\x29\56\x73\165\x62\155\x69\164\x28\x29\x3b\42\40\166\141\x6c\165\145\x3d\42\102\x61\143\x6b\42\x20\143\154\x61\163\163\x3d\42\x62\x75\x74\164\x6f\x6e\x20\x62\165\164\164\x6f\x6e\x2d\x70\x72\151\x6d\141\162\x79\40\x62\165\x74\x74\x6f\x6e\x2d\x6c\x61\162\x67\x65\42\40\57\76\xd\xa\xd\12\11\x9\11\11\11\x9\x9\x3c\146\x6f\x72\155\x20\x6e\141\x6d\x65\x3d\x22\x66\x31\42\x20\x6d\x65\164\x68\x6f\144\x3d\42\160\x6f\x73\164\42\x20\x61\143\x74\151\157\156\75\x22\42\40\x69\x64\75\42\155\x6f\x5f\157\141\165\x74\150\137\x67\157\x74\157\x5f\154\157\x67\151\156\137\x66\157\x72\155\42\76\15\xa\x9\x9\11\x9\11\x9\x9\x9\74\151\156\160\x75\164\x20\164\171\x70\145\75\x22\150\x69\144\144\145\156\42\40\166\141\x6c\x75\x65\x3d\42\143\x68\141\x6e\x67\145\x5f\x6d\151\x6e\x69\x6f\x72\x61\156\x67\145\x22\40\x6e\141\155\145\75\42\157\160\x74\x69\157\156\x22\x2f\76\xd\xa\11\11\11\11\11\11\11\x9";
        wp_nonce_field("\143\x68\141\x6e\147\145\137\155\151\x6e\151\x6f\x72\x61\x6e\147\145", "\143\150\x61\x6e\x67\x65\137\x6d\x69\x6e\x69\x6f\x72\141\x6e\x67\145\137\156\157\156\x63\x65");
        echo "\11\11\11\x9\x9\x9\11\74\x2f\146\x6f\162\x6d\x3e\15\12\11\x9\11\x9\x9\x9\74\x2f\164\144\76\15\12\x9\11\11\x9\11\x3c\x2f\164\x72\x3e\xd\xa\11\11\x9\11\11\x3c\164\x72\76\74\164\144\x3e\x26\x6e\142\x73\x70\73\x3c\57\x74\144\x3e\x3c\164\x64\x3e\74\57\164\x64\76\74\x2f\x74\162\x3e\xd\xa\11\11\x9\x9\11\x3c\x74\162\x3e\x3c\164\x64\x3e\46\x6e\x62\163\160\x3b\74\x2f\x74\x64\x3e\x3c\164\144\x3e\x3c\57\x74\144\x3e\x3c\57\164\x72\x3e\xd\12\11\11\11\x9\74\x2f\x74\141\142\x6c\x65\x3e\xd\xa\x9\x9\x3c\x2f\x64\151\x76\76\xd\12\11\x9";
    }
}
