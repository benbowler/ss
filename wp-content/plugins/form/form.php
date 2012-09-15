<?php
/*
 Plugin Name: Form Builder
 Plugin URI: http://www.zingiri.com
 Description: Create amazing web forms with ease.
 Author: Zingiri
 Version: 1.1.7
 Author URI: http://www.zingiri.com/
 */

define("FORM_VERSION","1.1.7");

// Pre-2.6 compatibility for wp-content folder location
if (!defined("WP_CONTENT_URL")) {
	define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
}
if (!defined("WP_CONTENT_DIR")) {
	define("WP_CONTENT_DIR", ABSPATH . "wp-content");
}

if (!defined("FORM_PLUGIN")) {
	$form_plugin=str_replace(realpath(dirname(__FILE__).'/..'),"",dirname(__FILE__));
	$form_plugin=substr($form_plugin,1);
	define("FORM_PLUGIN", $form_plugin);
}

if (!defined("BLOGUPLOADDIR")) {
	$upload=wp_upload_dir();
	define("BLOGUPLOADDIR",$upload['path']);
}

define("FORM_URL", WP_CONTENT_URL . "/plugins/".FORM_PLUGIN."/");

$form_version=get_option("form_version");
add_action("init","form_init");
if (!is_admin() && isset($_GET['ajax']) && ($_GET['ajax'] == 1)) {
	add_action("init","form_ajax");
} else {
	add_action('admin_head','form_admin_header');
	add_action('wp_head','form_header');
}
add_action('admin_notices','form_admin_notices');
add_filter('the_content', 'form_content', 10, 3);

register_activation_hook(__FILE__,'form_activate');
register_deactivation_hook(__FILE__,'form_deactivate');
register_uninstall_hook(__FILE__,'form_uninstall');

require_once(dirname(__FILE__) . '/includes/shared.inc.php');
require_once(dirname(__FILE__) . '/includes/http.class.php');
require_once(dirname(__FILE__) . '/controlpanel.php');

function form_admin_notices() {
	global $form;
	$errors=array();
	$warnings=array();
	$files=array();
	$dirs=array();

	if (isset($form['output']['warnings']) && is_array($form['output']['warnings']) && (count($form['output']['warnings']) > 0)) {
		$warnings=$form['output']['warnings'];
	}
	if (isset($form['output']['errors']) && is_array($form['output']['errors']) && (count($form['output']['errors']) > 0)) {
		$errors=$form['output']['errors'];
	}
	$upload=wp_upload_dir();
	if (session_save_path() && !is_writable(session_save_path())) $errors[]='PHP sessions are not properly configured on your server, the sessions save path '.session_save_path().' is not writable.';
	if ($upload['error']) $errors[]=$upload['error'];
	if (get_option('form_debug')) $warnings[]="Debug is active, once you finished debugging, it's recommended to turn this off";
	if (phpversion() < '5') $warnings[]="You are running PHP version ".phpversion().". We recommend you upgrade to PHP 5.3 or higher.";
	if (ini_get("zend.ze1_compatibility_mode")) $warnings[]="You are running PHP in PHP 4 compatibility mode. We recommend you turn this option off.";
	if (!function_exists('curl_init')) $errors[]="You need to have cURL installed. Contact your hosting provider to do so.";

	if (count($warnings) > 0) {
		echo "<div id='zing-warning' style='background-color:greenyellow' class='updated fade'><p><strong>";
		foreach ($warnings as $message) echo 'Form Builder: '.$message.'<br />';
		echo "</strong> "."</p></div>";
	}
	if (count($errors) > 0) {
		echo "<div id='zing-warning' style='background-color:pink' class='updated fade'><p><strong>";
		foreach ($errors as $message) echo 'Form Builder:'.$message.'<br />';
		echo "</strong> "."</p></div>";
	}

	return array('errors'=> $errors, 'warnings' => $warnings);
}

function form_activate() {
	if (!get_option('form_key')) update_option('form_key',md5(time().sprintf(mt_rand(),'%10d')));
	update_option("form_version",FORM_VERSION);
}

function form_deactivate() {
	form_output('deactivate');
}

function form_uninstall() {
	form_output('uninstall');
	
	$form_options=form_options();

	delete_option('form_log');
	foreach ($form_options as $value) {
		delete_option( $value['id'] );
	}
	delete_option('form_key');
	delete_option("form_log");
	delete_option("form_ftp_user"); //legacy
	delete_option("form_ftp_password"); //legacy
	delete_option("form_version");
	delete_option('form-support-us');
}

