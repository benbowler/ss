<?php
/**
 * admin-classes
 * The library of all the administration classes
 * @author Swashata <swashata4u@gmail.com>
 * @package WP Feedback & Surver Manager
 * @subpackage Admin Backend classes
 * @version 1.0.0
 */


class wp_feedback_settings extends wp_feedback_base {
    public function __construct() {
        $this->capability = 'manage_feedback';
        $this->action_nonce = 'wp_feedback_settings_nonce';

        parent::__construct();

        $this->icon_url = $this->url['images'] . 'settings_32.png';
        $this->is_metabox = true;
        $this->metabox_col = 1;
    }

    /*________________SYSTEM METHODS________________*/
    public function admin_menu() {
        $this->pagehook = add_submenu_page('wp_feedback_dashboard', __('WP Feedback & Survey Manager Settings', 'fbsr'), __('Settings', 'fbsr'), $this->capability, 'wp_feedback_settings', array(&$this, 'index'));
        parent::admin_menu();
    }
    public function index() {
        $this->index_head(__('WP FeedBack & Survey Manager &raquo; General Settings', 'fbsr'));
        ?>
<style type="text/css">
    #wp_feedback_survey_op h3.hndle, #wp_feedback_feedback_op h3.hndle {
        border: medium none;
    }
</style>
<div class="metabox-holder">
    <?php $this->print_metabox_containers('normal'); ?>
    <?php $this->print_metabox_containers('side'); ?>
</div>
        <?php
        $this->index_foot();
    }

    public function on_load_page() {
        parent::on_load_page();

        add_meta_box('wp_feedback_global_op', __('FeedBack Manager &raquo; Global Options', 'fbsr'), array(&$this, 'meta_global'), $this->pagehook, 'normal');
        add_meta_box('wp_feedback_survey_op', __('FeedBack Manager &raquo; Survey Options', 'fbsr'), array(&$this, 'meta_survey'), $this->pagehook, 'normal');
        add_meta_box('wp_feedback_feedback_op', __('FeedBack Manager &raquo; Feedback Options', 'fbsr'), array(&$this, 'meta_feedback'), $this->pagehook, 'normal');

        get_current_screen()->add_help_tab(array(
            'id' => 'overview',
            'title' => __('Overview', 'fbsr'),
            'content' =>
                '<p>' . __('This page provides an easy way to customize all aspects of the Feedback/Survey Form. But we first you need to understand the three main aspects of the form.', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('<strong>Survey Questions:</strong> This, if enabled, shows the multiple choice type (with only one or more than one answers) questions on the first tab. All of them are made mandatory to the surveyee.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Feedback Topics:</strong> This if enabled, will show feedback topic and a textarea to leave feedback on that particular topic. None of these are mandatory for the surveyee.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Personal Information:</strong> This, if enabled, will show the surveyee to enter his/her first name, last name, email and phone number. All of these are made mandatory except the opinion which you can choose to turn off.', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('You can get more help by clicking the [?] icon beside every options. Or you can check the corresponding tabs inside this help screen...', 'fbsr') . '</p>' .
                '<p><strong>' . __('Please note that attempting to save this page will clear any transient cache (The Trends Transient Cache)', 'fbsr') . '</strong></p>'
        ));

        get_current_screen()->add_help_tab(array(
            'id' => 'global-options',
            'title' => __('Global Options', 'fbsr'),
            'content' =>
                '<p>' . __('Change the overall behavious of the Feedback Form. Here are some of the most important facts.', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('<strong>Enabling a Tab:</strong> There are 3 main tabs of the form. <em>Survey, Feedback & Personal Information</em>. You can choose to enable/disable each of them individually.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Title & Subtitle:</strong> The name of each of the tabs can be customized using these options. For say, if you wish to make a quiz on your WordPress site, then you would probably name the Survey Tab as <em>"Questions", Answer all what you can</em> or something similar.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Terms Page:</strong> You should provide a Terms & Conditions page. This will impose a Checkbox under the Personal Information Tab and will make it mandatory for the surveyee to tick it. Also a link to the page will be put beside it.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Notification:</strong> Enter an email address if you like to get notified whenever someone submits your form. Please add wordpress@yourdomain.tld to the anti-spam filter else it might go into spam.', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('You can get more help by clicking the [?] icon beside every options.', 'fbsr') . '</p>'
        ));

        get_current_screen()->add_help_tab(array(
            'id' => 'survey-options',
            'title' => __('Survey Options', 'fbsr'),
            'content' =>
                '<p>' . __('Create upto 10 Survey Questions from this options screen. In order to show it, you need to have the "Enable Survey" ticked on the global options. Here are some of the most important facts.', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('<strong>Enabled:</strong> This has to be ticked if you want the particular survey to show up.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Question:</strong> Put the Survey Question here.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Answer Options:</strong> These are the answer options that will show up below the question. You can have any number of options. Just put new options in a new line.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Question Type:</strong> If single, then the surveyee will be able to select only one from the available options (using radio box), otherwise he/she will be able to select multiple options (using checkbox).', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('You can get more help by clicking the [?] icon beside every options.', 'fbsr') . '</p>'
        ));

        get_current_screen()->add_help_tab(array(
            'id' => 'feedback-options',
            'title' => __('Feedback Options', 'fbsr'),
            'content' =>
                '<p>' . __('Create upto 10 Feedback Topic from this options screen. In order to show it, you need to have the "Enable Feedback" ticked on the global options. Here are some of the most important facts.', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('<strong>Enabled:</strong> This has to be ticked if you want the particular feedback to show up.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Topic:</strong> This is the heading of the feedback which is put beside the checkbox. If user clicks on the checkbox then corresponding <strong>Description</strong> and a textbox are shown.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Feedback Email:</strong> This is the most useful thing. If you want a particulat feedback to go into a particular email, then you can enter it here. Even you can have multiple email addresses seperated by comma(,).', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('You can get more help by clicking the [?] icon beside every options.', 'fbsr') . '</p>'
        ));

        get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'fbsr'), wp_feedback_loader::$documentation) . '</p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Support Forums</a>', 'fbsr'), wp_feedback_loader::$support_forum) . '</p>'
	);
    }

    public function save_post() {
        parent::save_post();

        $save_flag = array();
        delete_transient('wp_feedback_data_t');

        $wp_feedback_global = array(
            'enable_survey' => true,
            'enable_feedback' => true,
            'enable_pinfo' => true,
            'enable_opinion' => true,
            'terms_page' => '',
            'email' => get_option('admin_email'),
        );

        $wp_feedback_global = wp_parse_args($this->post['global'], $wp_feedback_global);
        if(isset($this->post['global']['enable_survey'])) {
            $wp_feedback_global['enable_survey'] = true;
        } else {
            $wp_feedback_global['enable_survey'] = false;
        }
        if(isset($this->post['global']['enable_feedback'])) {
            $wp_feedback_global['enable_feedback'] = true;
        } else {
            $wp_feedback_global['enable_feedback'] = false;
        }
        if(isset($this->post['global']['enable_pinfo'])) {
            $wp_feedback_global['enable_pinfo'] = true;
        } else {
            $wp_feedback_global['enable_pinfo'] = false;
        }
        if(isset($this->post['global']['enable_opinion'])) {
            $wp_feedback_global['enable_opinion'] = true;
        } else {
            $wp_feedback_global['enable_opinion'] = false;
        }

        $wp_feedback_survey = array();
        $wp_feedback_feedback = array();

        for($i = 0; $i < 20; $i++) {
            $wp_feedback_survey[$i] = array(
                'enabled' => isset($this->post['survey'][$i]['enabled']) ? true : false,
                'question' => $this->post['survey'][$i]['question'],
                'options' => trim(preg_replace("/\r\n\s*[\r\n]*/", "\r\n", $this->post['survey'][$i]['options']), "\r\n"),
                'type' => $this->post['survey'][$i]['type'],
            );
            $wp_feedback_feedback[$i] = array(
                'enabled' => isset($this->post['feedback'][$i]['enabled']) ? true : false,
                'name' => $this->post['feedback'][$i]['name'],
                'description' => $this->post['feedback'][$i]['description'],
                'email' => $this->post['feedback'][$i]['email'],
            );
        }

        $updates = array(
            'wp_feedback_global', 'wp_feedback_survey', 'wp_feedback_feedback',
        );

        foreach($updates as $u) {
            if(update_option($u, ${$u})) {
                $save_flag[] = true;
            } else {
                $save_flag[] = false;
            }
        }

        if(in_array(true, $save_flag)) {
            wp_redirect(add_query_arg(array('post_result' => '1'), $_POST['_wp_http_referer']));
        } else {
            wp_redirect(add_query_arg(array('post_result' => '2'), $_POST['_wp_http_referer']));
        }
        die();
    }



    /*________________METABOX METHODS________________*/
    public function meta_global() {
        $op = get_option('wp_feedback_global');
        ?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="col">
                <label for="global_enable_survey"><?php _e('Enable Survey', 'fbsr'); ?></label>
            </th>
            <td>
                <label for="global_enable_survey"><?php $this->print_checkbox('global[enable_survey]', '1', $op['enable_survey'] == true); ?>
                <?php _e('Enabled?', 'fbsr'); ?></label>
            </td>
            <td>
                <span class="help">
                    <?php _e('Check this if you want to show the survey form.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th scope="col">
                <label for="global_enable_feedback"><?php _e('Enable Feedback', 'fbsr'); ?></label>
            </th>
            <td>
                <label for="global_enable_feedback"><?php $this->print_checkbox('global[enable_feedback]', '1', $op['enable_feedback'] == true); ?>
                <?php _e('Enabled?', 'fbsr'); ?></label>
            </td>
            <td>
                <span class="help">
                    <?php _e('Check this if you want to show the feedback form.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th scope="col">
                <label for="global_enable_pinfo"><?php _e('Enable Personal Information', 'fbsr'); ?></label>
            </th>
            <td>
                <label for="global_enable_pinfo"><?php $this->print_checkbox('global[enable_pinfo]', '1', $op['enable_pinfo'] == true); ?>
                <?php _e('Enabled?', 'fbsr'); ?></label>
            </td>
            <td>
                <span class="help">
                    <?php _e('Check this if you want to log the personal information.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th scope="col">
                <label for="global_enable_opinion"><?php _e('Enable Opinion', 'fbsr'); ?></label>
            </th>
            <td>
                <label for="global_enable_opinion"><?php $this->print_checkbox('global[enable_opinion]', '1', $op['enable_opinion'] == true); ?>
                <?php _e('Enabled?', 'fbsr'); ?></label>
            </td>
            <td>
                <span class="help">
                    <?php _e('Check this if you want to show the Opinion text box. This does not have any effect if the Personal Information is unchecked.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <?php $titles = array('survey' => __('Survey', 'fbsr'), 'feedback' => __('Feedback', 'fbsr'), 'pinfo' => __('Personal Info', 'fbsr')); ?>
        <?php foreach($titles as $t => $v) : ?>
        <tr>
            <th scope="col">
                <label for="global_<?php echo $t; ?>_title"><?php echo $v . ' ' . __('Title', 'fbsr'); ?></label>
            </th>
            <td>
                <?php $this->print_input_text('global[' . $t . '_title]', $op[$t . '_title'], 'regular-text'); ?>
            </td>
            <td>
                <span class="help">
                    <?php _e('This will be shown as the title of the corresponding form tab.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th scope="col">
                <label for="global_<?php echo $t; ?>_subtitle"><?php echo $v . ' ' . __('Sub Title', 'fbsr'); ?></label>
            </th>
            <td>
                <?php $this->print_input_text('global[' . $t . '_subtitle]', $op[$t . '_subtitle'], 'large-text'); ?>
            </td>
            <td>
                <span class="help">
                    <?php _e('This will be shown as the sub title of the corresponding form tab.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>

        <tr>
            <th scope="col">
                <label for="global_success_message"><?php _e('Success Message', 'fbsr'); ?></label>
            </th>
            <td>
                <?php $this->print_textarea('global[success_message]', $op['success_message'], 'large-text'); ?>
            </td>
            <td>
                <span class="help">
                    <?php _e('This message will be shown to the users when they submit the form.', 'fbsr'); ?>
                </span>
            </td>
        </tr>

        <tr>
            <th scope="col">
                <label for="global_terms_page"><?php _e('Terms Page', 'fbsr'); ?></label>
            </th>
            <td>
                <?php $this->print_input_text('global[terms_page]', $op['terms_page'], 'regular-text code'); ?>
            </td>
            <td>
                <span class="help">
                    <?php _e('If any page ID is given here, then user will be presented with a checkbox which he/she has to check before submitting. This will lead to the specified page on click.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
        <tr>
            <th scope="col">
                <label for="global_email"><?php _e('Notification Email', 'fbsr'); ?></label>
            </th>
            <td>
                <?php $this->print_input_text('global[email]', $op['email'], 'regular-text code'); ?>
            </td>
            <td>
                <span class="help">
                    <?php _e('Enter the address where the notification email will be sent. Make sure you have set anti-spam filter for wordpress@yourdomain.tld otherwise automated emails might go into spam folder.', 'fbsr'); ?>
                </span>
            </td>
        </tr>
    </tbody>
</table>
        <?php
    }

    public function meta_survey() {
        $op = get_option('wp_feedback_survey');
        ?>
<ul class="metabox-tabs">
    <?php for($i = 0; $i < 20; $i++) : ?>
    <li class="tab survey-<?php echo $i; ?>"><a href="javascript:void(null);" title="<?php echo __('Survey', 'fbsr') . ' ' . ($i+1); ?>" class="<?php if($i == 0) echo 'active'; ?>"><?php echo ($i+1); ?></a></li>
    <?php endfor; ?>
</ul>
<?php for($i = 0; $i < 20; $i++) : ?>
<div class="survey-<?php echo $i; ?>">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="survey_<?php echo $i; ?>_enabled"><?php _e('Enabled?', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_checkbox('survey[' . $i . '][enabled]', '1', $op[$i]['enabled'] == true); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Tick this to show this on survey form.', 'fbrs'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="survey_<?php echo $i; ?>_question"><?php _e('Survey Question', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_input_text('survey[' . $i . '][question]', $op[$i]['question'], 'large-text'); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Enter the question of this survey.', 'fbsr'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="survey_<?php echo $i; ?>_options"><?php _e('Answer Options', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_textarea('survey[' . $i . '][options]', $op[$i]['options'], 'large-text', 4); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Enter the answers of the questions. One answer at a line.', 'fbsr'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="survey_<?php echo $i; ?>_type"><?php _e('Question Type', 'fbsr'); ?></label>
                </th>
                <td>
                    <select name="survey[<?php echo $i; ?>][type]" id="survey_<?php echo $i; ?>_type">
                        <?php $this->print_select_op(array('single', 'multiple'), $op[$i]['type']); ?>
                    </select>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Single type options will be shown using selectbox dropdowns. Multiple will be shown using checkboxes'); ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php endfor; ?>
        <?php
    }

    public function meta_feedback() {
        $op = get_option('wp_feedback_feedback');
        ?>
<ul class="metabox-tabs">
    <?php for($i = 0; $i < 20; $i++) : ?>
    <li class="tab feedback-<?php echo $i; ?>"><a title="<?php echo __('Feedback', 'fbsr') . ' ' . ($i+1); ?>" href="javascript:void(null);" class="<?php if($i == 0) echo 'active'; ?>"><?php echo ($i+1); ?></a></li>
    <?php endfor; ?>
</ul>
<?php for($i = 0; $i < 20; $i++) : ?>
<div class="feedback-<?php echo $i; ?>">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="feedback_<?php echo $i; ?>_enabled"><?php _e('Enabled?', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_checkbox('feedback[' . $i . '][enabled]', '1', $op[$i]['enabled'] == true); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Tick this to show this on feedback form.', 'fbrs'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="feedback_<?php echo $i; ?>_name"><?php _e('Feedback Topic', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_input_text('feedback[' . $i . '][name]', $op[$i]['name'], 'large-text'); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Enter the topic name for this feedback.', 'fbsr'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="feedback_<?php echo $i; ?>_description"><?php _e('Feedback Description', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_textarea('feedback[' . $i . '][description]', $op[$i]['description'], 'large-text', 4); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('Enter the description which will be shown to the surveyer.', 'fbsr'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="feedback_<?php echo $i; ?>_email"><?php _e('Feedback Email', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_input_text('feedback[' . $i . '][email]', $op[$i]['email'], 'regular-text code'); ?>
                </td>
                <td>
                    <span class="help">
                        <?php _e('If you wish to mail yourself or your team member this particular feedback, then you can have email address here. Please note that you should add anti-spam filter, else auto generated emails might go into spam.', 'fbsr'); ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php endfor; ?>
        <?php
    }
}

class wp_feedback_view extends wp_feedback_base {

    public function __construct() {
        $this->capability = 'view_feedback';
        $this->action_nonce = 'wp_feedback_view_nonce';
        parent::__construct();

        $this->icon_url = $this->url['images'] . 'feedback_list_32.png';
    }

    public function admin_menu() {
        add_submenu_page('wp_feedback_dashboard', __('View a Feedback', 'fbsr'), __('View a Feedback', 'fbsr'), $this->capability, 'wp_feedback_view', array(&$this, 'index'));
        parent::admin_menu();
    }
    public function index() {
        $this->index_head(__('WP Feedback & Survey Manager &raquo; View a Feedback', 'fbsr'), false);
        if(isset($_GET['id']) || isset($_GET['id2'])) {
            $this->show_feedback();
        } else {
            $this->show_form();
        }
        $this->index_foot();
    }

    public function on_load_page() {
        parent::on_load_page();
        get_current_screen()->add_help_tab(array(
            'id' => 'overview',
            'title' => __('Overview', 'fbsr'),
            'content' =>
                '<p>' . __('Using this page, you can view a particular feedback either by it\'s ID (which is mailed to the notification email when a feedback is being submitted) Or select one from the latest 100.', 'fbsr') . '</p>',
        ));
        get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'fbsr'), wp_feedback_loader::$documentation) . '</p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Support Forums</a>', 'fbsr'), wp_feedback_loader::$support_forum) . '</p>'
	);
    }

    private function show_feedback() {
        $id = !empty($_GET['id']) ? (int) $_GET['id'] : $_GET['id2'];
        $f = new wp_feedback_view_cb();
        $f->show($id);
        echo '<p class="submit"><a href="admin.php?page=wp_feedback_view" class="button-primary">' . __('Go Back!', 'fbsr') . '</a></p>';
    }

    private function show_form() {
        global $wpdb, $wp_feedback_info;
        $s = array();
        $last100 = $wpdb->get_results("SELECT f_name, l_name, id FROM {$wp_feedback_info['feedback_table']} ORDER BY `date` DESC LIMIT 0, 100");
        if(empty($last100)) {
            $this->print_p_error(__('There are no feedback in the database. Please be patient!', 'fbsr'));
            return;
        }

        foreach($last100 as $l)
            $s[$l->id] = $l->f_name . ' ' . $l->l_name;
        ?>
<h3><?php _e('Select a Feedback', 'fbsr'); ?></h3>
<?php $this->print_p_update(__('Please either enter an ID or select one from the latest 100', 'fbsr')); ?>
<form action="" method="get">
    <?php foreach($_GET as $k => $v) : ?>
    <input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
    <?php endforeach; ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="id"><?php _e('Enter the ID', 'fbsr'); ?></label>
                </th>
                <td>
                    <?php $this->print_input_text('id', '', 'regular-text code'); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="id2"><?php _e('Or Select One', 'fbsr'); ?></label>
                </th>
                <td>
                    <select name="id2" id="id2">
                        <?php $this->print_select_op($s, null, true); ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" value="<?php _e('Submit', 'fbsr'); ?>" />
    </p>
</form>
        <?php
    }
}

class wp_feedback_report_survey extends wp_feedback_base {
    public $survey;

    public function __construct() {
        $this->capability = 'view_feedback';
        $this->action_nonce = 'wp_feedback_survey_report_nonce';
        parent::__construct();
        $this->icon_url = $this->url['images'] . 'report_32.png';
        $this->survey = get_option('wp_feedback_survey');

        add_action('wp_ajax_wp_feedback_survey_report', array(&$this, 'ajax_response'));
    }

    public function admin_menu() {
        $this->pagehook = add_submenu_page('wp_feedback_dashboard', __('Generate Report for Surveys', 'fbsr'), __('Survey Report', 'fbsr'), $this->capability, 'wp_feedback_report_survey', array(&$this, 'index'));
        parent::admin_menu();
    }
    public function index() {
        $this->index_head(__('Survey Report Generator', 'fbsr'), false);
        $this->show_form();
        $this->index_foot();
    }

    public function on_load_page() {
        parent::on_load_page();
        get_current_screen()->add_help_tab(array(
            'id' => 'overview',
            'title' => __('Overview', 'fbsr'),
            'content' =>
                '<p>' . __('This page provides a nice way to view all the survey reports from beginning to end. As this can be a bit database expensive, so reports are pulled 5 at a time. You will need JavaScript to view this page.', 'fbsr') . '</p>',
        ));
        get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'fbsr'), wp_feedback_loader::$documentation) . '</p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Support Forums</a>', 'fbsr'), wp_feedback_loader::$support_forum) . '</p>'
	);
    }

    /**
     * Shows the AJAX Starter form ;)
     * @global wpdb $wpdb
     * @global array $wp_feedback_info
     */
    public function show_form() {
        global $wpdb, $wp_feedback_info;

        $ids = $wpdb->get_col("SELECT id FROM {$wp_feedback_info['feedback_table']}");

        $info_count = array();

        if(empty($ids)) {
            $this->print_p_error(__('Sorry but no survey report yet!', 'fbsr'));
        } else {
            ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart', 'motionchart']});
</script>
<noscript><?php _e('You need to enabled JavaScript to view this page', 'fbsr'); ?></noscript>
<div id="wp_feedback_survey_pgbar" style="height: 25px;position: relative">
    <div id="wp_feedback_percent" style="font-weight:bold;text-align:center;position:absolute;left:50%;top:50%;width:50px;margin-left:-25px;height:25px;margin-top:-9px"></div>
</div>

<?php foreach($this->survey as $k => $survey) : ?>
<?php if($survey['enabled'] == true) : ?>
<?php $info_count[$k] = explode("\r\n", $survey['options']); ?>
<h3><?php echo $survey['question']; ?></h3>
<div id="wp_feedback_survey_wrap_<?php echo $k; ?>" style="text-align: center;">
    <img src="<?php echo $this->url['images'] . 'ajax.gif'; ?>" />
    <div id="wp_feedback_survey_wrap_<?php echo $k; ?>_col"></div>
    <div id="wp_feedback_survey_wrap_<?php echo $k; ?>_pie"></div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<script type="text/javascript">

jQuery(document).ready(function($) {
    var feedback_ids = <?php echo json_encode($ids); ?>;
    var feedback_total = feedback_ids.length;
    var feedback_count = 5;
    var feedback_percent = 0;

    var info_count = <?php echo json_encode($info_count, JSON_FORCE_OBJECT); ?>;

    var store_count = new Object();

    for(var question in info_count) {
        store_count[question] = new Object();
        for(var option in info_count[question]) {
            store_count[question][option] = 0;
        }
    }



    $('#wp_feedback_survey_pgbar').progressbar();
    $('#wp_feedback_percent').html('0%');

    function GetSurveyReport(id) {
        var data = {
            action : 'wp_feedback_survey_report',
            ids : id
        };
        $.post(ajaxurl, data, function(obj) {


            for(var question in obj) {
                //alert('For question ' + question)
                for(var option in obj[question]) {
                    //alert(info_count[question][option] + ' Count ' + obj[question][option]);
                    if(undefined != store_count[question])
                        store_count[question][option] += parseInt(obj[question][option]);
                }
            }

            feedback_percent = (feedback_count / feedback_total) * 100;
            $('#wp_feedback_survey_pgbar').progressbar('value', feedback_percent);
            var p_now = Math.round(feedback_percent);
            if(p_now > 100)
                p_now = 100;
            $('#wp_feedback_percent').html(p_now + '%');

            feedback_count += 5;

            if(feedback_ids.length) {
                GetSurveyReport([feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift()]);
            } else {
                for(var question in info_count) {
                    //alert('Question ' + question);
                    var data = new Array();
                    data[0] = new Array('<?php _e('Option', 'fbsr'); ?>', '<?php _e('Count', 'fbsr'); ?>');
                    for(var option in info_count[question]) {
                        //alert('Option ' + info_count[question][option] + ' Total Count ' + store_count[question][option]);

                        data[data.length] = new Array(info_count[question][option], store_count[question][option]);
                    }
                    //alert(question);
                    $('#wp_feedback_survey_wrap_' + question + ' img').hide();

                    var gdata = google.visualization.arrayToDataTable(data);
                    new google.visualization.PieChart(document.getElementById('wp_feedback_survey_wrap_' + question + '_pie')).draw(gdata, {
                        title : '<?php _e('Answers', 'fbsr'); ?>'
                    });
                    //$('#wp_feedback_survey_wrap' + question + 'pie').
                }
            }
        }, 'json');
    }

    GetSurveyReport([feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift(), feedback_ids.shift()]);
});
</script>
            <?php
        }
    }


    /**
     *
     * @global wpdb $wpdb
     * @global array $wp_feedback_info
     */
    public function ajax_response() {
        global $wpdb, $wp_feedback_info;
        $results = $wpdb->get_results("SELECT survey FROM {$wp_feedback_info['feedback_table']} WHERE id IN (" . implode(',', $this->post['ids']) . ")");

        $return = array();



        foreach($results as $result) {
            $result = maybe_unserialize($result->survey);
            foreach($result as $k => $r) {
                if(is_array($r)) {
                    foreach($r as $l) {
                        $return[$k][$l]++;
                    }
                } else {
                    $return[$k][$r]++;
                }
            }
        }


        echo json_encode($return, JSON_FORCE_OBJECT);
        die();
    }
}

class wp_feedback_view_all extends wp_feedback_base {
    /**
     * The feedback table class object
     * Should be instantiated on-load
     * @var WP_Feedback_Table
     */
    public $table_view;
    public function __construct() {
        $this->capability = 'manage_feedback';
        $this->action_nonce = 'wp_feedback_view_all_nonce';

        parent::__construct();
        $this->icon_url = $this->url['images'] . 'feedback_list_32.png';
        add_filter('set-screen-option', array(&$this, 'table_set_option'), 10, 3);

        $this->post_result[4] = array(
            'type' => 'update',
            'msg' => __('Successfully deleted the feedbacks', 'fbsr'),
        );
        $this->post_result[5] = array(
            'type' => 'error',
            'msg' => __('Please select an action', 'fbsr'),
        );
        $this->post_result[6] = array(
            'type' => 'update',
            'msg' => __('Successfully deleted the feedback', 'fbsr'),
        );
    }

    public function admin_menu() {
        $this->pagehook = add_submenu_page('wp_feedback_dashboard', __('View all Feedbacks', 'fbsr'), __('View all Feedbacks', 'fbsr'), $this->capability, 'wp_feedback_view_all', array(&$this, 'index'));
        parent::admin_menu();
    }

    public function index() {

        $this->index_head(__('WP Feedback & Survey Manager &raquo; View All', 'fbsr'), false);
        $this->table_view->prepare_items();
        ?>
<form action="" method="get">
    <?php foreach($_GET as $k => $v) : if($k != 'paged' && $k != 's') : ?>
    <input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
    <?php endif; endforeach; ?>
    <?php $this->table_view->search_box(__('Search Feedbacks', 'fbsr'), 'search_id'); ?>
</form>
<form action="" method="post">
    <?php $this->table_view->display(); ?>
    <?php $this->index_foot(); ?>
</form>
        <?php

    }

    public function save_post() {
        parent::save_post(false);
        if(!wp_verify_nonce($_POST['_wpnonce'], 'bulk-wp_feedback_table_items')) {
            echo 'Havva';
            wp_die(__('Cheatin&#8217; uh?'));
        }


        $feedbacks = implode(',', $this->post['feedbacks']);
        global $wpdb, $wp_feedback_info;
        if($this->post['action'] == 'delete' || $this->post['action2'] == 'delete') {
            if($wpdb->query("DELETE FROM {$wp_feedback_info['feedback_table']} WHERE id IN ({$feedbacks})")) {
                wp_redirect(add_query_arg(array('post_result' => 4), $_POST['_wp_http_referer']));
            } else {
                wp_redirect(add_query_arg(array('post_result' => 2), $_POST['_wp_http_referer']));
            }
        } else {
            wp_redirect(add_query_arg(array('post_result' => 5), $_POST['_wp_http_referer']));
        }


        die();
    }

    public function on_load_page() {
        global $wpdb, $wp_feedback_info;

        if($_SERVER['REQUEST_METHOD'] == 'POST')
            $this->save_post ();

        if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            if(wp_verify_nonce($_GET['_wpnonce'], 'wp_feedback_delete_' . $_GET['id'])) {
                $wpdb->query($wpdb->prepare("DELETE FROM {$wp_feedback_info['feedback_table']} WHERE id = %d", $_GET['id']));
                wp_redirect(add_query_arg(array('post_result' => 6), 'admin.php?page=' . $_GET['page']));
            } else {
                wp_die(__('Cheatin&#8217; uh?'));
            }
            die();
        }

        $option = 'per_page';
        $args = array(
            'label' => __('Feedback Rows', 'fbsr'),
            'default' => 20,
            'option' => 'feedbacks_per_page',
        );
        add_screen_option($option, $args);
        $this->table_view = new WP_Feedback_Table();
        parent::on_load_page();

        get_current_screen()->add_help_tab( array(
	'id'		=> 'overview',
	'title'		=> __('Overview'),
	'content'	=>
		'<p>' . __('This screen provides access to all of your feedbacks & surveys. You can customize the display of this screen to suit your workflow.', 'fbsr') . '</p>'
	) );
	get_current_screen()->add_help_tab( array(
	'id'		=> 'screen-content',
	'title'		=> __('Screen Content'),
	'content'	=>
		'<p>' . __('You can customize the display of this screen&#8217;s contents in a number of ways:') . '</p>' .
		'<ul>' .
			'<li>' . __('You can hide/display columns based on your needs and decide how many feedbacks to list per screen using the Screen Options tab.', 'fbsr') . '</li>' .
			'<li>' . __('You can search a particular feedback by using the Search Form. You can type in just the first name or the last name or the email or the ID or even the IP Address.', 'fbsr') . '</li>' .
		'</ul>'
	) );
	get_current_screen()->add_help_tab( array(
	'id'		=> 'action-links',
	'title'		=> __('Available Actions'),
	'content'	=>
		'<p>' . __('Hovering over a row in the posts list will display action links that allow you to manage your feedbacks. You can perform the following actions:', 'fbsr') . '</p>' .
		'<ul>' .
			'<li>' . __('<strong>Preview</strong> pops up a modal window with the detailed preview of the particular feedback.', 'fbsr') . '</li>' .
			'<li>' . __('<strong>Delete</strong> removes your feedback from this list as well as from the database. You can restore it back, so make sure you want to delete it before you do.', 'fbsr') . '</li>' .
		'</ul>'
	) );
	get_current_screen()->add_help_tab( array(
	'id'		=> 'bulk-actions',
	'title'		=> __('Bulk Actions'),
	'content'	=>
		'<p>' . __('The only bulk action available right now is <strong>Delete</strong>. This will permanently delete the ticked feedbacks from the database.', 'fbsr') . '</p>'
	) );

	get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'fbsr'), wp_feedback_loader::$documentation) . '</p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Support Forums</a>', 'fbsr'), wp_feedback_loader::$support_forum) . '</p>'
	);
    }

    public function table_set_option($status, $option, $value) {
        return $value;
    }
}

