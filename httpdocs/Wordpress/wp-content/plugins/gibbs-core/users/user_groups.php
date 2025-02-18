<?php

class UserGroups
{
    // Return the ID of the currently active user group.
    public static function get_active_group_id()
    {
        $active_group_id = get_user_meta(get_current_user_id(), '_gibbs_active_group_id', true);
        if ($active_group_id != '')
        {
            return $active_group_id;
        }
        return '0';
    }
}

?>