function form_content($content) {
	global $form;

	if (preg_match_all('/\[form(.*)\]/',$content,$matches)) {
		$pg=isset($_REQUEST['zf']) ? $_REQUEST['zf'] : 'form';
		$postVars=array();
		if (!isset($_POST['formid']) && !isset($_POST['form'])) $postVars['formid']=$matches[1][0];
		if (!isset($_POST['action'])) $postVars['action']='add';
		form_output($pg,$postVars);
		$output='<div id="form">';
		$output.=$form['output']['body'];
		$output.='</div>';
		$content=str_replace($matches[0][0],$output,$content);
	}
	return $content;
}

function form_output($form_to_include='',$postVars=array()) {
	global $post,$form;
	global $wpdb;
	global $wordpressPageName;
	global $form_loaded;

	$ajax=isset($_REQUEST['ajax']) ? $_REQUEST['ajax'] : false;

	$http=form_http($form_to_include);
	form_log('Notification','Call: '.$http);
	//echo '<br />'.$http.'<br />';
	$news = new zHttpRequest($http,'form');
	$news->noErrors=true;
	$news->post=array_merge($news->post,$postVars);

	if (!$news->curlInstalled()) {
		form_log('Error','CURL not installed');
		return "cURL not installed";
	} elseif (!$news->live()) {
		form_log('Error','A HTTP Error occured');
		return "A HTTP Error occured";
	} else {
		if ($ajax==1) {
			ob_end_clean();
			$buffer=$news->DownloadToString();
			if ($news->error && is_admin()) echo $news->errorMessage;
			elseif ($news->error) echo 'The service is currently unavailable';
			else {
				$form['output']=json_decode($buffer,true);
				if (!$form['output']) {
					$form['output']['body']=$buffer;
					$form['output']['head']='';
				}
				//$body=form_parser_ajax1($form['output']['body']);
				echo $form['output']['body'];
			}
			die();
		} elseif ($ajax==2) {
			ob_end_clean();
			$output=$news->DownloadToString();
			if ($news->error && is_admin()) echo $news->errorMessage;
			elseif ($news->error) echo 'The service is currently unavailable';
			else {
				$body=$news->body;
				$body=form_parser_ajax2($body);
				header('HTTP/1.1 200 OK');
				echo $body;
			}
			die();
		} else {
			$buffer=$news->DownloadToString();
			if ($news->error && is_admin()) echo $news->errorMessage;
			elseif ($news->error) echo 'The service is currently unavailable';
			else {
				$form['output']=json_decode($buffer,true);
				if (!$form['output']) {
					$form['output']['body']=$buffer;
					$form['output']['head']='';
				} else {
					if (isset($form['output']['http_referer'])) $_SESSION['form']['http_referer']=$form['output']['http_referer'];
				}
				$form['output']['body']=form_parser($form['output']['body']);
			}
		}
	}
}


if (!class_exists('simple_html_dom')) require(dirname(__FILE__).'/includes/simple_html_dom.php');
function form_parser($buffer) {
	global $wp_version;
	if (is_admin() && ($wp_version >= '3.3')) {
		$html = new simple_html_dom();
		$html->load($buffer);
		if ($textareas=$html->find('textarea[class=theEditor]')) {
			foreach ($textareas as $textarea) {
				ob_start();
				wp_editor($textarea->innertext,$textarea->id);
				$editor=ob_get_clean();
				$textarea->outertext=$editor;
			}
		}
		return $html->__toString();
	}
	return $buffer;
}

function form_header() {
	echo '<script type="text/javascript">';
	echo "var formPageurl='".form_home()."';";
	echo "var aphpsAjaxURL='".'?zf=ajax&ajax=1&form='."';";
	echo "var aphpsURL='".form_url(false).'aphps/fwkfor/'."';";
	echo "var wsCms='gn';";
	echo '</script>';

	echo '<link rel="stylesheet" type="text/css" href="' . FORM_URL . 'css/client.css" media="screen" />';
	echo '<link rel="stylesheet" type="text/css" href="' . FORM_URL . 'css/integrated_view.css" media="screen" />';
}