/**
 * Dashboard class
 */
class wp_feedback_dashboard extends wp_feedback_base {
    public function __construct() {
        $this->capability = 'view_feedback';
        $this->action_nonce = 'wp_feedback_dashboard_nonce';

        parent::__construct();

        $this->icon_url = $this->url['images'] . 'feedback_32.png';
        $this->is_metabox = true;
    }

    /*________________SYSTEM METHODS________________*/
    public function admin_menu() {
        $this->pagehook = add_object_page(__('WP Feedback & Survey Manager', 'fbsr'), __('Feedback', 'fbsr'), $this->capability, 'wp_feedback_dashboard', array(&$this, 'index'), $this->url['images'] . 'feedback_18.png');
        add_submenu_page('wp_feedback_dashboard', __('WP Feedback & Survey Manager', 'fbsr'), __('Dashboard', 'fbsr'), $this->capability, 'wp_feedback_dashboard', array(&$this, 'index'));
        parent::admin_menu();
    }
    public function index() {
        $this->index_head(__('WP Feedback & Survey Manager &raquo; Dashboard', 'fbsr'));
        ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart', 'motionchart']});
</script>
<div class="metabox-holder">
    <?php $this->print_metabox_containers('normal'); ?>
    <?php $this->print_metabox_containers('side'); ?>
</div>
<div class="clear"></div>
<div class="metabox-holder">
    <?php $this->print_metabox_containers('debug'); ?>
</div>
        <?php
        $this->index_foot(false);
    }

