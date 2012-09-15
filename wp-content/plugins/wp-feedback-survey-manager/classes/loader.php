<?php
/**
 * loader
 * The library of loader class
 * @author Swashata <swashata4u@gmail.com>
 * @package WP Feedback & Surver Manager
 * @subpackage Loader
 * @version 1.0.0
 */

class wp_feedback_loader {
    /**
     * @deprecated
     * @var array stores the options
     */
    var $op;

    /**
     * The init classes used to generate the admin menu
     * The class should initialize and hook itself
     * @see /classes/admin-class.php and extend from the base abstract class
     * @staticvar array
     */
    static $init_classes = array();

    /**
     * @staticvar string
     * Holds the absolute path of the main plugin file directory
     */
    static $abs_path;

    /**
     * @staticvar string
     * Holds the absolute path of the main plugin file
     */
    static $abs_file;

    /**
     * Holds the text domain
     * Use the string directly instead
     * But still set this for some methods, especially the loading of the textdomain
     */
    static $text_domain;

    /**
     * @staticvar string
     * The current version of the plugin
     */
    static $version;

    /**
     * @staticvar string
     * The abbreviated name of the plugin
     * Mainly used for the enqueue style and script of the default admin.css and admin.js file
     */
    static $abbr;

    /**
     * The Documentation Link - From InTechgrity
     * @var string
     */
    static $documentation;

    /**
     * The support forum link - From WordPress Extends
     * @var string
     */
    static $support_forum;


    /**
     * Constructor function
     * @global array $wp_admrs_info The information option variable
     * @param type $file_loc
     * @param type $classes
     * @param type $text_domain
     * @param type $version
     * @param type $abbr
     */
    public function __construct($file_loc, $text_domain = 'default', $version = '1.0.0', $abbr = '', $doc = '', $sup = '') {
        self::$abs_path = dirname($file_loc);
        self::$abs_file = $file_loc;
        self::$text_domain = $text_domain;
        self::$version = $version;
        self::$abbr = $abbr;
        self::$init_classes = array('wp_feedback_dashboard', 'wp_feedback_report_survey', 'wp_feedback_view', 'wp_feedback_view_all', 'wp_feedback_settings');
        self::$documentation = $doc;
        self::$support_forum = $sup;
        global $wp_feedback_info;
        $wp_feedback_info = get_option('wp_feedback_info');
    }

    public function load() {
        //activation hook
        register_activation_hook(self::$abs_file, array(&$this, 'plugin_install'));
        /** Load Text Domain For Translations */
        add_action('plugins_loaded', array(&$this, 'plugin_textdomain'));

        //admin area
        if(is_admin()) {
            //admin menu items
            $this->init_admin_menus();
            add_action('admin_init', array(&$this, 'gen_admin_menu'), 20);
            add_action('wp_ajax_view_feedback', array(&$this, 'view_feedback'));

            //wp_ajax
            //add_action('wp_ajax_itgdb_iwi_view', array(&$this, 'admin_form_ajax'));
        } else {
            //add frontend script + style
            add_action('wp_print_styles', array(&$this, 'enqueue_script_style'));

            //add a shortcode
            add_shortcode('feedback', array(&$this, 'shortcode_main'));
            add_shortcode('feedback_trend', array(&$this, 'shortcode_trend'));

        }
        add_action('wp_ajax_nopriv_wp_feedback_submit', array(&$this, 'shortcode_ajax'));
        add_action('wp_ajax_wp_feedback_submit', array(&$this, 'shortcode_ajax'));

        //other filters + actions
        //add_action($tag, $function_to_add);
        //add_filter($tag, $function_to_add);
    }

    public function init_admin_menus() {
        foreach((array) self::$init_classes as $class) {
            if(class_exists($class)) {
                global ${'admin_menu' . $class};
                ${'admin_menu' . $class} = new $class();
            }
        }
    }


