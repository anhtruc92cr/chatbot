<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://soxes.ch/
 * @since             1.0.0
 * @package           Soxes_Chatbot
 *
 * @wordpress-plugin
 * Plugin Name:       Soxes chatbot
 * Plugin URI:        https://soxes.ch/
 * Description:       This plugin will generate a chatbot in Frontend. Required ACF PRO and Gravity form.
 * Version:           1.0.4
 * Author:            Truc Nguyen
 * Author URI:        https://soxes.ch/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       soxes-chatbot
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SOXES_CHATBOT_VERSION', '1.1.13');
define('SOXES_CHATBOT_NAME', 'soxes-chatbot');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-soxes-chatbot-activator.php
 */
function activate_soxes_chatbot()
{
    // Require ACF PRO
    if (!is_plugin_active('gravityforms/gravityforms.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php') && current_user_can('activate_plugins')) {
        wp_die('Sorry, but this plugin requires the ACF Pro and Gravity Form to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
    }
    require_once plugin_dir_path(__FILE__) . 'includes/class-soxes-chatbot-activator.php';
    Soxes_Chatbot_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-soxes-chatbot-deactivator.php
 */
function deactivate_soxes_chatbot()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-soxes-chatbot-deactivator.php';
    Soxes_Chatbot_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_soxes_chatbot');
register_deactivation_hook(__FILE__, 'deactivate_soxes_chatbot');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-soxes-chatbot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_soxes_chatbot()
{
    $plugin = Soxes_Chatbot::get_instance(SOXES_CHATBOT_NAME, SOXES_CHATBOT_VERSION);
    $plugin->run();
}

run_soxes_chatbot();
