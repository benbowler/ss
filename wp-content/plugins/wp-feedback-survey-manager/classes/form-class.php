<?php
/**
 * The main form class
 * This prints the form and its HTML given the options
 * Also handles the post request to save the user input
 */

class wp_feedback_form {
    var $global;
    var $survey;
    var $feedback;
    var $post;

    public function __construct() {
        $this->global = get_option('wp_feedback_global');
        $this->survey = get_option('wp_feedback_survey');
        $this->feedback = get_option('wp_feedback_feedback');

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->post = $_POST;

            if(get_magic_quotes_gpc())
                array_walk_recursive ($this->post, array($this, 'stripslashes_gpc'));

            array_walk_recursive ($this->post, array($this, 'htmlspecialchar_ify'));
        }
    }

    /**
     *
     * @global wpdb $wpdb
     */
    public function save_post() {
        global $wpdb, $wp_feedback_info;
        //first validate
        $errors = array();
        $return = array();
        $data = $this->post['wp_feedback'];

        if($this->global['enable_pinfo'] == true) :
            if('' == $data['pinfo']['f_name'])
                $errors[] = __('First name is empty', 'fbsr');
            if('' == $data['pinfo']['l_name'])
                $errors[] = __('Last name is empty', 'fbsr');
            if('' == $data['pinfo']['email'])
                $errors[] = __('Email is empty', 'fbsr');
            if(false === $data['pinfo']['email'])
                $errors[] = __('Invalid email address', 'fbsr');
            if('' == $data['pinfo']['phone'])
                $errors[] = __('Phone number is empty', 'fbsr');
            if($data['pinfo']['human'] != $data['pinfo']['h_h'])
                $errors[] = __('Please correctly answer the security questions', 'fbsr');
        endif;

        if($this->global['enable_survey'] == true) :
            $sur_count = 0;
            foreach($this->survey as $s) {
                if($s['enabled'] == true)
                    $sur_count++;
            }
            if($sur_count != count($data['survey'])) {
                $errors[] = __('Please answer all the surveys', 'fbsr');
            }
        endif;

        $feed_count = 0;
        foreach($this->feedback as $f) {
            if($f['enabled'] == true)
                $feed_count++;
        }


        //feedbacks are always optional

        if(empty($errors)) {
            //collect the personal data
            $pinfo = array();
            if($this->global['enable_pinfo'] == true) :
                $pinfo['f_name'] = $data['pinfo']['f_name'];
                $pinfo['l_name'] = $data['pinfo']['l_name'];
                $pinfo['email'] = $data['pinfo']['email'];
                $pinfo['phone'] = $data['pinfo']['phone'];
            endif;
            $pinfo['ip'] = $_SERVER['REMOTE_ADDR'];
            $pinfo['date'] = current_time('mysql');


            //collect the surveys
            $survey = array();
            if($this->global['enable_survey'] == true) :
                foreach($this->survey as $k => $s) {
                    if(true == $s['enabled']) {
                        $survey[$k] = $data['survey'][$k];
                    }
                }
            endif;

            //collect the feedbacks
            $feedback = array();
            if($this->global['enable_feedback'] == true) :
                foreach($this->feedback as $k => $f) {
                    if(true == $f['enabled'] && '' != $data['feedback'][$k]) {
                        $feedback[$k] = $data['feedback'][$k];
                        $this->send_feedback_email($f['email'], $data['feedback'][$k], $f['name'], $pinfo, $data['opinion']);
                    }
                }
            endif;

            if($this->global['enable_pinfo'] == true) :
                $feedback['opinion'] = $data['opinion'];
            endif;

            //insert
            if($wpdb->insert($wp_feedback_info['feedback_table'], array(
                'f_name' => $pinfo['f_name'],
                'l_name' => $pinfo['l_name'],
                'email' => $pinfo['email'],
                'phone' => $pinfo['phone'],
                'survey' => maybe_serialize($survey),
                'feedback' => maybe_serialize($feedback),
                'ip' => $pinfo['ip'],
                'date' => $pinfo['date'],
            ), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'))) {
                $this->send_notification_email($this->global['email'], $pinfo, $wpdb->insert_id);
                $return['msg'] = '<div class="wp_feedback_msg" id="wp_feedback_success"><h4>' . __('Your feedback was successfully submitted', 'fbsr') . '</h4><p>' . htmlspecialchars_decode($this->global['success_message']) . '</p></div>';
                $return['type'] = 'success';
            } else {
                $return['msg'] = '<div class="wp_feedback_msg" id="wp_feedback_error"><h4>' . __('Could not save to the database', 'fbsr') . '</h4><p>' . __('Something terrible has occured. Please contact the administrator.', 'fbsr') . '<ul>';
                $return['type'] = 'fail';
            }

        } else {
            $return['msg'] = '<div class="wp_feedback_msg" id="wp_feedback_error"><h4>' . __('Form Validation Error', 'fbsr') . '</h4><p>' . __('Following errors has occured. Please correct them and resubmit the form.', 'fbsr') . '</p><ul>';
                foreach($errors as $e) {
                    $return['msg'] .= '<li>' . $e . '</li>';
                }
                $return['msg'] .= '</ul></div>';
                $return['type'] = 'fail';
        }
        return $return;
    }

    private function send_notification_email($email, $pinfo, $id) {
        if(trim($email) == '')
            return;

        $content = sprintf(__('
<html><body>
<p>A new feedback has been submitted. You can visit it at</p>
<p><strong>%sadmin.php?page=wp_feedback_view&id=%s</strong>

<h4>User Details</h4>
<ul>
<li><strong>First Name</strong>: %s</li>
<li><strong>Last Name</strong>: %s</li>
<li><strong>Email</strong>: %s</li>
<li><strong>Phone</strong>: %s</li>
</ul>

<p><em>
This is an autogenerated email. Please do not respond to this.<br />
You are receiving this notification because you are one of the email subscribers for the mentioned Feedback.<br />
If you wish to stop receiving emails, then please go to %1$sadmin.php?page=wp_feedback_settings and remove your email from there.<br />
If you can not access the link, then please contact your administrator.
</em></p>

<p>WP Feedback and Survey Manger Plugin <br />
- By Swashata<br />
http://www.intechgrity.com/</p>
</body></html>
', 'fbsr'), get_admin_url(), $id, $pinfo['f_name'], $pinfo['l_name'], $pinfo['email'], $pinfo['phone']);
        $sub = sprintf(__('[%s]New Feedback Notification', 'fbsr'), get_bloginfo('name'));
        $header = 'Content-Type: text/html' . "\r\n";
        wp_mail($email, $sub, $content, $header);
    }

    private function send_feedback_email($email, $content, $sub, $pinfo, $op) {
        if(trim($email) == '')
            return;
        $sub = sprintf(__('[%s] New feedback on the topic: %s', 'fbsr'), get_bloginfo('name'), $sub);

        $content = sprintf(__('
<html><body>

<h2>User Details</h2>
<ul>
<li><strong>First Name</strong>: %s</li>
<li><strong>Last Name</strong>: %s</li>
<li><strong>Email</strong>: %s</li>
<li><strong>Phone</strong>: %s</li>
</ul>

<h2>Feedback Details</h2>
<p><strong>Feedback Topic:</strong> %s</p>
------------------------------------------------------------------------------
%s
------------------------------------------------------------------------------
<p><strong>General Opinion:</strong></p>
------------------------------------------------------------------------------
%s
------------------------------------------------------------------------------

<br /><br />

------------------------------------------------------------------------------
<p><em>
This is an autogenerated email. Please do not respond to this.<br />
You are receiving this notification because you are one of the email subscribers for the mentioned Feedback.<br />
If you wish to stop receiving emails, then please go to %sadmin.php?page=wp_feedback_settings and remove your email from there.<br />
If you can not access the link, then please contact your administrator.
</em></p>

<p>WP Feedback and Survey Manger Plugin <br />
- By Swashata<br />
http://www.intechgrity.com/</p>
</body></html>
', 'fbsr'), $pinfo['f_name'], $pinfo['l_name'], $pinfo['email'], $pinfo['phone'], $sub, wpautop($content), wpautop($op), get_admin_url());

        $header = 'Content-Type: text/html' . "\r\n";
        wp_mail($email, $sub, $content, $header);
    }

    public function print_form() {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        ?>
<style type="text/css">
    @import url('<?php echo plugins_url('/static/front/css/smoothness/jquery-ui-1.8.22.custom.css', wp_feedback_loader::$abs_file); ?>');
    @import url('<?php echo plugins_url('/static/front/css/validationEngine.jquery.css', wp_feedback_loader::$abs_file); ?>');
    @import url('<?php echo plugins_url('/static/front/css/form.css', wp_feedback_loader::$abs_file); ?>');
</style>
<div id="wp_feedback">
    <form action="" method="post" id="feedback_form">
        <input type="hidden" name="action" value="wp_feedback_submit" />
        <div id="wp_feedback_tabs_wrap">
            <ul id="wp_feedback_tabs">
                <?php if(true == $this->global['enable_survey']) : ?>
                <li class="wp_feedback_survey"><a href="#wp_feedback_tab_survey"><?php echo $this->global['survey_title']; ?><span><?php echo $this->global['survey_subtitle']; ?></span></a></li>
                <?php endif; ?>
                <?php if(true == $this->global['enable_feedback']) : ?>
                <li class="wp_feedback_feedback"><a href="#wp_feedback_tab_feedback"><?php echo $this->global['feedback_title']; ?><span><?php echo $this->global['feedback_subtitle']; ?></span></a></li>
                <?php endif; ?>
                <?php if(true == $this->global['enable_pinfo']) : ?>
                <li class="wp_feedback_pinfo"><a href="#wp_feedback_tab_pinfo"><?php echo $this->global['pinfo_title']; ?><span><?php echo $this->global['pinfo_subtitle']; ?></span></a></li>
                <?php endif; ?>
            </ul>

            <?php if(true == $this->global['enable_survey']) : ?>
            <div id="wp_feedback_tab_survey">
                <?php $c = 0; $e = 0; ?>
                <?php foreach($this->survey as $survey) : ?>
                <?php if(true == $survey['enabled']) : $e++; ?>
                <div class="wp_feedback_survey_wrap <?php echo $e; ?>">
                    <h4><?php echo $survey['question']; ?></h4>
                    <?php if($survey['type'] == 'single') : ?>
                    <?php $this->print_radioboxes('wp_feedback[survey][' . $c . ']', $survey['options']); ?>
                    <?php else : ?>
                    <?php $this->print_checkboxes('wp_feedback[survey][' . $c . '][]', $survey['options']); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php $c++; endforeach; ?>
                <div class="clear"></div>
            </div>
            <?php endif; ?>

            <?php if(true == $this->global['enable_feedback']) : ?>
            <div id="wp_feedback_tab_feedback">
                <?php $c = 0; $e = 0; ?>
                <?php foreach($this->feedback as $feedback) : ?>
                <?php if(true == $feedback['enabled']) : $e++; ?>
                <div class="wp_feedback_feedback_wrap_o <?php echo ($e % 2 == 0 ? 'even' : 'odd'); ?>">
                    <div class="wp_feedback_feedback_wrap">
                        <?php $this->print_feedback('wp_feedback[feedback][' . $c . ']', $feedback['description'], $feedback['name']); ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php $c++; endforeach; ?>
                <div class="clear"></div>
            </div>
            <?php endif; ?>

            <?php if(true == $this->global['enable_pinfo']) : ?>
            <div id="wp_feedback_tab_pinfo">
                <label class="float" for="wp_feedback_p_info_f_name">
                    <?php _e('First Name:', 'fbsr'); ?><br />
                    <?php $this->print_textbox('wp_feedback[pinfo][f_name]'); ?>
                </label>
                <label class="float" for="wp_feedback_p_info_l_name">
                    <?php _e('Last Name:', 'fbsr'); ?><br />
                    <?php $this->print_textbox('wp_feedback[pinfo][l_name]'); ?>
                </label>
                <div class="clear"></div>
                <label class="float" for="wp_feedback_p_info_email">
                    <?php _e('Email:', 'fbsr'); ?><br />
                    <?php $this->print_textbox('wp_feedback[pinfo][email]', 'validate[required,custom[email]] text'); ?>
                </label>
                <label class="float" for="wp_feedback_p_info_phone">
                    <?php _e('Phone:', 'fbsr'); ?><br />
                    <?php $this->print_textbox('wp_feedback[pinfo][phone]', 'validate[required,custom[phone]] text'); ?>
                </label>
                <div class="clear"></div>
                <?php if(true == $this->global['enable_opinion']) : ?>
                <div class="text_wrap">
                    <label for="wp_feedback_opinion"><?php _e('Other Opinions:', 'fbsr'); ?></label>
                    <textarea name="wp_feedback[opinion]" id="wp_feedback_opinion" cols="5"></textarea>
                </div>
                <?php endif; ?>
                <label class="float" for="wp_feedback_p_info_human">
                    <?php echo __('Security Question: ') . $num1 . ' + ' . $num2 . ' = ?'; ?><br />
                    <?php $this->print_textbox('wp_feedback[pinfo][human]', 'validate[required,funcCall[wp_feedback_security]]'); ?>
                    <input type="hidden" name="wp_feedback[pinfo][h_h]" id="wp_feedback_p_info_h_h" value="<?php echo esc_attr($num1 + $num2); ?>" />
                </label>
                <label class="float" for="wp_feedback_p_info_date">
                    <?php _e('Submission Date:', 'fbsr'); ?><br />
                    <input type="text" class="text" value="<?php echo date('F jS, Y', current_time('timestamp')); ?>" readonly="readonly" />
                </label>
                <div class="clear"></div>
                <?php if(!empty($this->global['terms_page'])) : $link = get_permalink($this->global['terms_page']); ?>
                <div class="wp_feedback_terms">
                    <label for="wp_feedback_pinfo_terms">
                        <input type="checkbox" id="wp_feedback_pinfo_terms" name="wp_feedback[pinfo][terms]" class="validate[required]" value="1" />
                        <?php printf(__('By submitting this form, you hereby accept our <a href="%s" target="_blank">Terms & Conditions</a>. Your IP address <strong>%s</strong> will be stored in our database.', 'fbsr'), $link, $_SERVER['REMOTE_ADDR']); ?>
                    </label>
                </div>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="wp_feedback_navigation">
            <button class="prev_button"><?php _e('&laquo; Previous', 'fbsr'); ?></button>
            <button type="submit" class="sub_button"><?php _e('Submit', 'fbsr'); ?></button>
            <button class="next_button"><?php _e('Next &raquo;', 'fbsr'); ?></button>
        </div>
    </form>
    <div id="wp_feedback_ajax" style="display: none">
        <h4><?php _e('Please wait while we are submitting your form', 'fbsr'); ?></h4>
        <p><?php _e('Submitting', 'fbsr'); ?></p>
    </div>
    <!--
    <div id="wp_feedback_success">
        <h4><?php _e('Your feedback was successfully submitted', 'fbsr'); ?></h4>
        <p><?php echo $this->global['success_message']; ?></p>
    </div>
    <div id="wp_feedback_error">
        <h4><?php _e('Some error has occured', 'fbsr'); ?></h4>
        <p><?php _e('Please correct the data and submit again', 'fbsr'); ?></p>
    </div>
    -->
</div>
<script type="text/javascript">
function wp_feedback_security(field, rules, i, options) {
    if(field.val() != jQuery('#wp_feedback_p_info_h_h').val()) {
        return '<?php _e('* The answer is incorrect. It should be ', 'fbsr'); ?>' + jQuery('#wp_feedback_p_info_h_h').val();
    }
}
</script>
        <?php
    }

    private function print_textbox($name, $class = 'validate[required] text') {
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
<input type="text" class="<?php echo $class; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="" />
        <?php
    }

    private function print_feedback($name, $description, $f_name) {
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
<div class="toggle_f">
    <h4><label for="<?php echo $id; ?>"><input type="checkbox" id="<?php echo $id; ?>" /> <?php echo $f_name; ?></label></h4>
    <div class="toggle_d">
        <?php echo wpautop($description); ?>
        <textarea name="<?php echo $name; ?>"></textarea>
    </div>
</div>
        <?php
    }

    private function print_radioboxes($name, $options) {
        $option = $this->split_options($options);
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
        <?php foreach($option as $k => $v) : ?>
<label class="float" for="<?php echo $id . '_' . $k; ?>">
<input type="radio" class="validate[required] radio" name="<?php echo $name; ?>" id="<?php echo $id . '_' . $k; ?>" value="<?php echo $k; ?>" />
<?php echo $v; ?>
</label>
        <?php endforeach; ?>
<div class="clear"></div>
        <?php
    }

    private function print_checkboxes($name, $options) {
        $option = $this->split_options($options);
        $id = str_replace(array('[', ']'), array('_', ''), $name);
        ?>
        <?php foreach($option as $k => $v) : ?>
<label class="float" for="<?php echo $id . '_' . $k; ?>">
<input type="checkbox" class="validate[minCheckbox[1]] checkbox" name="<?php echo $name; ?>" id="<?php echo $id . '_' . $k; ?>" value="<?php echo $k; ?>" />
<?php echo $v; ?>
</label>
        <?php endforeach; ?>
<div class="clear"></div>
        <?php
    }

    private function split_options($option) {
        $option = explode("\r\n", $option);
        array_walk($option, array(&$this, 'clean_options'));
        foreach($option as $k => $v) {
            if('' == $v)
                unset($option[$k]);
        }
        return $option;
    }

    protected function clean_options(&$value) {
        $value = htmlspecialchars(trim(strip_tags(htmlspecialchars_decode($value))));
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
        $value = htmlspecialchars(trim(strip_tags($value)));
    }
}

class wp_feedback_form_shortcode {
    public function feedback_cb() {
        $form = new wp_feedback_form();
        $show_form = true;

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $get_save_post = $form->save_post ();
            if($get_save_post['type'] == 'fail') {
                $show_form = true;
            } else {
                $show_form = false;
            }
            echo $get_save_post['msg'];
        }

        if($show_form)
            $form->print_form ();

        $this->feedback_enqueue();
    }

    public function feedback_enqueue() {
        //wp_enqueue_script('wp_feedback_shortcode', plugins_url('/static/front/js/form.js', wp_feedback_loader::$abs_file), array('jquery'), wp_feedback_loader::$version, true);
        wp_enqueue_script('wp_feedback_ve', plugins_url('/static/front/js/jquery.validationEngine-en.js', wp_feedback_loader::$abs_file), array('jquery'), wp_feedback_loader::$version, true);
        wp_enqueue_script('wp_feedback_v', plugins_url('/static/front/js/jquery.validationEngine.js', wp_feedback_loader::$abs_file), array('jquery'), wp_feedback_loader::$version, true);
        //wp_enqueue_script('wp_feedback_jquis', plugins_url('/static/front/js/jquery-ui-1.8.22.custom.min.js', wp_feedback_loader::$abs_file), array('jquery'), wp_feedback_loader::$version, true);
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-button');
        wp_enqueue_script('wp_feedback_form', plugins_url('/static/front/js/form.js', wp_feedback_loader::$abs_file), array('jquery'), wp_feedback_loader::$version, true);
        wp_localize_script('wp_feedback_form', 'wpFBObj', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }
}

class wp_feedback_trend {
    public $survey;
    public $global;

    public function __construct() {
        $this->global = get_option('wp_feedback_global');
        $this->survey = get_option('wp_feedback_survey');
    }

    public function print_trend() {
        $data = $this->get_data();

        $info = array();
        ?>
<style type="text/css">
@import url('<?php echo plugins_url('/static/front/css/form.css', wp_feedback_loader::$abs_file); ?>');
</style>
<noscript><?php _e('You need to enabled JavaScript to view this page', 'fbsr'); ?></noscript>
<?php $i = 0; foreach($this->survey as $sk => $survey) : ?>
<?php if($survey['enabled'] == true) : $i++; ?>
<?php $info[$sk] = explode("\r\n", $survey['options']); ?>
<div id="wp_feedback_trend_<?php echo $sk; ?>" class="wp_feedback_trend <?php echo ($i % 2 == 0 ? 'even' : 'odd'); ?>">
    <h4><?php echo $survey['question']; ?></h4>
    <div id="wp_feedback_trend_<?php echo $sk; ?>_pie"><img src="<?php echo plugins_url('/static/admin/images/ajax.gif', wp_feedback_loader::$abs_file); ?>" /></div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<?php if($i == 0) : //No survey acticvated ?>
<div class="p-message red">
    <p>
        <?php _e('No survey has been activated yet! Please go to the <strong>Settings</strong> page and enable and setup some survey question to see data here.', 'fbsr'); ?>
    </p>
</div>
<?php endif; ?>
<div class="clear" style="clear: both"></div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('visualization', '1.0', {'packages':['corechart', 'motionchart']});
</script>
<script type="text/javascript">
    var data = <?php echo json_encode($data, JSON_FORCE_OBJECT); ?>;
    var info = <?php echo json_encode($info, JSON_FORCE_OBJECT); ?>;

    function onLoadPie() {
        for(var question in info) {
            var gdata = new Array();
            gdata[0] = new Array('<?php _e('Options', 'fbsr'); ?>', '<?php _e('Count', 'fbsr'); ?>');
            for(var option in info[question]) {
                gdata[gdata.length] = new Array(info[question][option], data[question][option]);
            }

            document.getElementById('wp_feedback_trend_' + question + '_pie').innerHTML = '';

            new google.visualization.PieChart(document.getElementById('wp_feedback_trend_' + question + '_pie')).draw(google.visualization.arrayToDataTable(gdata), {
                title : '<?php _e('Answers', 'fbsr'); ?>'
            });
        }
    }
    google.setOnLoadCallback(onLoadPie);
</script>
<p style="text-align: right">
    <small><em><?php printf(__('Powered by: %s', 'fbsr'), '<a href="http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/">WP Feedback & Survey Manager Plugin - WordPress - InTechgrity</a>') ?></em></small>
</p>
        <?php
    }

    /**
     * Get the data from the database
     * It does not check whether the survey question is enabled. It simply returns all the values from the latest 100 db rows
     * @access Private
     * @global wpdb $wpdb
     * @global array $wp_feedback_info
     * @return array Associative array of enabled survey questions' results where the key corresponds to the question number
     */
    private function get_data() {
        $data = get_transient('wp_feedback_data_t');

        if(false !== $data) {
            return $data;
        }

        $data = array();

        global $wpdb, $wp_feedback_info;
        $results = $wpdb->get_col("SELECT survey FROM {$wp_feedback_info['feedback_table']} ORDER BY `date` DESC LIMIT 0,100");
        if(null == $results) {
            $data['type'] = 'Empty';
        } else {
            $data['type'] = 'NonEmpty';
            foreach($results as $result) {
                $result = maybe_unserialize($result);
                foreach($result as $k => $r) {
                    if(is_array($r)) {
                        foreach($r as $l) {
                            $data[$k][$l]++;
                        }
                    } else {
                        $data[$k][$r]++;
                    }
                }
            }
        }

        set_transient('wp_feedback_data_t', $data, 1*60*60);
        return $data;
    }
}
