<?php


namespace MoOauthClient;

class StorageHandler
{
    private $storage;
    public function __construct($H7 = '')
    {
        $Ex = empty($H7) || '' === $H7 ? json_encode([]) : sanitize_text_field(wp_unslash($H7));
        $this->storage = json_decode($Ex, true);
    }
    public function add_replace_entry($NZ, $mB)
    {
        $this->storage[$NZ]["\x56"] = $mB;
        $this->storage[$NZ]["\110"] = md5($mB);
    }
    public function get_value($NZ)
    {
        if (isset($this->storage[$NZ])) {
            goto uq;
        }
        return false;
        uq:
        $mB = $this->storage[$NZ];
        if (!(!is_array($mB) || !isset($mB["\x56"]) || !isset($mB["\110"]))) {
            goto q3;
        }
        return false;
        q3:
        if (!(md5($mB["\126"]) !== $mB["\110"])) {
            goto Ln;
        }
        return false;
        Ln:
        return $mB["\126"];
    }
    public function remove_key($NZ)
    {
        if (!isset($this->storage[$NZ])) {
            goto Ax;
        }
        unset($this->storage[$NZ]);
        Ax:
    }
    public function stringify()
    {
        global $mx;
        $Qr = $this->storage;
        $Qr[\bin2hex("\165\x69\144")]["\x56"] = bin2hex(MO_UID);
        $Qr[\bin2hex("\x75\x69\144")]["\110"] = md5($Qr[\bin2hex("\165\151\x64")]["\x56"]);
        return $mx->base64url_encode(wp_json_encode($Qr));
    }
    public function get_storage()
    {
        return $this->storage;
    }
}