    public function on_load_page() {
        parent::on_load_page();

        add_meta_box('wp_feedback_meta_tq', __('Thank You!', 'fbsr'), array(&$this, 'meta_thank_you'), $this->pagehook, 'normal', 'high');
        add_meta_box('wp_feedback_meta_social', __('Support & Social', 'fbsr'), array(&$this, 'meta_social'), $this->pagehook, 'side', 'high');

        add_meta_box('wp_feedback_meta_trend', __('Latest 100 Survey Reports'), array(&$this, 'meta_trend'), $this->pagehook, 'debug', 'high');

        get_current_screen()->add_help_tab(array(
            'id' => 'overview',
            'title' => __('Overview', 'fbsr'),
            'content' =>
                '<p>' . __('Thank you for choosing WP Feedback & Survey Manager Plugin. This screen provides some basic information of the plugin and Latest 100 Survey Reports. The design is integrated from WordPress\' own framework. So you should feel like home!', 'fbsr') . '<p>' .
                '<p>' . __('The concept and working of the Plugin is very simple.', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('You setup the form from the <a href="admin.php?page=wp_feedback_settings">Settings Page</a>.', 'fbsr') . '</li>' .
                        '<li>' . __('You use the Shortcodes (check the Shortcodes tab on this help screen) for displaying on your Site/Blog.', 'fbsr') . '</li>' .
                        '<li>' . __('Finally use the <a href="admin.php?page=wp_feedback_report_survey">Survey Reports</a> Or <a href="admin.php?page=wp_feedback_view_all">View all Feedbacks</a> pages to analyze the submissions.', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('Sounds easy enough? Then get started by going to the <a href="admin.php?page=wp_feedback_settings">Settings Page</a> now. You can always click on the <strong>HELP</strong> button above the screen to know more.', 'fbsr') . '</p>' .
                '<p>' . __('If you have any suggestions or have encountered any bug, please feel free to use the WordPress support forum', 'fbsr') . '</p>',
        ));

        get_current_screen()->add_help_tab(array(
            'id' => 'shortcodes',
            'title' => __('Shortcodes', 'fbsr'),
            'content' =>
                '<p>' . __('This plugin comes with two shortcodes. One for displaying the FORM and other for displaying the Trends (The same Latest 100 Survey Reports you see on this screen)', 'fbsr') . '</p>' .
                '<ul>' .
                        '<li>' . __('<code>[feedback]</code> : Just use this inside a Post/Page and the form will start appearing.', 'fbsr') . '</li>' .
                        '<li>' . __('<code>[feedback_trend]</code> : Use this to show the Trends based on latest 100 Survey Reports for all available questions. Just like the dashboard widget on this screen.', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('If the output of the shortcodes look weird, then probably you have copied them from the list above with the <code>&lt;code&gt;</code> HTML markup. Please delete them and manually write the shortcode.', 'fbsr') . '</p>',
        ));

        get_current_screen()->add_help_tab(array(
            'id' => 'credits',
            'title' => __('Credits', 'fbsr'),
            'content' =>
                '<p>' . __('The very basic & simplest form of the idea of this plugin came from my friend <strong>Arnab Saha</strong> during our Annual College Fest. As the development began, we pondered upon more ideas and finally we released it publicly.', 'fbsr') . '</p>' .
                '<p>' . __('The plugin uses a few free and/or open source products, which are:', 'fbsr') .
                '<ul>' .
                        '<li>' . __('<strong><a href="http://www.google.com/webfonts/">Google WebFont</a></strong> : To make the form look better.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong><a href="http://jqueryui.com/">jQuery UI</a></strong> : Renders the basic "Tab Like" appearance of the form.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong><a href="https://developers.google.com/chart/">Google Charts Tool</a></strong> : Renders the report charts on both backend as well as frontend.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong><a href="https://github.com/posabsolute/jQuery-Validation-Engine">jQuery Validation Engine</a></strong> : Wonderful form validation plugin from Position-absolute. Please note that we are using version 2.2 of this plugin which works while trying to validate a particular div and all form elements inside it.', 'fbsr') . '</li>' .
                        '<li>' . __('<strong>Icons</strong> : <a href="http://www.oxygen-icons.org/" target="_blank">Oxygen Icons</a> and <a href="http://www.woothemes.com/2009/09/woofunction-178-amazing-web-design-icons/" target="_blank">WooFunctions Icon</a>', 'fbsr') . '</li>' .
                '</ul>' .
                '<p>' . __('Also special thanks to <em>Prateek Sarkar</em> & <em>Sayantan Mukherjee</em> for helping me with the beta testing of this plugin.', 'fbsr') . '</p>' .
                '<p>' . __('Once again thanks for using this plugin. If you have any suggestions, we would like to hear. If you can, then please support the future development of this plugin by donating.', 'fbsr') . '</p>',
        ));

        get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Documentation</a>', 'fbsr'), wp_feedback_loader::$documentation) . '</p>' .
	'<p>' . sprintf(__('<a href="%s" target="_blank">Support Forums</a>', 'fbsr'), wp_feedback_loader::$support_forum) . '</p>'
	);
    }


    /*_______________METABOX CB____________________*/
    public function meta_thank_you() {
        ?>
<p>
    <?php _e('Thank you for using WP Feedback and Survey Manager Plugin. I\'ve made this plugin keeping the priority of our college fest in mind. But I would like to listen to your suggestions to improve it.', 'fbsr'); ?>
</p>
<ul class="ul-square">
    <li><?php _e('<strong>Plugin Author</strong>: <a href="http://www.intechgrity.com">Swashata</a>', 'fbsr'); ?></li>
    <li><?php _e('<strong>Documentation</strong>: <a href="http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/">Click Here</a>', 'fbsr'); ?></li>
    <li><?php _e('<strong>Hire Us</strong>: <a href="mailto:swashata@intechgrity.com">Get in Touch</a>', 'fbsr'); ?></li>
</ul>
<p>
    <?php printf(__('<strong>Plugin Version:</strong> <em>%s</em>', 'fbsr'), wp_feedback_loader::$version); ?>
</p>
<div class="p-message">
    <p>
        <strong><?php _e('If you find it useful, then please support us by liking our facebook fan page, Rating this plugin 5/5 on WordPress repository or donating. Countless hours have been invested to make this plugin one of the finest :-) Any form of support is highly appreciated.', 'fbsr'); ?></strong>
    </p>
<script src="http://feeds.feedburner.com/iTgWordPress?format=sigpro" type="text/javascript" ></script><noscript><p>Subscribe to RSS headline updates from: <a href="http://feeds.feedburner.com/iTgWordPress"></a><br/>Powered by FeedBurner</p> </noscript>
</div>
        <?php
    }

    public function meta_social() {
        ?>
<div class="p-message">
    <p style="text-align: center;">
        <a href="http://www.intechgrity.com/about/buy-us-some-beer/" target="_blank" title="Buy us some beer? Thank you!"><img src="<?php echo $this->url['images'] . 'donate.png' ?>" alt="Donate" /></a>
    </p>
</div>
<p style="float: left; text-align: center">
    <a href="http://www.facebook.com/swashata" target="_blank" title="Wanna be my friend? Go ahead... :-)"><img src="<?php echo $this->url['images'] . 'facebook_add.png' ?>" alt="Facebook Friends" /></a><br />
    <a href="http://www.facebook.com/intechgrity" target="_blank" title="Be our Facebook Page Fan? Thanks again :)"><img alt="Facebook FanPage" src="<?php echo $this->url['images'] . 'facebook_follow.png'; ?>" /></a>
</p>
<p style="float: right; text-align: center">
    <a href="http://www.twitter.com/swashata" target="_blank" title="Follow my tweets!"><img src="<?php echo $this->url['images'] . 'twitter_follow.png' ?>" alt="Twitter Follow" /></a><br />
</p>
<div class="clear"></div>

<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fintechgrity&amp;width=450&amp;height=170&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false&amp;appId=124472621612" scrolling="no" frameborder="0" style="display: block; border:none; overflow:hidden; width:450px; height:170px; margin: 0 auto" allowTransparency="true"></iframe>
<p style="text-align: center; margin: 2.5px auto 0">
    <?php _e('Icons Used from: ', 'fbsr'); ?> <a href="http://twitterbuttons.sociableblog.com/" target="_blank">SociableBlog</a> | <a href="http://www.oxygen-icons.org/" target="_blank">Oxygen Icons</a> | <a href="http://www.woothemes.com/2009/09/woofunction-178-amazing-web-design-icons/" target="_blank">WooFunctions Icon</a>
</p>
        <?php
    }

    public function meta_trend() {
        ?>
<p>
    <?php _e('To display similar report on your blog, use the shortcode <code>[feedback_trend]</code>. Just put it in a post or preferably a full-width page.', 'fbsr'); ?>
</p>
        <?php
        $trend = new wp_feedback_trend();
        $trend->print_trend();
    }
}

/**
 * The base admin class
 * @abstract
 */
abstract class wp_feedback_base {
    /**
     * Duplicates the $_POST content and properly process it
     * Holds the typecasted (converted int and floats properly and escaped html) value after the constructor has been called
     * @var array
     */
    var $post = array();

    /**
     * Holds the hook of this page
     * @var string Pagehook
     * Should be set during the construction
     */
    var $pagehook;

    /**
     * The nonce for admin-post.php
     * Should be set the by extending class
     * @var string
     */
    var $action_nonce;

    /**
     * The URL of the admin page icon
     * Should be set by the extending class
     * @var string
     */
    var $icon_url;

    /**
     * This gets passed directly to current_user_can
     * Used for security and should be set by the extending class
     * @var string
     */
    var $capability;

    /**
     * Holds the URL of the static directories
     * Just the /static/admin/ URL and sub directories under it
     * access it like $url['js'], ['images'], ['css'], ['root'] etc
     * @var array
     */
    var $url = array();

    /**
     * Set this to true if you are going to use the WordPress Metabox appearance
     * This will enqueue all the scripts and will also set the screenlayout option
     * @var bool False by default
     */
    var $is_metabox = false;

    /**
     * Default number of columns on metabox
     * @var int
     */
    var $metabox_col = 2;

    /**
     * Holds the post result message string
     * Each entry is an associative array with the following options
     *
     * $key : The code of the post_result value =>
     *
     *      'type' => 'update' : The class of the message div update | error
     *
     *      'msg' => '' : The message to be displayed
     *
     * @var array
     */
    var $post_result = array();

    /**
     * The action value to be used for admin-post.php
     * This is generated automatically by appending _post_action to the action_nonce variable
     * @var string
     */
    var $admin_post_action;

    /**
     * Whether or not to print form on the admin wrap page
     * Mainly for manually printing the form
     * @var bool
     */
    var $print_form;

    /**
     * The constructor function
     * 1. Properly copies the $_POST to $this->post on POST request
     * 2. Calls the admin_menu() function
     * You should have parent::__construct() for all these to happen
     */
    public function __construct() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->post = $_POST;

            if(get_magic_quotes_gpc())
                array_walk_recursive ($this->post, array($this, 'stripslashes_gpc'));

            array_walk_recursive ($this->post, array($this, 'htmlspecialchar_ify'));
        }

        $plugin = wp_feedback_loader::$abs_file;

        $this->url = array(
            'root' => plugins_url('/static/admin/', $plugin),
            'js' => plugins_url('/static/admin/js/', $plugin),
            'images' => plugins_url('/static/admin/images/', $plugin),
            'css' => plugins_url('/static/admin/css/', $plugin),
        );

        $this->post_result = array(
            1 => array(
                'type' => 'update',
                'msg' => __('Successfully saved the options'),
            ),
            2 => array(
                'type' => 'error',
                'msg' => __('Either you have not changed anything or some error has occured. Please contact the developer'),
            ),
            3 => array(
                'type' => 'update',
                'msg' => __('The Master Reset was successful'),
            ),
        );

        $this->admin_post_action = $this->action_nonce . '_post_action';

        //register admin_menu hook
        add_action('admin_menu', array(&$this, 'admin_menu'));

        //register admin-post.php hook
        add_action('admin_post_' . $this->admin_post_action, array(&$this, 'save_post'));
    }

