<?php
$auth = SwpmAuth::get_instance();
$user_data = (array) $auth->userData;
$user_data['membership_level_alias'] = $auth->get('alias');
extract($user_data, EXTR_SKIP);
$settings = SwpmSettings::get_instance();
$force_strong_pass = $settings->get_value('force-strong-passwords');
if (!empty($force_strong_pass)) {
    $pass_class = apply_filters("swpm_profile_strong_pass_validation", "validate[custom[strongPass],minSize[8]]");
} else {
    $pass_class = "";
}
SimpleWpMembership::enqueue_validation_scripts();
?>

<div class="swpm-edit-profile-form">
    <form id="swpm-editprofile-form" name="swpm-editprofile-form" method="post" action="" class="swpm-validate-form">
        <?php wp_nonce_field('swpm_profile_edit_nonce_action', 'swpm_profile_edit_nonce_val'); ?>

        <!-- Username -->
        <div class="swpm-form-field">
            <?php apply_filters('swpm_edit_profile_form_before_username', ''); ?>
            <label for="user_name"><?php echo SwpmUtils::_('Username'); ?></label>
            <div><?php echo $user_name; ?></div>
        </div>

        <!-- Email -->
        <div class="swpm-form-field">
            <label for="email"><?php echo SwpmUtils::_('Email'); ?></label>
            <input type="text" id="email" name="email" class="swpm-text-field" value="<?php echo esc_attr($email); ?>" autocomplete="off" />
        </div>

        <!-- Password -->
        <div class="swpm-form-field">
            <label for="password"><?php echo SwpmUtils::_('Password'); ?></label>
            <input type="password" id="password" class="<?php echo esc_attr($pass_class); ?> swpm-text-field" name="password" autocomplete="off" placeholder="<?php echo SwpmUtils::_('Leave empty to keep the current password'); ?>" />
        </div>

        <!-- Repeat Password -->
        <div class="swpm-form-field">
            <label for="password_re"><?php echo SwpmUtils::_('Repeat Password'); ?></label>
            <input type="password" id="password_re" class="swpm-text-field" name="password_re" autocomplete="off" placeholder="<?php echo SwpmUtils::_('Leave empty to keep the current password'); ?>" />
        </div>

        <!-- First Name -->
        <div class="swpm-form-field">
            <label for="first_name"><?php echo SwpmUtils::_('First Name'); ?></label>
            <input type="text" id="first_name" name="first_name" class="swpm-text-field" value="<?php echo esc_attr($first_name); ?>" />
        </div>

        <!-- Last Name -->
        <div class="swpm-form-field">
            <label for="last_name"><?php echo SwpmUtils::_('Last Name'); ?></label>
            <input type="text" id="last_name" name="last_name" class="swpm-text-field" value="<?php echo esc_attr($last_name); ?>" />
        </div>

        <!-- Phone -->
        <div class="swpm-form-field">
            <label for="phone"><?php echo SwpmUtils::_('Phone'); ?></label>
            <input type="text" id="phone" name="phone" class="swpm-text-field" value="<?php echo esc_attr($phone); ?>" />
        </div>

        <!-- Street -->
        <div class="swpm-form-field">
            <label for="address_street"><?php echo SwpmUtils::_('Street'); ?></label>
            <input type="text" id="address_street" name="address_street" class="swpm-text-field" value="<?php echo esc_attr($address_street); ?>" />
        </div>

        <!-- City -->
        <div class="swpm-form-field">
            <label for="address_city"><?php echo SwpmUtils::_('City'); ?></label>
            <input type="text" id="address_city" name="address_city" class="swpm-text-field" value="<?php echo esc_attr($address_city); ?>" />
        </div>

        <!-- State -->
        <div class="swpm-form-field">
            <label for="address_state"><?php echo SwpmUtils::_('State'); ?></label>
            <input type="text" id="address_state" name="address_state" class="swpm-text-field" value="<?php echo esc_attr($address_state); ?>" />
        </div>

        <!-- Zipcode -->
        <div class="swpm-form-field">
            <label for="address_zipcode"><?php echo SwpmUtils::_('Zipcode'); ?></label>
            <input type="text" id="address_zipcode" name="address_zipcode" class="swpm-text-field" value="<?php echo esc_attr($address_zipcode); ?>" />
        </div>

        <!-- Country -->
        <div class="swpm-form-field">
            <label for="country"><?php echo SwpmUtils::_('Country'); ?></label>
            <select id="country" name="country" class="swpm-text-field"><?php echo SwpmMiscUtils::get_countries_dropdown($country); ?></select>
        </div>

        <!-- Company Name -->
        <div class="swpm-form-field">
            <label for="company_name"><?php echo SwpmUtils::_('Company Name'); ?></label>
            <input type="text" id="company_name" name="company_name" class="swpm-text-field" value="<?php echo esc_attr($company_name); ?>" />
        </div>

        <!-- Membership Level (non-editable, for display only) -->
        <div class="swpm-form-field">
            <?php apply_filters('swpm_edit_profile_form_membership_level_tr_attributes', ''); ?>
            <label><?php echo SwpmUtils::_('Membership Level'); ?></label>
            <div><?php echo $membership_level_alias; ?></div>
        </div>

        <div class="swpm-edit-profile-submit-section">
            <input type="submit" value="<?php echo SwpmUtils::_('Update'); ?>" class="swpm-button" name="swpm_editprofile_submit" />
        </div>

        <?php echo SwpmUtils::delete_account_button(); ?>

        <input type="hidden" name="action" value="custom_posts" />
    </form>
</div>
