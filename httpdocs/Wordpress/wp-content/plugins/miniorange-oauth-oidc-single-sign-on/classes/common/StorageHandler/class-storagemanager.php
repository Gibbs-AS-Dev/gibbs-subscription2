<?php


namespace MoOauthClient;

use MoOauthClient\StorageHandler;
class StorageManager
{
    private $storage_handler;
    const PRETTY = "\160\x72\145\x74\x74\171";
    const JSON = "\x6a\x73\157\156";
    const RAW = "\x72\141\x77";
    public function __construct($H7 = '')
    {
        $this->storage_handler = new StorageHandler(empty($H7) ? $H7 : base64_decode($H7));
    }
    private function decrypt($JK)
    {
        return empty($JK) || '' === $JK ? $JK : strtolower(hex2bin($JK));
    }
    private function encrypt($JK)
    {
        return empty($JK) || '' === $JK ? $JK : strtoupper(bin2hex($JK));
    }
    public function get_state()
    {
        return $this->storage_handler->stringify();
    }
    public function add_replace_entry($NZ, $mB)
    {
        if ($mB) {
            goto br;
        }
        return;
        br:
        $mB = is_string($mB) ? $mB : wp_json_encode($mB);
        $this->storage_handler->add_replace_entry(bin2hex($NZ), bin2hex($mB));
    }
    public function get_value($NZ)
    {
        $mB = $this->storage_handler->get_value(bin2hex($NZ));
        if ($mB) {
            goto UL;
        }
        return false;
        UL:
        $eY = json_decode(hex2bin($mB), true);
        return json_last_error() === JSON_ERROR_NONE ? $eY : hex2bin($mB);
    }
    public function remove_key($NZ)
    {
        $mB = $this->storage_handler->remove_key(bin2hex($NZ));
    }
    public function validate()
    {
        return $this->storage_handler->validate();
    }
    public function dump_all_storage($ss = self::RAW)
    {
        $Qr = $this->storage_handler->get_storage();
        $LZ = [];
        foreach ($Qr as $NZ => $mB) {
            $Gh = \hex2bin($NZ);
            if ($Gh) {
                goto tS;
            }
            goto pL;
            tS:
            $LZ[$Gh] = $this->get_value($Gh);
            pL:
        }
        Rr:
        switch ($ss) {
            case self::PRETTY:
                echo "\74\160\162\145\x3e";
                print_r($LZ);
                echo "\x3c\57\x70\x72\x65\x3e";
                goto ca;
            case self::JSON:
                echo \json_encode($LZ);
                goto ca;
            default:
            case self::RAW:
                print_r($LZ);
                goto ca;
        }
        Ys:
        ca:
    }
}