    /*______________________________________SYSTEM METHODS______________________________________*/

    /**
     * Hook to the admin menu
     * Should be overriden and also the hook should be saved in the $this->pagehook
     * In the end, the parent::admin_menu() should be called for load to hooked properly
     */
    public function admin_menu() {
        add_action('load-' . $this->pagehook, array(&$this, 'on_load_page'));
        //$this->pagehook = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
        //do the above or similar in the overriden callback function
    }

    /**
     * Use this to generate the admin page
     * always call parent::index() so the save post is called
     * also call $this->index_foot() after the generation of page (the last line of this function)
     * to give some compatibility (mainly with the metaboxes)
     * @access public
     */
    abstract public function index();

    protected function index_head($title = '', $print_form = true) {
        $this->print_form = $print_form;
        ?>
<style type="text/css">
    <?php echo '#' . $this->pagehook; ?>-widgets .meta-box-sortables {
        margin: 0 8px;
    }
</style>
<div class="wrap" id="<?php echo $this->pagehook; ?>-widgets">
    <div class="icon32">
        <img src="<?php echo $this->icon_url; ?>" height="32" width="32" alt="icon" />
    </div>
    <h2><?php echo $title; ?></h2>
    <?php
        if(isset($_GET['post_result'])) {
            $msg = $this->post_result[(int) $_GET['post_result']];
            if(!empty($msg)) {
                if($msg['type'] == 'update' || $msg['type'] == 'updated')
                    $this->print_update($msg['msg']);
                else
                    $this->print_error($msg['msg']);
            }
        }
    ?>
    <?php if($this->print_form) : ?>
    <form method="post" action="admin-post.php">
        <input type="hidden" name="action" value="<?php echo $this->admin_post_action; ?>" />
        <?php wp_nonce_field($this->action_nonce, $this->action_nonce); ?>
        <?php if($this->is_metabox) : ?>
        <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
        <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
        <?php endif; ?>
    <?php endif; ?>
        <?php
    }