function form_admin_header() {
	global $wp_version,$form;

	if (isset($_REQUEST['page']) && ($_REQUEST['page']=='form')) {
		echo '<script type="text/javascript">';
		echo "var formPageurl='admin.php?page=form&';";
		echo "var aphpsAjaxURL='".'?zf=ajax&ajax=1&form='."';";
		echo "var aphpsURL='".form_url(false).'aphps/fwkfor/'."';";
		echo "var wsCms='gn';";
		echo '</script>';
		echo '<link rel="stylesheet" type="text/css" href="' . FORM_URL . 'css/admin.css" media="screen" />';
		echo '<link rel="stylesheet" type="text/css" href="' . FORM_URL . 'css/integrated_view.css" media="screen" />';
		if (isset($form['output']['head']) && $form['output']['head']) {
			echo $form['output']['head'];
		}
		if ($wp_version < '3.3') wp_tiny_mce( false, array( 'editor_selector' => 'theEditor' ) );
	}

}
function form_http($page="index") {
	global $current_user;

	$vars="";
	$http=form_url().'?pg='.$page;
	$and="&";
	if (count($_GET) > 0) {
		foreach ($_GET as $n => $v) {
			if (!in_array($n,array('page')))
			{
				$vars.= $and.$n.'='.cc_urlencode($v);
				$and="&";
			}
		}
	}

	$and="&";

	$wp=array();
	if (is_user_logged_in()) {
		$wp['login']=$current_user->data->user_login;
		$wp['email']=$current_user->data->user_email;
		$wp['first_name']=isset($current_user->data->first_name) ? $current_user->data->first_name: $current_user->data->display_name;
		$wp['last_name']=isset($current_user->data->last_name) ? $current_user->data->last_name : $current_user->data->display_name;
		$wp['roles']=$current_user->roles;
	}
	$wp['lic']=get_option('form_lic');
	$wp['siteurl']=home_url();
	$wp['sitename']=get_bloginfo('name');
	$wp['pluginurl']=FORM_URL;
	if (is_admin()) $wp['pageurl']='admin.php?page=form&zf=form_edit&';
	else $wp['pageurl']=form_home();

	$wp['time_format']=get_option('time_format');
	$wp['admin_email']=get_option('admin_email');
	$wp['key']=get_option('form_key');
	$wp['lang']=get_option('form_lang'); //get_bloginfo('language');
	$vars.=$and.'wp='.urlencode(base64_encode(json_encode($wp)));

	if (isset($_SESSION['form']['http_referer'])) $vars.='&http_referer='.cc_urlencode($_SESSION['form']['http_referer']);

	if ($vars) $http.=$vars;

	return $http;
}

function form_home() {
	global $post,$page_id;

	$pageID = $page_id;

	if (get_option('permalink_structure')){
		$homePage = get_option('home');
		$wordpressPageName = get_permalink($pageID);
		$wordpressPageName = str_replace($homePage,"",$wordpressPageName);
		$home=$homePage.$wordpressPageName;
		if (substr($home,-1) != '/') $home.='/';
		$home.='?';
	}else{
		$home=get_option('home').'/?page_id='.$pageID.'&';
	}

	return $home;
}

function form_ajax() {
	global $form;
	if (is_admin() && isset($_GET['zf'])) {
		$pg=$_GET['zf'];
		form_output($pg);
	}
}

function form_init()
{
	global $wp_version;

	ob_start();
	session_start();
	wp_enqueue_script('jquery');
	if (is_admin() && isset($_GET['zf'])) {
		$pg=$_GET['zf'];
		form_output($pg);
		if ($pg=='form_edit') {
			wp_enqueue_script(array('jquery-ui-core','jquery-ui-datepicker','jquery-ui-sortable'));
			wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/flick/jquery-ui.css');
		}
		if (isset($_REQUEST['page']) && ($_REQUEST['page']=='form')) {
			if ($wp_version < '3.3') {
				wp_enqueue_script(array('editor', 'thickbox', 'media-upload'));
				wp_enqueue_style('thickbox');
			}
		}
	} else {
		wp_enqueue_script(array('jquery-ui-core','jquery-ui-datepicker'));
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/flick/jquery-ui.css');
	}
}

function form_log($type=0,$msg='',$filename="",$linenum=0) {
	if (get_option('form_debug')) {
		if (is_array($msg)) $msg=print_r($msg,true);
		$v=get_option('form_log');
		if (!is_array($v)) $v=array();
		array_unshift($v,array(time(),$type,$msg));
		update_option('form_log',$v);
	}
}

function form_url($endpoint=true) {
	$url='http://form.clientcentral.info/'; //URL end point for web services stored on Zingiri servers
	if ($endpoint) $url.='api.php';
	return $url;
}



