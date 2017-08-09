<?php
$title = isset($instance['title']) ? $instance['title'] : __('Subscribe to my list', 'email-octopus');
$list_id = isset($instance['list_id']) ? $instance['list_id'] : '';
$include_name_fields = isset($instance['include_name_fields']) ? (bool)$instance['include_name_fields'] : false;
$include_referral = isset($instance['include_referral']) ? (bool)$instance['include_referral'] : true;
$success_redirect_url = isset($instance['success_redirect_url']) ? $instance['success_redirect_url'] : '';
$redirect_on_success = !empty($success_redirect_url);

settings_errors();
?>
<div class="email-octopus-widget-options">
    <p>
        <label for="<?php echo $this->get_field_id('list_id'); ?>"><?php _e('List ID:', 'email-octopus'); ?></label>
        <input id="<?php echo $this->get_field_id('list_id'); ?>" name="<?php echo $this->get_field_name('list_id'); ?>" type="text" value="<?php echo esc_attr($list_id); ?>" class="widefat">
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'email-octopus'); ?></label>
        <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" class="widefat">
    </p>
    <p>
        <input type="checkbox" <?php checked($include_name_fields); ?> id="<?php echo $this->get_field_id('include_name_fields'); ?>" name="<?php echo $this->get_field_name('include_name_fields'); ?>" class="checkbox" />
        <label for="<?php echo $this->get_field_id('include_name_fields'); ?>"><?php _e('Include optional name fields', 'email-octopus'); ?></label>
    </p>
    <p>
        <input type="checkbox" <?php checked($include_referral); ?> id="<?php echo $this->get_field_id('include_referral'); ?>" name="<?php echo $this->get_field_name('include_referral'); ?>" class="checkbox" />
        <label for="<?php echo $this->get_field_id('include_referral'); ?>"><?php _e('Include referral link', 'email-octopus'); ?></label>
    </p>
    <p>
        <input type="checkbox" <?php checked($redirect_on_success); ?> id="<?php echo $this->get_field_id('redirect_on_success'); ?>" name="<?php echo $this->get_field_name('redirect_on_success'); ?>" class="checkbox redirect-on-success" />
        <label for="<?php echo $this->get_field_id('redirect_on_success'); ?>"><?php _e('Instead of thanking the user for subscribing, redirect them to a URL', 'email-octopus'); ?></label>
    </p>
    <p class="success-redirect-url-wrapper">
        <input id="<?php echo $this->get_field_id('success_redirect_url'); ?>" name="<?php echo $this->get_field_name('success_redirect_url'); ?>" type="text" value="<?php echo esc_attr($success_redirect_url); ?>" placeholder="URL" class="widefat success-redirect-url">
    </p>
</div>