    /**
     * Include this to the end of index function so that metaboxes work
     */
    protected function index_foot($submit = true, $text = 'Save Changes') {
        ?>
    <?php if($this->print_form) : ?>
        <?php if(true == $submit) : ?>
        <div class="clear" />
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e($text, 'wpadmrs'); ?>" name="submit" />&nbsp;
            <input type="reset" class="button-secondary" value="<?php _e('Reset', 'wpadmrs'); ?>" name="reset" />
        </p>
        <?php endif; ?>
    </form>
    <?php endif; ?>
</div>
<?php if($this->is_metabox) : ?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready( function($) {
        // close postboxes that should be closed
        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
        // postboxes setup
        postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
});
//]]>
</script>
<?php endif; ?>
        <?php
    }

    /**
     * Override to manage the save_post
     * This should be written by all the classes extending this
     *
     *
     * * General Template
     *
     * //process here your on $_POST validation and / or option saving
     *
     * //lets redirect the post request into get request (you may add additional params at the url, if you need to show save results
     * wp_redirect(add_query_arg(array(), $_POST['_wp_http_referer']));
     *
     *
     */
    public function save_post($check_referer = true) {
        //user permission check
        if (!current_user_can($this->capability))
            wp_die(__('Cheatin&#8217; uh?'));
        //check nonce
        if($check_referer) {
            if(!wp_verify_nonce($_POST[$this->action_nonce], $this->action_nonce))
                wp_die(__('Cheatin&#8217; uh?'));
        }

        //process here your on $_POST validation and / or option saving

        //lets redirect the post request into get request (you may add additional params at the url, if you need to show save results
        //wp_redirect(add_query_arg(array(), $_POST['_wp_http_referer']));
        //The above should be done by the extending after calling parent::save_post and processing post
    }

