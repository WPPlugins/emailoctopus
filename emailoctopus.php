<?php
/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package   Email_Octopus
 * @author    EmailOctopus <contact@emailoctopus.com>
 * @license   GPL-2.0+
 * @link      https://emailoctopus.com
 * @copyright 2016 EmailOctopus
 *
 * @wordpress-plugin
 * Plugin Name:       EmailOctopus
 * Plugin URI:        https://emailoctopus.com
 * Description:       Email marketing widget; easily add subscribers to an EmailOctopus list. Fully customisable.
 * Version:           1.3.3
 * Author:            EmailOctopus
 * Author URI:        https://emailoctopus.com
 * Text Domain:       emailoctopus
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

 // Prevent direct file access
if (!defined('ABSPATH')) {
    die;
}

class EmailOctopus extends WP_Widget
{
    /**
     * Unique identifier for the widget, used as the text domain when
     * internationalizing strings of text.
     *
     * @var string
     */
    protected $widget_slug = 'emailoctopus';

    /**
     * Specifies the classname and description, instantiates the widget, loads
     * localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct()
    {
        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        // Instantiate main class
        parent::__construct(
            $this->get_widget_slug(),
            __('EmailOctopus', $this->get_widget_slug()),
            array(
                'classname'  => $this->get_widget_slug().'-class',
                'description' => __(
                    'A form for subscribing to an EmailOctopus list.',
                    $this->get_widget_slug()
                )
            )
        );

        // Register admin styles and scripts
        add_action(
            'admin_echo_styles',
            array($this, 'register_admin_styles')
        );
        add_action(
            'admin_enqueue_scripts',
            array($this, 'register_admin_scripts')
        );

        // Register site styles and scripts
        add_action(
            'wp_enqueue_scripts',
            array($this, 'register_widget_styles')
        );
        add_action(
            'wp_enqueue_scripts',
            array($this, 'register_widget_scripts')
        );
    }


    /**
     * Returns the widget slug.
     *
     * @return Plugin slug.
     */
    public function get_widget_slug() {
        return $this->widget_slug;
    }

    /**
     * Outputs the content of the widget.
     *
     * @param array args     The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget($args, $instance)
    {
        // Check if there is a cached output
        $cache = wp_cache_get($this->get_widget_slug(), 'widget');

        if (!is_array($cache)) {
            $cache = array();
        }

        if (isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        if (isset($cache[$args['widget_id']])) {
            return print $cache[$args['widget_id']];
        }

        extract($args, EXTR_SKIP);

        $widget_output = $before_widget;
        ob_start();
        include(plugin_dir_path(__FILE__) . 'views/widget.php');
        $widget_output .= ob_get_clean();
        $widget_output .= $after_widget;

        $cache[$args['widget_id']] = $widget_output;

        wp_cache_set($this->get_widget_slug(), $cache, 'widget');

        echo $widget_output;
    }

    /**
     * Flushes the widget's cache.
     */
    public function flush_widget_cache()
    {
        wp_cache_delete($this->get_widget_slug(), 'widget');
    }

    /**
     * Processes the widget's options to be saved.
     *
     * @param array new_instance The new instance of values to be generated via the update.
     * @param array old_instance The previous instance of values before the update.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array(
            'title' => strip_tags($new_instance['title']),
            'list_id' => strip_tags($new_instance['list_id']),
            'include_name_fields' =>
                strip_tags($new_instance['include_name_fields']),
            'include_referral' => strip_tags($new_instance['include_referral']),
            'success_redirect_url' =>
                strip_tags($new_instance['success_redirect_url']),
        );

        if (isset($new_instance['redirect_on_success'])) {
            $url = $new_instance['success_redirect_url'];
            if (empty($url)) {
                add_settings_error(
                    'success_redirect_url',
                    'success-redirect-url-missing',
                    'To redirect your subscriber, a URL is required'
                );
                $instance['success_redirect_url'] = '';
            } elseif (filter_var($url, FILTER_VALIDATE_URL) === false) {
                add_settings_error(
                    'success_redirect_url',
                    'success-redirect-url-invalid',
                    'To redirect your subscriber, a full and valid URL is required, e.g. http://yoururl.com'
                );
                $instance['success_redirect_url'] = '';
            }
        } else {
            $instance['success_redirect_url'] = '';
        }

        return $instance;
    }

    /**
     * Generates the administration form for the widget.
     *
     * @param array instance The array of keys and values for the widget.
     */
    public function form($instance)
    {
        $instance = wp_parse_args((array)$instance);

        // Display the admin form
        include(plugin_dir_path(__FILE__) . 'views/admin.php');
    }

    /**
     * Loads the widget's text domain for localization and translation.
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            $this->get_widget_slug(),
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Fired when the plugin is activated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
     */
    public function activate($network_wide)
    {
        // TODO: Needed?
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public function deactivate($network_wide)
    {
        // TODO: Needed?
    }

    /**
     * Registers and enqueues admin-specific styles.
     */
    public function register_admin_styles()
    {
        wp_enqueue_style(
            $this->get_widget_slug() . '-admin-styles',
            plugins_url('css/admin.css', __FILE__)
        );
    }

    /**
     * Registers and enqueues admin-specific JavaScript.
     */
    public function register_admin_scripts()
    {
        wp_enqueue_script(
            $this->get_widget_slug() . '-admin-script',
            plugins_url('js/admin.js', __FILE__),
            array('jquery')
        );
    }

    /**
     * Registers and enqueues widget-specific styles.
     */
    public function register_widget_styles()
    {
        wp_enqueue_style(
            $this->get_widget_slug() . '-widget-styles',
            plugins_url('css/widget.css', __FILE__)
        );
    }

    /**
     * Registers and enqueues widget-specific scripts.
     */
    public function register_widget_scripts()
    {
        $handle = $this->get_widget_slug() . '-admin-script';

        wp_enqueue_script(
            $handle,
            plugins_url('js/widget.js', __FILE__),
            array('jquery')
        );
        wp_localize_script(
            $handle,
            $this->get_widget_slug() . '_message',
            array(
                'success' => __('Thanks for subscribing!'),
                'missing_email_address_error' => __('Your email address is required.', $this->get_widget_slug()),
                'invalid_email_address_error' => __('Your email address looks incorrect, please try again.', $this->get_widget_slug()),
                'bot_submission_error' => __('This doesn\'t look like a human submission.', $this->get_widget_slug()),
                'invalid_parameters_error' => __('This form has missing or invalid fields.', $this->get_widget_slug()),
                'unknown_error' => __('Sorry, an unknown error has occurred. Please try again later.', $this->get_widget_slug()),
            )
        );
    }
}

add_action('widgets_init', create_function('', 'register_widget("EmailOctopus");'));