    public function gen_admin_menu() {
        $admin_menus = array();
        foreach((array) self::$init_classes as $class) {
            if(class_exists($class)) {
                global ${'admin_menu' . $class};
                $admin_menus[] = ${'admin_menu' . $class}->get_pagehook();
            }
        }

        foreach($admin_menus as $menu) {
            add_action('admin_print_styles-' . $menu, array(&$this, 'admin_enqueue_script_style'));
        }

    }

    public function admin_enqueue_script_style() {
        wp_enqueue_script('thickbox');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('ColorPicker', plugins_url('/static/admin/js/colorpicker.js', self::$abs_file), array('jquery'), self::$version);
        wp_enqueue_script(self::$abbr, plugins_url('/static/admin/js/admin.js', self::$abs_file), array('jquery'), self::$version);

        wp_enqueue_style(self::$abbr, plugins_url('/static/admin/css/admin.css', self::$abs_file), array(), self::$version);
        wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
        wp_enqueue_style('ColorPicker', plugins_url('/static/admin/css/colorpicker.css', self::$abs_file), array(), self::$version);
        wp_enqueue_style('jQuery-UI-style', plugins_url('/static/admin/css/jquery-ui-1.7.3.custom.css', self::$abs_file), array(), self::$version);

        //wp_enqueue_style('itgdb_iwi_style_base', plugins_url('/static/style.css', self::$abs_path), array(), '1.1.0');
        //wp_enqueue_script('itgdb_iwi_printarea', plugins_url('/static/jquery.printElement.min.js', self::$abs_path), array('jquery'), '1.0.0');
    }

    public function enqueue_script_style() {
        /* Everything is handled by the shortcode or the form class */
        //add style
        //wp_enqueue_style('wp_feedback_css', plugins_url('/static/front/css/form.css', self::$abs_file), array(), self::$version);
        //wp_enqueue_style('wp_feedback_jqui', plugins_url('/static/front/css/smoothness/jquery-ui-1.8.22.custom.css', self::$abs_file), array(), self::$version);
        //wp_enqueue_style('wp_feedback_jqvl', plugins_url('/static/front/css/validationEngine.jquery.css', self::$abs_file), array(), self::$version);

        //add script
//        wp_enqueue_script('wp_feedback_ve', plugins_url('/static/front/js/jquery.validationEngine-en.js', self::$abs_file), array('jquery'), self::$version);
//        wp_enqueue_script('wp_feedback_v', plugins_url('/static/front/js/jquery.validationEngine.js', self::$abs_file), array('jquery'), self::$version);
//        wp_enqueue_script('wp_feedback_jquis', plugins_url('/static/front/js/jquery-ui-1.8.22.custom.min.js', self::$abs_file), array('jquery'), self::$version);
//        wp_enqueue_script('wp_feedback_form', plugins_url('/static/front/js/form.js', self::$abs_file), array('jquery'), self::$version);
//        wp_localize_script('wp_feedback_form', 'wpFBObj', array(
//            'ajaxurl' => admin_url('admin-ajax.php'),
//        ));
    }

    public function plugin_install() {
        include_once self::$abs_path . '/classes/install-class.php';

        $install = new wp_feedback_install();
        $install->install();
    }

    /**
     * Load the text domain on plugin load
     * Hooked to the plugins_loaded via the load method
     */
    public function plugin_textdomain() {
        load_plugin_textdomain('fbsr', false, dirname(plugin_basename(self::$abs_file)) . '/translations/');
    }

    /* Shortcode callbacks */
    public function shortcode_main($args, $content = null) {
        $shortcode = new wp_feedback_form_shortcode();
        $shortcode->feedback_cb();
    }
    public function shortcode_trend($args, $content = null) {
        $s = new wp_feedback_trend();
        $s->print_trend();
    }

    /* AJAX callbacks */
    public function shortcode_ajax() {
        //echo 'Test';
        $form = new wp_feedback_form();
        $result = $form->save_post();
        echo json_encode($result);
        die();
    }

    public function view_feedback() {
        $fb = new wp_feedback_view_cb();
        $fb->show($_GET['id']);
        die();
    }


}