    /**
     * Hook to the load plugin page
     * This should be overriden
     * Also call parent::on_load_page() for screenoptions
     * @uses add_meta_box
     */
    public function on_load_page() {
        if($this->is_metabox) {
            add_screen_option('layout_columns', array(
                'max' => 2,
                'default' => $this->metabox_col,
            ));
            wp_enqueue_script('common');
            wp_enqueue_script('wp-lists');
            wp_enqueue_script('postbox');

            /**
             * MetaBox Tab like wp-stat
             * @link  http://developersmind.com/2011/04/05/wordpress-tabbed-metaboxes/
             */
            wp_enqueue_style('jf-metabox-tabs', $this->url['css'] . 'metabox-tabs.css');
            wp_enqueue_script('jf-metabox-tabs', $this->url['js'] . 'metabox-tabs.js', array( 'jquery' ) );
        }
    }

    /**
     * Get the pagehook of this class
     * @return string
     */
    public function get_pagehook() {
        return $this->pagehook;
    }

    /**
     * Prints the metaboxes of a custom context
     * Should atleast pass the $context, others are optional
     *
     * The screen defaults to the $this->pagehook so make sure it is set before using
     * This should be the return value given by add_admin_menu or similar function
     *
     * The function automatically checks the screen layout columns and prints the normal/side columns accordingly
     * If screen layout column is 1 then even if you pass with context side, it will be hidden
     * Also if screen layout is 1 and you pass with context normal, it will get full width
     *
     * @param string $context The context of the metaboxes. Depending on this HTML ids are generated. Valid options normal | side
     * @param string $container_classes (Optional) The HTML class attribute of the container
     * @param string $container_style (Optional) The RAW inline CSS style of the container
     */
    public function print_metabox_containers($context = 'normal', $container_classes = '', $container_style = '') {
        global $screen_layout_columns;
        $style = 'width: 50%;';

        //check to see if only one column has to be shown

        if(isset($screen_layout_columns) && $screen_layout_columns == 1) {
            //normal?
            if('normal' == $context) {
                $style = 'width: 100%;';
            } else if ('side' == $context) {
                $style = 'display: none;';
            }
        }

        //override for the special debug area (1 column)
        if('debug' == $context) {
            $style = 'width: 100%;';
            $container_classes .= ' debug-metabox';
        }
        ?>
<div class="postbox-container <?php echo $container_classes; ?>" style="<?php echo $style . $container_style; ?>" id="<?php echo (('normal' == $context)? 'postbox-container-1' : 'postbox-container-2'); ?>">
    <?php do_meta_boxes($this->pagehook, $context, ''); ?>
</div>
        <?php
    }


