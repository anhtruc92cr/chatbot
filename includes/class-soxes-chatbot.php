<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://soxes.ch/
 * @since      1.0.0
 *
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/includes
 * @author     Truc Nguyen <truc.nguyen@soxes.ch>
 */
class Soxes_Chatbot
{
    /**
     * The instance
     *
     * @since    1.0.0
     * @access   private
     * @var      Soxes_Chatbot $instance Singleton class
     */
    private static $instance = null;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Soxes_Chatbot_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        if ($version) {
            $this->version = $version;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = $plugin_name;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Only if the class has no instance
     *
     * @since    1.0.0
     */
    public static function get_instance($plugin_name, $version)
    {
        if (null === self::$instance) {
            self::$instance = new Soxes_Chatbot($plugin_name, $version);
        }

        return self::$instance;
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Soxes_Chatbot_Loader. Orchestrates the hooks of the plugin.
     * - Soxes_Chatbot_i18n. Defines internationalization functionality.
     * - Soxes_Chatbot_Admin. Defines all hooks for the admin area.
     * - Soxes_Chatbot_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-soxes-chatbot-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-soxes-chatbot-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-soxes-chatbot-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-soxes-chatbot-public.php';

        $this->loader = Soxes_Chatbot_Loader::get_instance();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Soxes_Chatbot_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Soxes_Chatbot_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = Soxes_Chatbot_Admin::get_instance($this->get_plugin_name(), $this->get_version());
        //Turn the below on when you want to use js and css for admin area
//        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
//        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = Soxes_Chatbot_Public::get_instance($this->get_plugin_name(), $this->get_version());
        add_shortcode('chatbot_widget', array($plugin_public, 'chatbot_shortcode'));

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('gform_pre_submission', $plugin_public, 'custom_gform_pre_submission');
        if (!is_admin()) {
            $this->loader->add_action('wp_footer', $plugin_public, 'render_chatbot');
            $this->loader->add_action('wp_footer', $plugin_public, 'svg_icons');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts_gf');
            $this->loader->add_filter('gform_confirmation', $plugin_public, 'custom_confirmation', 10, 2);
        }
        $this->loader->add_action('wp_ajax_nopriv_rerender_form', $plugin_public, 'rerender_form');
        $this->loader->add_action('wp_ajax_rerender_form', $plugin_public, 'rerender_form');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Soxes_Chatbot_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }
}
