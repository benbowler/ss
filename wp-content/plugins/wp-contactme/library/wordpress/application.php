<?php
if (! class_exists ( 'wv48fv_application' ))
{
	require_once dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'base/application.php';
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	class wv48fv_application extends bv48fv_application {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		/*********************************************************************
		 * Settings Getter, Setters & unsetters
		 *********************************************************************/
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &user($user_id = null) {
			return $this->cache ( 'wv48fv_user', $user_id );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function siteuri($array = false) {
			$siteurl = strtolower ( get_option ( 'siteurl' ) );
			$siteurl = explode ( '//', $siteurl );
			$protocol = $siteurl [0] . '//';
			$siteurl = $siteurl [1];
			$siteurl = explode ( '?', $siteurl );
			$siteurl = urldecode ( trim ( $siteurl [0], '/' ) );
			$return = array ('protocol' => $protocol, 'uri' => $siteurl );
			if (! $array) {
				$return = implode ( '', $return );
			}
			return $return;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function pluginuri() {
			$return = substr ( $this->directory, strlen ( ABSPATH ) );
			$return = str_replace ( '\\', '/', $return );
			$return = $this->siteuri () . '/' . $return;
			return $return;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function __construct($filename) {
			parent::__construct ( $filename );
			register_activation_hook($filename,array($this,'activate'));
			register_deactivation_hook($filename,array($this,'deactivate'));
			$this->legacy ()->update();
			add_action('init',array($this,'init'));
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function activate()
		{
			do_action($this->application()->slug.'_activate');
		}
		public function deactivate()
		{
			do_action($this->application()->slug.'_deschedule');
			do_action($this->application()->slug.'_deactivate');
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function init()
		{
			do_action('sandbox_register',$this->application());
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function relative_path($uri = null) {
			global $current_blog;
			if (null === $uri) {
				$uri = $_SERVER ['REQUEST_URI'];
			}
			//$uri = substr ( $uri , strlen ( $current_blog->path ) );
			$uri = substr ( $uri, strlen ( get_option ( 'site_url' ) ) );
			
			$uri = explode ( '?', $uri );
			$uri = $uri [0];
			$uri = rtrim ( $uri, '/' );
			$uri = '/' . rtrim ( $uri, '/' );
			return $uri;
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &mu() {
			return $this->cache ( 'wv48fv_mu' );
		}		
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &posts() {
			return $this->cache ( 'wv48fv_posts' );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &blogs() {
			return $this->cache ( 'wv48fv_blogs' );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &legacy() {
			return $this->cache ( $this->classes->legacy );
		}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
		public function &comments() {
			return $this->cache ( 'wv48fv_comments' );
		}	
	}
}