    /*______________________________________INTERNAL METHODS______________________________________*/

    /**
     * Prints error msg in WP style
     * @param string $msg
     */
    protected function print_error($msg = '', $echo = true) {
        $output = '<div class="error fade"><p>' . $msg . '</p></div>';
        if($echo)
            echo $output;
        else
            return $output;
    }

    protected function print_update($msg = '', $echo = true) {
        $output = '<div class="updated fade"><p>' . $msg . '</p></div>';
        if($echo)
            echo $output;
        else
            return $output;
    }

    protected function print_p_error($msg = '', $echo = true) {
        $output = '<div class="p-message red"><p>' . $msg . '</p></div>';
        if($echo)
            echo $output;
        return $output;
    }

    protected function print_p_update($msg = '', $echo = true) {
        $output = '<div class="p-message yellow"><p>' . $msg . '</p></div>';
        if($echo)
            echo $output;
        return $output;
    }

    protected function print_p_okay($msg = '', $echo = true) {
        $output = '<div class="p-message green"><p>' . $msg . '</p></div>';
        if($echo)
            echo $output;
        return $output;
    }

    /**
     * stripslashes gpc
     * Strips Slashes added by magic quotes gpc thingy
     * @access protected
     * @param string $value
     */
    protected function stripslashes_gpc(&$value) {
        $value = stripslashes($value);
    }

    protected function htmlspecialchar_ify(&$value) {
        $value = htmlspecialchars($value);
    }

    /*______________________________________SHORTCUT HTML METHODS______________________________________*/

    /**
     * Shortens a string to a specified character length.
     * Also removes incomplete last word, if any
     * @param string $text The main string
     * @param string $char Character length
     * @param string $cont Continue character(…)
     * @return string
     */
    public function shorten_string($text, $char, $cont = '…') {
        $text = strip_tags(strip_shortcodes($text));
        $text = substr($text, 0, $char); //First chop the string to the given character length
        if(substr($text, 0, strrpos($text, ' '))!='') $text = substr($text, 0, strrpos($text, ' ')); //If there exists any space just before the end of the chopped string take upto that portion only.
        //In this way we remove any incomplete word from the paragraph
        $text = $text.$cont; //Add continuation ... sign
        return $text; //Return the value
    }

    /**
     * Get the first image from a string
     * @param string $html
     * @return mixed string|bool The src value on success or boolean false if no src found
     */
    public function get_first_image($html) {
        $matches = array();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $html, $matches);
        if(!$output) {
            return false;
        }
        else {
            $src = $matches[1][0];
            return trim($src);
        }
    }

    /**
     * Wrap a RAW JS inside <script> tag
     * @param String $string The JS
     * @return String The wrapped JS to be used under HTMl document
     */
    public function js_wrap( $string ) {
            return "\n<script type='text/javascript'>\n" . $string . "\n</script>\n";
    }

    /**
     * Wrap a RAW CSS inside <style> tag
     * @param String $string The CSS
     * @return String The wrapped CSS to be used under HTMl document
     */
    public function css_wrap( $string ) {
            return "\n<style type='text/css'>\n" . $string . "\n</style>\n";
    }

    /**
     * Prints options of a selectbox
     *
     * @param array $ops Should pass either an array of string ('label1', 'label2') or associative array like array('val' => 'val1', 'label' => 'label1'),...
     * @param string $key The key in the haystack, if matched a selected="selected" will be printed
     */
    public function print_select_op($ops, $key, $inner = false) {
        foreach((array) $ops as $k => $op) : ?>
        <?php if(!is_array($op)) : if(!$inner) $op = array('val' => $op, 'label' => ucfirst ($op)); else $op = array('val' => $k, 'label' => $op); endif; ?>
<option value="<?php echo esc_attr($op['val']); ?>"<?php if($key == $op['val']) echo ' selected="selected"'; ?>><?php echo $op['label']; ?></option>
        <?php endforeach;
    }

    /**
     * Prints a set of checkboxes for a single HTML name
     *
     * @param string $name The HTML name of the checkboxes
     * @param array $items The associative array of items array('val' => 'value', 'label' => 'label'),...
     * @param array $checked The array of checked items. It matches with the 'val' of the haystack array
     * @param string $sep (Optional) The seperator, HTML non-breaking-space (&nbsp;) by default. Can be <br /> or anything
     */
    public function print_checkboxes($name, $items, $checked, $sep = '&nbsp;&nbsp;') {
        if(!is_array($checked))
            $checked = (array) $checked;
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        foreach((array) $items as $item) : ?>
<label for="<?php echo esc_attr($id . '_' . $item['val']); ?>">
    <input type="checkbox" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($id . '_' . $item['val']); ?>" value="<?php echo esc_attr($item['val']); ?>"<?php if(in_array($item['val'], $checked)) echo ' checked="checked"'; ?> /> <?php echo $item['label']; ?>
</label>
        <?php echo $sep;
        endforeach;
    }

    /**
     * Prints a set of radioboxes for a single HTML name
     *
     * @param string $name The HTML name of the checkboxes
     * @param array $items The associative array of items array('val' => 'value', 'label' => 'label'),...
     * @param string $checked The value of checked radiobox. It matches with the val of the haystack
     * @param string $sep (Optional) The seperator, two HTML non-breaking-space (&nbsp;) by default. Can be <br /> or anything
     */
    public function print_radioboxes($name, $items, $checked, $sep = '&nbsp;&nbsp;') {
        if(!is_string($checked))
            $checked = (string) $checked;
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        foreach((array) $items as $item) : ?>
<label for="<?php echo esc_attr($id . '_' . $item['val']); ?>">
    <input type="radio" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($id . '_' . $item['val']); ?>" value="<?php echo esc_attr($item['val']); ?>"<?php if($checked == $item['val']) echo ' checked="checked"'; ?> /> <?php echo $item['label']; ?>
</label>
        <?php echo $sep;
        endforeach;
    }

    /**
     * Print a single checkbox
     * Useful for printing a single checkbox like for enable/disable type
     *
     * @param string $name The HTML name
     * @param string $value The value attribute
     * @param mixed (string|bool) $checked Can be true or can be equal to the $value for adding checked attribute. Anything else and it will not be added.
     */
    public function print_checkbox($name, $value, $checked) {
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
<input type="checkbox" name="<?php echo esc_attr($name); ?>" id="<?php echo $id; ?>" value="<?php echo esc_attr($value); ?>"<?php if($value == $checked || true == $checked) echo ' checked="checked"'; ?> />
        <?php
    }

    /**
     * Prints a input[type="text"]
     * All attributes are escaped except the value
     * @param string $name The HTML name attribute
     * @param string $value The value of the textbox
     * @param string $class (Optional) The css class defaults to regular-text
     */
    public function print_input_text($name, $value, $class = 'regular-text') {
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
<input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo $value; ?>" class="<?php echo esc_attr($class); ?>" />
        <?php
    }

    /**
     * Prints a <textarea> with custom attributes
     * All attributes are escaped except the value
     * @param string $name The HTML name attribute
     * @param string $value The value of the textbox
     * @param string $class (Optional) The css class defaults to regular-text
     * @param int $rows (Optional) The number of rows in the rows attribute
     * @param int $cols (Optional) The number of columns in the cols attribute
     */
    public function print_textarea($name, $value, $class = 'regular-text', $rows = 3, $cols = 20) {
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
<textarea name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($class); ?>" rows="<?php echo (int) $rows; ?>" cols="<?php echo (int) $cols; ?>"><?php echo $value; ?></textarea>
        <?php
    }


    /**
     * Displays a jQuery UI Slider to the page
     * @param string $name The HTML name of the input box
     * @param int $value The initial/saved value of the input box
     * @param int $max The maximum of the range
     * @param int $min The minimum of the range
     * @param int $step The step value
     */
    public function print_ui_slider($name, $value, $max = 100, $min = 0, $step = 1) {
        ?>
<div class="slider"></div>
<input type="text" class="small-text code slider-text" max="<?php echo $max; ?>" min="<?php echo $min; ?>" step="<?php echo $step; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
        <?php
    }

    /**
     * Prints a jPicker ColorPicker
     *
     * @param string $name The HTML name of the input box
     * @param string $value The HEX color code
     */
    public function print_jpicker($name, $value) {
        $value = ltrim($value, '#');
        ?>
<input type="text" class="jcolor-picker code" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
        <?php
    }

    /**
     * Prints a input box with an attached upload button
     *
     * @param string $name The HTML name of the input box
     * @param string $value The value of the input box
     */
    public function print_uploadbutton($name, $value) {
        ?>
<input type="text" class="regular-text code" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />&nbsp;
<input class="upload-button" type="button" value="<?php _e('Upload'); ?>" />
        <?php
    }
}

