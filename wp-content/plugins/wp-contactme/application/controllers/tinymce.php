<?php
class contactme_tinymce extends wv48fv_action {
	function mce_buttonsWPfilter($buttons) {
		array_push ( $buttons,  $this->application()->slug );
		return $buttons;
	}
	function mce_external_pluginsWPfilter($plugin_array) {
		$plugin_array ['v48fv_tinymce'] = $this->application ()->pluginuri () . '/library/base/public/js/tinymce_register.js';
		return $plugin_array;
	}
	public function v48fv_dataWPfilter($data)
	{
		$slug = $this->application()->slug;
		$data['tinymce']['cmds'][$slug]['file']= $this->control_url ( "{$this->application()->slug}-popup" );
		$data['tinymce']['cmds'][$slug]['width']= 200;
		$data['tinymce']['cmds'][$slug]['height']= 50;
		$data['tinymce']['cmds'][$slug]['inline']= 1;
		$data['tinymce']['cmds'][$slug]['always']= true;
		$data['tinymce']['buttons'][$slug]['image'] = $this->application ()->pluginuri () . '/library/base/public/images/16x16_forms.png' ;
		$data['tinymce']['buttons'][$slug]['title'] = $this->application()->name ;
		$data['tinymce']['buttons'][$slug]['cmd'] = $slug; ;
		return $data;
	}
	public function popupWPpageMeta($return) {
		$return ['slug'] = $this->application()->slug.'-popup';
		return $return;
	}
	public function popupWPpage() {
		echo $this->render_script ( 'popup.phtml' );
	}
}