<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://soxes.ch/
 * @since      1.0.0
 *
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/admin
 * @author     Truc Nguyen <truc.nguyen@soxes.ch>
 */
class Soxes_Chatbot_Admin
{
    /**
     * The instance
     *
     * @since    1.0.0
     * @access   private
     * @var      Soxes_Chatbot_Admin $instance Singleton class
     */
    private static $instance = null;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->add_menu_page();
        $this->add_acf_fields();
    }

    /**
     * Only if the class has no instance
     *
     * @since    1.0.0
     */
    public static function get_instance($plugin_name, $version)
    {
        if (null === self::$instance) {
            self::$instance = new Soxes_Chatbot_Admin($plugin_name, $version);
        }

        return self::$instance;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Soxes_Chatbot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Soxes_Chatbot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

//        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/soxes-chatbot-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Soxes_Chatbot_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Soxes_Chatbot_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

//        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/soxes-chatbot-admin.js', array('jquery'), $this->version, false);
    }

    /**
     * Register menu for the admin area.
     *
     * @since    1.0.0
     */
    private function add_menu_page()
    {
        if (function_exists('acf_add_options_page') && function_exists('acf_add_options_sub_page')) {
            // Chatbot setting
            $parent = acf_add_options_page(array(
                'page_title' => __('Chatbot Settings', 'theme'),
                'menu_title' => __('Chatbot', 'theme'),
                'icon_url' => 'dashicons-format-chat',
                'redirect' => false,
                'menu_slug' => 'soxes-chatbot-settings'
            ));
            // Chatbot content
            $child = acf_add_options_sub_page(array(
                'page_title' => __('Chatbot content', 'theme'),
                'menu_title' => __('Add chatbot', 'theme'),
                'parent_slug' => $parent['menu_slug'],
                'menu_slug' => 'soxes-chatbot'
            ));
//            // TODO: Export
//            $child1 = acf_add_options_sub_page(array(
//                'page_title' => __('Export', 'theme'),
//                'menu_title' => __('Export', 'theme'),
//                'parent_slug' => $parent['menu_slug'],
//                'menu_slug' => 'soxes-chatbot-export'
//            ));
        }
    }

    /**
     * Register ACF fields for the admin area.
     *
     * @since    1.0.0
     */
    private function add_acf_fields()
    {
        if (function_exists('acf_add_local_field_group')):

            //Chatbot settings
            acf_add_local_field_group(array(
                'key' => 'group_611b53dab1cfd',
                'title' => 'Chatbot settings',
                'fields' => array(
                    array(
                        'key' => 'field_611b53e95a832',
                        'label' => 'Chatbot type',
                        'name' => 'soxes_chatbot_type',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'Fixed bottom' => 'Fixed bottom',
                            'Shortcode' => 'Shortcode',
                        ),
                        'default_value' => 'Fixed bottom',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'wpml_cf_preferences' => 0,
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_61248ece85539',
                        'label' => 'Form',
                        'name' => 'soxes_chatbot_form',
                        'type' => 'forms',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'id',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'wpml_cf_preferences' => 0,
                    ),
                    array(
                        'key' => 'field_612c8ac608391',
                        'label' => 'Exclude pages',
                        'name' => 'soxes_exclude_pages',
                        'type' => 'page_link',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array(
                            0 => 'page',
                        ),
                        'taxonomy' => '',
                        'allow_null' => 1,
                        'allow_archives' => 0,
                        'multiple' => 1,
                        'wpml_cf_preferences' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'soxes-chatbot-settings',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ));
            //Chatbot Content
            acf_add_local_field_group(array(
                'key' => 'group_6112455dc5637',
                'title' => 'chatbot content',
                'fields' => array(
                    array(
                        'key' => 'field_611245633a158',
                        'label' => 'Question & Answer',
                        'name' => 'soxes_question_answer',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'wpml_cf_preferences' => 0,
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => 'Add question',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_6112459f08b7e',
                                'label' => 'Question',
                                'name' => 'soxes_question',
                                'type' => 'textarea',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '20',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'wpml_cf_preferences' => 0,
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => 3,
                                'new_lines' => '',
                            ),
                            array(
                                'key' => 'field_611245ae08b7f',
                                'label' => 'Answers',
                                'name' => 'soxes_answers',
                                'type' => 'repeater',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '70',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'wpml_cf_preferences' => 0,
                                'collapsed' => '',
                                'min' => 0,
                                'max' => 0,
                                'layout' => 'table',
                                'button_label' => 'Add answer',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_6112461708b82',
                                        'label' => 'ID',
                                        'name' => 'id',
                                        'type' => 'text',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '10',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'wpml_cf_preferences' => 0,
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'prepend' => '',
                                        'append' => '',
                                        'maxlength' => '',
                                    ),
                                    array(
                                        'key' => 'field_61134908b43ac',
                                        'label' => 'Select type',
                                        'name' => 'soxes_select_type',
                                        'type' => 'select',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '20',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'wpml_cf_preferences' => 0,
                                        'choices' => array(
                                            'Text' => 'Text',
                                            'Link' => 'Link',
                                            'Form' => 'Form',
                                            'Posts' => 'Posts',
                                            'Category' => 'Category',
                                            'Direct contact' => 'Direct contact',
                                        ),
                                        'default_value' => 'Text',
                                        'allow_null' => 0,
                                        'multiple' => 0,
                                        'ui' => 0,
                                        'return_format' => 'value',
                                        'ajax' => 0,
                                        'placeholder' => '',
                                    ),
                                    array(
                                        'key' => 'field_611245d908b81',
                                        'label' => 'Answer',
                                        'name' => 'soxes_answer',
                                        'type' => 'textarea',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Text',
                                                ),
                                            ),
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Link',
                                                ),
                                            ),
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Form',
                                                ),
                                            ),
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Direct contact',
                                                ),
                                            ),
                                        ),
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'wpml_cf_preferences' => 2,
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'maxlength' => '',
                                        'rows' => 3,
                                        'new_lines' => 'br',
                                    ),
                                    array(
                                        'key' => 'field_61134942b43ad',
                                        'label' => 'Link',
                                        'name' => 'soxes_answer_link',
                                        'type' => 'url',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Link',
                                                ),
                                            ),
                                        ),
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'wpml_cf_preferences' => 0,
                                    ),
                                    array(
                                        'key' => 'field_612ddad30fd2c',
                                        'label' => 'Posts',
                                        'name' => 'soxes_answer_posts',
                                        'type' => 'post_object',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Posts',
                                                ),
                                            ),
                                        ),
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'post_type' => array(
                                            0 => 'post',
                                        ),
                                        'taxonomy' => '',
                                        'allow_null' => 1,
                                        'multiple' => 1,
                                        'return_format' => 'id',
                                        'wpml_cf_preferences' => 0,
                                        'ui' => 1,
                                    ),
                                    array(
                                        'key' => 'field_6115e9c3416ba',
                                        'label' => 'Category',
                                        'name' => 'soxes_category',
                                        'type' => 'taxonomy',
                                        'instructions' => '',
                                        'required' => 0,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Category',
                                                ),
                                            ),
                                        ),
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'wpml_cf_preferences' => 0,
                                        'taxonomy' => 'category',
                                        'field_type' => 'multi_select',
                                        'allow_null' => 1,
                                        'add_term' => 0,
                                        'save_terms' => 0,
                                        'load_terms' => 0,
                                        'return_format' => 'object',
                                        'multiple' => 0,
                                    ),
                                    array(
                                        'key' => 'field_61163b29e0513',
                                        'label' => 'Direct contact',
                                        'name' => 'soxes_direct_contact',
                                        'type' => 'user',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => array(
                                            array(
                                                array(
                                                    'field' => 'field_61134908b43ac',
                                                    'operator' => '==',
                                                    'value' => 'Direct contact',
                                                ),
                                            ),
                                        ),
                                        'wrapper' => array(
                                            'width' => '20',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'role' => '',
                                        'allow_null' => 0,
                                        'multiple' => 0,
                                        'return_format' => 'array',
                                        'wpml_cf_preferences' => 0,
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_611245bf08b80',
                                'label' => 'Parent answer ID',
                                'name' => 'soxes_parent_answer_id',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '10',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'wpml_cf_preferences' => 0,
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'soxes-chatbot',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ));

        endif;
    }
}
