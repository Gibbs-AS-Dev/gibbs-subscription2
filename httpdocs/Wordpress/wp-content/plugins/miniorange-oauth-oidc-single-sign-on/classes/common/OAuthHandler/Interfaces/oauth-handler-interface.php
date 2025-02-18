<?php


namespace MoOauthClient;

interface OauthHandlerInterface
{
    public function get_token($ZO, $x1, $Ao, $Qq);
    public function get_access_token($ZO, $x1, $Ao, $Qq);
    public function get_id_token($ZO, $x1, $Ao, $Qq);
    public function get_resource_owner_from_id_token($JX);
    public function get_resource_owner($yZ, $j6);
    public function get_response($QR);
}
