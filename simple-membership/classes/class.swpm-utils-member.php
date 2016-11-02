<?php

/**
 * SwpmMemberUtils
 * All the utility functions related to member records should be added to this class
 */
class SwpmMemberUtils {

    public static function is_member_logged_in() {
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_logged_in_members_id() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('member_id');
    }

    public static function get_logged_in_members_username() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('user_name');
    }

    public static function get_logged_in_members_level() {
        $auth = SwpmAuth::get_instance();
        if (!$auth->is_logged_in()) {
            return SwpmUtils::_("User is not logged in.");
        }
        return $auth->get('membership_level');
    }

    public static function get_logged_in_members_level_name() {
        $auth = SwpmAuth::get_instance();
        if ($auth->is_logged_in()) {
            return $auth->get('alias');
        }
        return SwpmUtils::_("User is not logged in.");
    }

    public static function get_member_field_by_id($id, $field, $default = '') {
        global $wpdb;
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = %d";
        $userData = $wpdb->get_row($wpdb->prepare($query, $id));
        if (isset($userData->$field)) {
            return $userData->$field;
        }

        return apply_filters('swpm_get_member_field_by_id', $default, $id, $field);
    }
    
    public static function get_expiry_date_timestamp_by_user_id($swpm_id){
        $swpm_user = SwpmMemberUtils::get_user_by_id($swpm_id);
        $expiry_timestamp = SwpmUtils::get_expiration_timestamp($swpm_user);
        return $expiry_timestamp;
    }
    
    public static function get_user_by_id($swpm_id) {
        //Retrieves the SWPM user record for the given member ID
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = %d", $swpm_id);
        $result = $wpdb->get_row($query);
        return $result;
    }
  
    public static function get_user_by_user_name($swpm_user_name) {
        //Retrieves the SWPM user record for the given member username
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE user_name = %s", $swpm_user_name);
        $result = $wpdb->get_row($query);
        return $result;
    }

    public static function get_user_by_email($swpm_email) {
        //Retrieves the SWPM user record for the given member email address
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE email = %s", $swpm_email);
        $result = $wpdb->get_row($query);
        return $result;
    }
    
    public static function is_valid_user_name($user_name){
        return preg_match("/^[a-zA-Z0-9!@#$%&+\/=?^_`{|}~\.-]+$/", $user_name)== 1;
    }
 
    /*
     * Use this function to update or set account status of a member easily.
     */
    public static function update_account_state($member_id, $new_status = 'active') {
        global $wpdb;
        $members_table_name = $wpdb->prefix . "swpm_members_tbl";
        
        SwpmLog::log_simple_debug("Updating the account status value of member (" . $member_id . ") to: " . $new_status, true);
        $query = $wpdb->prepare("UPDATE $members_table_name SET account_state=%s WHERE member_id=%s", $new_status, $member_id);
        $resultset = $wpdb->query($query);
    }


    /*
     * Calculates the Access Starts date value considering the level and current expiry. Useful for after payment member profile update.
     */
    public static function calculate_access_start_date_for_account_update($args){
        $swpm_id = $args['swpm_id'];
        $membership_level = $args['membership_level'];
        $old_membership_level = $args['old_membership_level'];

        $subscription_starts = (date("Y-m-d"));
        if($membership_level == $old_membership_level){
            //Payment for the same membership level (renewal).

            //Algorithm - ONLY set the $subscription_starts date to current expiry date if the current expiry date is in the future. 
            //Otherwise set $subscription_starts to TODAY.
            $expiry_timestamp = SwpmMemberUtils::get_expiry_date_timestamp_by_user_id($swpm_id);
            if($expiry_timestamp > time()){
                //Account is not expired. Expiry date is in the future.
                $level_row = SwpmUtils::get_membership_level_row_by_id($membership_level);
                $subs_duration_type = $level_row->subscription_duration_type;
                if($subs_duration_type == SwpmMembershipLevel::NO_EXPIRY){
                    //No expiry type level.
                    //Use todays date for $subscription_starts date parameter.
                } else if ($subs_duration_type == SwpmMembershipLevel::FIXED_DATE){
                    //Fixed date expiry level.
                    //Use todays date for $subscription_starts date parameter.
                } else {
                    //Duration expiry level.
                    //Set the $subscription_starts date to the current expiry date so the renewal time starts from then.
                    $subscription_starts = date('Y-m-d', $expiry_timestamp);
                }
            } else {
                //Account is already expired.
                //Use todays date for $subscription_starts date parameter.
            }
        } else {
            //Payment for a NEW membership level (upgrade).
            //Use todays date for $subscription_starts date parameter.
        }
        
        return $subscription_starts;
    }

}
