<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://soxes.ch/
 * @since      1.0.0
 *
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Soxes_Chatbot
 * @subpackage Soxes_Chatbot/public
 * @author     Truc Nguyen <truc.nguyen@soxes.ch>
 */
class Soxes_Chatbot_Public
{

    /**
     * The instance
     *
     * @since    1.0.0
     * @access   private
     * @var      Soxes_Chatbot_Public $instance Singleton class
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
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $type The chatbot type
     */
    public $type;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      int $form The chatbot default form
     */
    public $form;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      array $exclude The chatbot exclude pages
     */
    public $exclude = array();

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->exclude = apply_filters('soxes_chatbot_excludes', get_field('soxes_exclude_pages', 'option'));
        $this->type = apply_filters('soxes_chatbot_type', get_field('soxes_chatbot_type', 'option'));
        $this->form = apply_filters('soxes_chatbot_form', get_field('soxes_chatbot_form', 'option'));
    }

    /**
     * Only if the class has no instance
     *
     * @since    1.0.0
     */
    public static function get_instance($plugin_name, $version)
    {
        if (null === self::$instance) {
            self::$instance = new Soxes_Chatbot_Public($plugin_name, $version);
        }

        return self::$instance;
    }

    /**
     * Get exclude pages
     *
     * @since    1.0.0
     */
    public function get_exclude()
    {
        return $this->exclude;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        if (!empty($this->exclude) && is_array($this->exclude) && in_array(get_the_ID(), $this->exclude)) {
            return;
        }
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/soxes-chatbot-public.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        if (!empty($this->exclude) && is_array($this->exclude) && in_array(get_the_ID(), $this->exclude)) {
            return;
        }
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/soxes-chatbot-public.min.js', array('jquery'), $this->version, false);
        $address = get_field('soxes_address_contact', 'option');
        $data = array(
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'direct_txt' => apply_filters('soxes_chatbot_direct_text', __('Your direct contact person', 'soxes-chatbot')),
            'address_txt' => $address ? $address : '',
            'hello_txt' => apply_filters('soxes_chatbot_hello_text', __('Hello', 'soxes-chatbot')),
            'icon_chatbot' => plugin_dir_url(__FILE__) . 'img/bot-icon.png',
            'chatbot_type' => $this->type,
            'default_form' => $this->form,
            'close_icon' => $this->render_close_icon(),
            'tooltip_text' => __('Go to', 'soxes-chatbot')
        );
        wp_localize_script($this->plugin_name, 'soxes_chatbot', $data);
    }

    /**
     * Render chatbot to frontend
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    public function render_chatbot()
    {
        $json_data = [];
        if (in_array(get_the_ID(), $this->exclude)) {
            return;
        }
        $field_data = $this->get_field_data();
        $questions = (!empty($field_data['questions'])) ? $field_data['questions'] : [];
        $start_q = (!empty($field_data['start_q'])) ? $field_data['start_q'] : [];
        if (empty($questions)) {
            return;
        }
        $data = $this->prepare_data($questions);
        if (!empty($data['json_data'])) {
            $json_data = $data['json_data'];
        }
        if (!empty($this->form)) : ?>
            <div class="modal-wrapper hide modal-form-chatbot" id="<?php echo 'modal-form-' . $this->form; ?>">
                <div class="modal-outer">
                    <div class="modal-inner">
                        <div class="modal-header">
                            <div class="modal-actions">
                                <span class="sprite sprite--close">
                                    <?php echo $this->render_close_icon(); ?>
                                </span>
                            </div>
                        </div>
                        <div class="modal-body">
                            <h3 class="form-title"><?php echo apply_filters('soxes_chatbot_form_title', __('Your request', 'soxes-chatbot'), $this->form); ?></h3>
                            <div id="soxes-form-wrapper_<?php echo $this->form; ?>" class="soxes-form-wrapper">
                            </div>
                        </div>
                        <div class="loading-container-chatbot">
                            <div class="overlay-loading"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php do_action('soxes_chatbot_before_widget'); ?>
        <div class="soxes_overlay_container chatbot_bottom" style="display: none;">
            <script type="data/json" class="default_questions"><?php echo json_encode($json_data); ?></script>
            <div class="soxes_overlay_container_outer">
                <div class="soxes_overlay_container_inner">
                    <div class="bot_heading">
                        <p><?php echo apply_filters('soxes_chatbot_chatbot_text', __('Chatbot', 'soxes-chatbot')); ?></p>
                        <div class="box-actions">
                            <span class="sprite sprite--nav-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                                    <path d="M15 7a1 1 0 010 2H1a1 1 0 110-2h14z"></path>
                                </svg>
                            </span>
                            <span class="sprite sprite--close soxes_chatbot_close">
                                <?php echo $this->render_close_icon(); ?>
                                <span class="close-text"
                                      style="display: none;"><?php echo apply_filters('soxes_chatbot_close_text', __('Close assistant', 'soxes-chatbot')); ?></span>
                            </span>
                        </div>
                    </div>
                    <?php echo $this->render_chatbot_inner($start_q); ?>
                </div>
            </div>
        </div>
        <?php do_action('soxes_chatbot_after_widget'); ?>
        <div class="bot_icon_only">
            <span class="chatbot-txt chatbot-txt-1 hidden"><?php _e('Hallo!', 'soxes-chatbot'); ?></span>
            <span class="chatbot-txt chatbot-txt-2 hidden"><?php _e('Fragen?', 'soxes-chatbot'); ?></span>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>img/bot-icon.png" alt="chatbot icon"/>
        </div>
        <?php
    }

    /**
     * Render svg icons to frontend
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    public function svg_icons()
    {
        if (in_array(get_the_ID(), $this->exclude)) {
            return;
        }
        echo '<div style="height: 0px; width: 0px; display: none;"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient x1="100%" y1="37.049%" x2="0%" y2="63.304%" id="a"><stop stop-color="#147BD1" offset="0%"></stop><stop stop-color="#00A3AD" offset="49.548%"></stop><stop stop-color="#8A75D1" offset="100%"></stop></linearGradient><style>.cls-1{fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:24px}</style></defs><symbol id="svgicon-close" viewBox="0 0 16 16"><path d="M2.61 1.21l.1.08L8 6.6l5.3-5.3a1 1 0 011.49 1.32l-.08.1L9.4 8l5.3 5.3a1 1 0 01-1.32 1.49l-.1-.08L8 9.4l-5.3 5.3a1 1 0 01-1.49-1.32l.08-.1L6.6 8 1.3 2.7a1 1 0 011.32-1.49z"></path></symbol><symbol id="svgicon-arrow-right" viewBox="0 0 16 16"><path d="M9.61 1.21l.1.08 6 6a1 1 0 01.08 1.32l-.08.1-6 6a1 1 0 01-1.5-1.32l.08-.1L12.6 9H1a1 1 0 01-.12-2H12.6L8.3 2.7a1 1 0 01-.08-1.31l.08-.1a1 1 0 011.32-.08z"></path></symbol></svg></div>';
    }

    /**
     * Prepare data default json format
     *
     * @since    1.0.0
     */
    /**
     * @return array
     */
    private function prepare_data($questions)
    {
        $json_data = [];
        $default_form = $this->form;
        //format data push to json value
        foreach ($questions as $question) {
            $data = [];
            $data['soxes_question'] = $question['soxes_question'];
            $data['soxes_parent_answer_id'] = $question['soxes_parent_answer_id'];
            foreach ($question['soxes_answers'] as $key => $answer) {
                $data['soxes_answers'][$key]['id'] = $answer['id'];
                $data['soxes_answers'][$key]['soxes_select_type'] = $answer['soxes_select_type'];
                $data['soxes_answers'][$key]['soxes_answer'] = $answer['soxes_answer'];
                $data['soxes_answers'][$key]['soxes_answer_link'] = $answer['soxes_answer_link'];
                if ($answer['soxes_select_type'] == 'Form') {
                    $data['soxes_answers'][$key]['soxes_answer_form'] = $default_form;
                } elseif ($answer['soxes_select_type'] == 'Category') {
                    $cats = [];
                    foreach ($answer['soxes_category'] as $category) {
                        $cat_json = new stdClass();
                        $cat_json->term_id = $category->term_id;
                        $cat_json->name = $category->name;
                        $cat_json->link = get_term_link($category);
                        array_push($cats, $cat_json);
                    }
                    $data['soxes_answers'][$key]['soxes_category'] = $cats;
                } elseif ($answer['soxes_select_type'] == 'Posts') {
                    $posts = [];
                    foreach ($answer['soxes_answer_posts'] as $post) {
                        $post_json = new stdClass();
                        $post_json->id = $post;
                        $post_json->name = get_the_title($post);
                        $post_json->link = get_the_permalink($post);
                        array_push($posts, $post_json);
                    }
                    $data['soxes_answers'][$key]['soxes_answer_posts'] = $posts;
                } elseif ($answer['soxes_select_type'] == 'Direct contact') {
                    $user_id = $answer['soxes_direct_contact']['ID'];
                    $avatar = get_field('author_avatar', 'user_' . $user_id);
                    $position = get_field('author_position', 'user_' . $user_id);
                    $linkedin = get_field('author_linkedin', 'user_' . $user_id);
                    $gender = get_field('author_gender', 'user_' . $user_id);
                    $gender = ($gender == 'Female') ? __('Your direct contact person&nbsp;', 'soxes-chatbot') : __('Your direct contact person', 'soxes-chatbot');;
                    if (!$avatar) {
                        $avatar = get_field('author_default_avatar', 'option');
                    }
                    $data['soxes_answers'][$key]['soxes_answer_form'] = $default_form;
                    $data['soxes_answers'][$key]['soxes_direct_contact']['ID'] = $user_id;
                    if (function_exists('get_post_author')) {
                        $author = get_post_author($user_id);
                        $data['soxes_answers'][$key]['soxes_direct_contact']['display_name'] = $author['full_name'];
                        $avatar = $author['avatar'];
                        $data['soxes_answers'][$key]['soxes_direct_contact']['position'] = $author['position'];
                        $data['soxes_answers'][$key]['soxes_direct_contact']['linkedin'] = $author['linkedin'];
                    } else {
                        $data['soxes_answers'][$key]['soxes_direct_contact']['display_name'] = $answer['soxes_direct_contact']['display_name'];
                        $data['soxes_answers'][$key]['soxes_direct_contact']['position'] = $position;
                        $data['soxes_answers'][$key]['soxes_direct_contact']['linkedin'] = $linkedin;
                    }
                    $data['soxes_answers'][$key]['soxes_direct_contact']['gender'] = $gender;

                    $email = $answer['soxes_direct_contact']['user_email'] ?? '';
                    if ($email) {
                        $email = explode('@', $email);
                        $email = $email[0];
                    }
                    if (!empty($email)) {
                        $data['soxes_answers'][$key]['soxes_direct_contact']['email'] = $email;
                    }
                    if (!empty($avatar)) {
                        $data['soxes_answers'][$key]['soxes_direct_contact']['avatar'] = $avatar;
                    }
                }
            }
            array_push($json_data, $data);
        }

        return apply_filters('soxes_chatbot_prepare_data', array(
            'json_data' => $json_data
        ), $questions);
    }

    /**
     * Return split data
     *
     * @since    1.1.0
     */
    /**
     * @return array
     */
    public function get_field_data()
    {
        $questions = get_field('soxes_question_answer', 'option');
        $start_q = '';
        if (!empty($questions) && is_array($questions)) {
            $indexed = (!empty($questions) && is_array($questions)) ? array_column($questions, 'soxes_parent_answer_id', '') : 0;
            $position = array_search('0', $indexed);
            if ($position !== false) {
                $start_q = $questions[$position];
            } else {
                $start_q = $questions[0];
            }
        }
        return array(
            'start_q' => $start_q,
            'questions' => $questions
        );
    }
    /**
     * Return shortcode content
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    public function chatbot_shortcode()
    {
        $field_data = $this->get_field_data();
        $questions = (!empty($field_data['questions'])) ? $field_data['questions'] : [];
        $start_q = (!empty($field_data['start_q'])) ? $field_data['start_q'] : [];
        ob_start();
        ?>
        <?php do_action('soxes_chatbot_before_shortcode'); ?>
        <div class="chatbot_widget">
            <div class="soxes_overlay_container">
                <div class="soxes_overlay_container_outer">
                    <div class="soxes_overlay_container_inner">
                        <div class="bot_heading">
                            <p><?php _e('Chatbot', 'soxes-chatbot'); ?></p>
                        </div>
                        <?php echo $this->render_chatbot_inner($start_q); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php do_action('soxes_chatbot_after_shortcode'); ?>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return apply_filters('soxes_chatbot_shortcode', $content, $start_q, $questions);
    }

    /**
     * Return chatbot inner
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    private function render_chatbot_inner($start_q)
    {
        ob_start();
        ?>
        <div class="bot_container bot_active">
            <div class="bot_outer">
                <div class="bot_inner">
                    <div class="bot_content">
                        <div class="bot_icon">
                            <img src="<?php echo plugin_dir_url(__FILE__); ?>img/bot-icon.png"
                                 alt="chatbot icon"/>
                        </div>
                        <div class="bot_item_wrapper">
                            <div class="bot_item bot_question bot_visible">
                                <?php echo (!empty($start_q['soxes_question'])) ? $start_q['soxes_question'] : ''; ?>
                            </div>
                            <div class="bot_item bot_answers bot_visible">
                                <?php if (is_array($start_q['soxes_answers']) && !empty($start_q['soxes_answers'])) :
                                    foreach ($start_q['soxes_answers'] as $key => $answer) :
                                        if (empty($answer['soxes_answer'])) {
                                            continue;
                                        }
                                        if ($answer['soxes_select_type'] == 'Form') :
                                            ?>
                                            <div class="wrap_answer active">
                                                <button class="bot__item bot__answer bot_open_form"
                                                        data-text="empty"
                                                        data-match="<?php echo 'match_id_' . $key; ?>"
                                                        data-value="<?php echo (!empty($answer['soxes_answer_form'])) ? $answer['soxes_answer_form'] : $this->form; ?>"
                                                        data-action="openForm"><?php echo (!empty($answer['soxes_answer'])) ? $answer['soxes_answer'] : ''; ?></button>
                                                <span class="sprite sprite--close">
                                                    <?php echo $this->render_close_icon(); ?>
                                                </span>
                                            </div>
                                        <?php elseif ($answer['soxes_select_type'] == 'Link') : ?>
                                            <div class="wrap_answer active">
                                                <a class="bot__item bot__answer bot_open_link"
                                                   data-match="<?php echo 'match_id_' . $key; ?>"
                                                   href="<?php echo $answer['soxes_answer_link']; ?>"><?php echo (!empty($answer['soxes_answer'])) ? $answer['soxes_answer'] : ''; ?></a>
                                                <span class="sprite sprite--close">
                                                    <?php echo $this->render_close_icon(); ?>
                                                </span>
                                            </div>
                                        <?php else : ?>
                                            <div class="wrap_answer active ">
                                                <button class="bot__item bot__answer"
                                                        data-match="<?php echo 'match_id_' . $key; ?>"
                                                        data-value="<?php echo (!empty($answer['id'])) ? $answer['id'] : 0; ?>"
                                                        data-action="openQuestion"><?php echo (!empty($answer['soxes_answer'])) ? $answer['soxes_answer'] : ''; ?></button>
                                                <span class="sprite sprite--close">
                                                    <?php echo $this->render_close_icon(); ?>
                                                </span>
                                            </div>
                                        <?php endif;
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return apply_filters('soxes_chatbot_inner', $content, $start_q);
    }

    /**
     * Return close icon
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    private function render_close_icon()
    {
        ob_start();
        ?>
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
             viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><g>
                <path d="M962.2,13.3l24.4,24.4L34.4,989.8L10,965.5L962.2,13.3z"></path>
                <path d="M39.2,10L990,960.9L960.9,990L10.1,39.1L39.2,10L39.2,10z"></path>
            </g></svg>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return apply_filters('soxes_chatbot_close_icon', $content);
    }

    /**
     * Custom Gravity form comfirmation
     *
     * @since    1.0.0
     */
    /**
     * @return string
     */
    public function custom_confirmation($confirmation, $form)
    {
        $default_form = $this->form;
        if ($form['id'] == $default_form) {
            $confirmation .= "<script>window.top.jQuery(document).on('gform_confirmation_loaded', function () {jQuery('.modal-form-chatbot').addClass('confirmation'); setTimeout(function(){ if(!jQuery('.modal-form-chatbot').hasClass('clicked')) {jQuery('.modal-form-chatbot').removeClass('confirmation'); jQuery('.modal-form-chatbot .sprite--close').click()} }, 7000) } );</script>";
        }
        return $confirmation;
    }

    /**
     * Custom Gravity form submission: send direct to user
     *
     * @since    1.0.0
     */
    /**
     * @return void
     */
    public function custom_gform_pre_submission($form)
    {
        $default_form = $this->form;
        if ($form['id'] == $default_form) {
            $field = apply_filters('soxes_chatbot_gf_email', $_POST['input_7']);
            if (isset($field) && $field != 'undefined') {
                $_POST['input_7'] = $field . '@soxes.ch';
            }
            if (!empty($_POST['input_15'])) {
                $_POST['input_8'] = $_POST['input_15'];
            }
        }
    }

    /**
     * Reload form after user submit successful AJAX
     *
     * @since    1.0.0
     */
    /**
     * @return void
     */
    public function rerender_form()
    {
        $form_id = isset($_GET['form_id']) ? absint($_GET['form_id']) : 0;
        if ($form_id != 0) {
            echo do_shortcode('[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]') . "<script type='text/javascript'>jQuery(document).ready(function() {                
            jQuery('#gform_{$form_id} .gform_footer button').on('click', function() {
                jQuery(this).addClass('disabled');
                var el = jQuery(this);
                el.addClass('disabled');
                
                var recaptchaWrapper = jQuery('iframe[src*=\"google.com/recaptcha/api2/bframe\"]').parent().parent();		    
                recaptchaWrapper.on('click', function(e){
                    el.removeClass('disabled');
                    el.parent().find('.gform_ajax_spinner').remove();
                });
            });
            window.top.jQuery(document).on('gform_confirmation_loaded', function () {
                jQuery('.modal-form-chatbot').addClass('confirmation');
                setTimeout(function(){ 
                    if(!jQuery('.modal-form-chatbot').hasClass('clicked')) {
                        jQuery('.modal-form-chatbot').removeClass('confirmation'); 
                        jQuery('.modal-form-chatbot .sprite--close').click()} 
                    }, 7000) 
            });
        });</script>";
        }
        exit();
    }

    public function enqueue_scripts_gf()
    {
        $default_form = $this->form;
        if ($default_form != 0) {
            gravity_form_enqueue_scripts($default_form, true);
        }
    }
}
