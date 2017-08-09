<?php
$title = isset($instance['title']) ? $instance['title'] : __('Subscribe to my list', 'emailoctopus');
$list_id = isset($instance['list_id']) ? $instance['list_id'] : '';
$include_name_fields = isset($instance['include_name_fields']) ? (bool)$instance['include_name_fields'] : false;
$include_referral = isset($instance['include_referral']) ? (bool)$instance['include_referral'] : false;
$success_redirect_url = isset($instance['success_redirect_url']) ? $instance['success_redirect_url'] : '';

if (is_user_logged_in() && strlen($list_id) !== 36):
?>
    <p class="emailoctopus-error-message">
        <?php
        _e('Before you can use the EmailOctopus widget, you need to provide a valid list for it to connect to.', 'emailoctopus');
        echo '<br><br>';
        _e('Take a look at the <a href="https://wordpress.org/plugins/emailoctopus/installation/" target="_blank">installation page</a> for more information.', 'emailoctopus');
        ?>
    </p>
<?php
else:
?>
    <div class="emailoctopus-form-wrapper">
        <?php
            if (trim($title)) {
                echo sprintf(
                    '<h2 class="emailoctopus-heading">%s</h2>',
                    esc_attr($title)
                );
            }
        ?>
        <p class="emailoctopus-success-message"></p>
        <p class="emailoctopus-error-message"></p>
        <form method="post" action="https://emailoctopus.com/lists/<?php echo esc_attr($list_id); ?>/members/external-add" class="emailoctopus-form">
            <div class="emailoctopus-form-row">
                <label><?php _e('Email address (required)', 'emailoctopus'); ?></label>
                <input type="email" name="emailAddress" class="emailoctopus-email-address"></input>
            </div>
            <?php
            if ($include_name_fields):
            ?>
                <div class="emailoctopus-form-row">
                    <label><?php _e('First name', 'emailoctopus'); ?></label>
                    <input type="text" name="firstName" class="emailoctopus-first-name"></input>
                </div>
                <div class="emailoctopus-form-row">
                    <label><?php _e('Last name', 'emailoctopus'); ?></label>
                    <input type="text" name="lastName" class="emailoctopus-last-name"></input>
                </div>
            <?php
            endif;
            ?>
            <div class="emailoctopus-form-row-hp" aria-hidden="true">
                <!-- Do not remove this field, otherwise you risk bot signups -->
                <input type="text" name="hp<?php echo esc_attr($list_id); ?>" class="emailoctopus-hp" tabindex="-1"></input>
            </div>
            <div class="emailoctopus-form-row-subscribe">
                <input type="hidden" name="successRedirectUrl" class="emailoctopus-success-redirect-url" value="<?php echo esc_attr($success_redirect_url); ?>"></input>
                <button type="submit"><?php _e('Subscribe', 'emailoctopus'); ?></button>
            </div>
        </form>
        <?php
        if ($include_referral):
        ?>
            <div class="emailoctopus-referral">
                <?php
                _e('Powered by <a href="https://emailoctopus.com?utm_source=form&utm_medium=wordpress_plugin">EmailOctopus</a>', 'emailoctopus');
                ?>
            </div>
        <?php
        endif;
        ?>
    </div>
<?php
endif;
?>