/**
 * Get the WP_List_Table for populating our table
 */
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class WP_Feedback_Table extends WP_List_Table {
    public $feedback;

    public function __construct() {
        $this->feedback = get_option('wp_feedback_feedback');

        parent::__construct(array(
            'singular' => 'wp_feedback_table_item',
            'plural' => 'wp_feedback_table_items',
            'ajax' => true,
        ));
    }

    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Name', 'fbsr'),
            'email' => __('Email', 'fbsr'),
            'phone' => __('Phone', 'fbsr'),
            'date' => __('Date', 'fbsr'),
            'ip' => __('IP Address', 'fbsr'),
            'feedback' => __('Feedbacks', 'fbsr'),
            'opinion' => __('Opinion', 'fbsr'),
        );
        return $columns;
    }

    public function get_sortable_columns() {
        $sortable = array(
            'title' => array('f_name', false),
            'date' => array('date', true),
            'email' => array('email', false),
            'phone' => array('phone', false),
            'ip' => array('ip', false),
        );
        return $sortable;
    }

    public function column_default($item, $column_name) {
        switch($column_name) {
            case 'title' :
                $actions = array(
                    'view' => sprintf('<a class="thickbox" href="admin-ajax.php?action=view_feedback&id=%d&width=640&height=500">%s</a>', (int) $item['id'], __('View', 'fbsr')),
                    'delete' => '<a class="delete" href="' . wp_nonce_url('?page=' . $_REQUEST['page'] . '&action=delete&id=' . $item['id'], 'wp_feedback_delete_' . $item['id']) . '">' . __('Delete', 'fbsr') . '</a>',
                );
                return sprintf('%1$s %2$s', '<strong><a class="thickbox" href="admin-ajax.php?action=view_feedback&id=' . $item['id'] . '&width=640&height=500">' . $item['f_name'] . ' ' . $item['l_name'] . '</a></strong>', $this->row_actions($actions));
                break;
            case 'email' :
                return '<a href="mailto:' . $item[$column_name] . '">' . $item[$column_name] . '</a>';
                break;
            case 'phone' :
            case 'ip' :
                return $item[$column_name];
                break;
            case 'date' :
                return date('F jS, Y \a\t h:i:sa', strtotime($item[$column_name]));
                break;
            case 'feedback' :
                $feedbacks = $item[$column_name];
                $ret = '<strong>' . sprintf(__('Total: %d', 'fbsr'), count($feedbacks) - 1) . '</strong><ul class="ul-square">';
                foreach($feedbacks as $k => $f) {
                    if($k === 'opinion')
                        continue;
                    $ret .= '<li>' . $this->feedback[$k]['name'] . '</li>';
                }
                $ret .= '</ul>';
                return $ret;
                break;
            case 'opinion' :
                $feedbacks = $item['feedback'];
                return $feedbacks['opinion'] != '' ? $feedbacks['opinion'] : '&dash;';
                break;
            default :
                return print_r($item, true);
        }
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="feedbacks[]" value="%s" />', $item['id']);
    }

    public function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete'),
        );
        return $actions;
    }

    /**
     *
     * @global wpdb $wpdb
     * @global type $_wp_column_headers
     * @global type $wp_feedback_info
     */
    public function prepare_items() {
        global $wpdb, $_wp_column_headers, $wp_feedback_info;
        $screen = get_current_screen();

        //prepare our query
        $query = "SELECT id, f_name, l_name, email, phone, feedback, ip, date FROM {$wp_feedback_info['feedback_table']}";
        $orderby = !empty($_GET['orderby']) ? $wpdb->escape($_GET['orderby']) : 'date';
        $order = !empty($_GET['order']) ? $wpdb->escape($_GET['order']) : 'desc';
        $where = '';

        if(!empty($_GET['s'])) {
            $search = '%' . $_GET['s'] . '%';

            $where = $wpdb->prepare(" WHERE f_name LIKE %s OR l_name LIKE %s OR email LIKE %s OR phone LIKE %s OR ip LIKE %s", $search, $search, $search, $search, $search);
        }
        $query .= $where;

        //pagination
        $totalitems = $wpdb->get_var("SELECT COUNT(id) FROM {$wp_feedback_info['feedback_table']}{$where}");
        $perpage = $this->get_items_per_page('feedbacks_per_page', 20);
        $totalpages = ceil($totalitems/$perpage);

        $this->set_pagination_args(array(
            'total_items' => $totalitems,
            'total_pages' => $totalpages,
            'per_page' => $perpage,
        ));
        $current_page = $this->get_pagenum();

        //put pagination and order on the query
        $query .= ' ORDER BY ' . $orderby . ' ' . $order . ' LIMIT ' . (($current_page - 1) * $perpage) . ',' . (int) $perpage;
        //print_r($query);

        //register the columns
        $this->_column_headers = $this->get_column_info();

        //fetch the items
        $this->items = $wpdb->get_results($query, ARRAY_A);
        foreach($this->items as $k => $item) {
            //var_dump($item);
            $this->items[$k]['feedback'] = maybe_unserialize($item['feedback']);
            //var_dump($item);
        }
        //var_dump($this->items);
    }

    public function no_items() {
        _e('No Feedback yet! Please be patient.', 'fbsr');
    }
}

/**
 * A stand alone class to show the feedback data
 */

class wp_feedback_view_cb {
    var $feedback;
    var $survey;

    public function __construct() {
        $this->feedback = get_option('wp_feedback_feedback');
        $this->survey = get_option('wp_feedback_survey');
    }

    /**
     * Show the feedback
     * @global wpdb $wpdb
     * @global array $wp_feedback_info
     * @param int $id
     */
    public function show($id) {
        global $wpdb, $wp_feedback_info;
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wp_feedback_info['feedback_table']} WHERE id = %d", $id));
        $survey = maybe_unserialize($result->survey);
        $feedback = maybe_unserialize($result->feedback);
        if(null === $result) {
            _e('Sorry, the entry does not exist anymore', 'fbsr');
        } else {
            ?>
<h3><?php _e('Personal Information', 'fbsr'); ?></h3>
<table class="widefat">
    <thead>
        <tr>
            <th scope="col" colspan="2"><?php printf(__('Feedback on %s', 'fbsr'), date('F jS, Y \a\t h:i:ma', strtotime($result->date))); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="row"><?php _e('First Name', 'fbsr'); ?></th>
            <td><?php echo $result->f_name; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Last Name', 'fbsr'); ?></th>
            <td><?php echo $result->l_name; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Eamil', 'fbsr'); ?></th>
            <td><a href="mailto:<?php echo $result->email; ?>"><?php echo $result->email; ?></a></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Phone Number', 'fbsr'); ?></th>
            <td><?php echo $result->phone; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php _e('IP Address', 'fbsr'); ?></th>
            <td><?php echo $result->ip; ?></td>
        </tr>
    </tbody>
</table>
<hr />
<h3><?php _e('Survey Data', 'fbsr'); ?></h3>
<table class="widefat">
    <thead>
        <tr>
            <th scope="col"><?php _e('Questions', 'fbsr'); ?></th>
            <th scope="col"><?php _e('Answer(s)', 'fbsr'); ?></th>
            <th scope="col"><?php _e('Option(s)', 'fbsr'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($survey as $sk => $sv) : $options = explode("\n", $this->survey[$sk]['options']); ?>
        <tr>
            <th scope="row"><?php echo $this->survey[$sk]['question']; ?></th>
            <td>
                <ul class="ul-<?php echo (is_array($sv) ? 'square' : 'disc'); ?>">
                    <?php if(is_array($sv)) : ?>
                    <?php foreach($sv as $a) : ?>
                    <li><?php echo $options[$a]; ?></li>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <li><?php echo $options[$sv]; ?></li>
                    <?php endif; ?>
                </ul>
            </td>
            <td>
                <ul class="ul-<?php echo ('single' == $this->survey[$sk]['type'] ? 'disc' : 'square'); ?>">
                    <?php foreach($options as $option) : ?>
                    <li><?php echo $option; ?></li>
                    <?php endforeach; ?>
                </ul>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<hr />
<h3><?php _e('Feedback Data', 'fbsr'); ?></h3>
<table class="widefat">
    <thead>
        <tr>
            <th scope="col"><?php _e('Topic', 'fbsr'); ?></th>
            <th scope="col"><?php _e('Feedback', 'fbsr'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($feedback as $fk => $f) : ?>
        <tr>
            <th scope="col"><?php echo ($fk !== 'opinion' ? $this->feedback[$fk]['name'] : __('Opinion', 'fbsr')); ?></th>
            <td>
                <?php echo wpautop($f); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
            <?php
        }
    }
}
