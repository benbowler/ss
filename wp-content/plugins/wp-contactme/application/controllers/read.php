<?php
/*****************************************************************************************
* filters on data reads
*****************************************************************************************/
class contactme_read extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function filter_options(&$options, $selected = null) {
		if (! is_array ( $options )) {
			return;
		}
		foreach ( $options as $key => &$option ) {
			if($this->dodebug())
			{
				$options['debug']=array();
			}
			if (isset ( $options ['disabled'] ) && $options ['disabled'] == 1) {
				unset ( $options [$key] );
			} else {
				if (! isset ( $option ['value'] )) {
					$option ['value'] = $key;
				}
				if (! isset ( $option ['text'] )) {
					$option ['text'] = $key;
				}
				if ($option ['value'] == $selected) {
					$option ['selected'] = 'selected';
				} else {
					$option ['selected'] = '';
				}
			}
		}
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function read_general($return,$form) {
		switch($return['general']['status'])
		{
			case 'open':
			case 'pending':
				if($return['data_collection']['post']['do']==='checked')
				{
					$post = $this->posts()->get_by_title($return['data_collection']['post']['name']);
					if($post!==false)
					{
						switch($post->post_status)
						{
							case 'publish':
								$return['general']['status']='closed';
								break;
							case 'pending':
								$return['general']['status']='pending';
								break;
						}
					}
				}
				break;
			case 'closed':
				break;
		}
		$return['general']['screen'] = $return['general']['status'];
		if(is_feed())
		{
			$return['general']['screen'] = 'rss';
		}
		elseif($return['general']['privacy']==='private' && !is_user_logged_in())
		{
				$return['general']['screen']='private';
		} elseif($this->request()->is_post($form))
		{
			$return['general']['screen']='results';
		}
		$this->filter_options ( $return ['general']['options'] ['config'], $return ['general']['config'] );
		$this->filter_options ( $return ['general']['options'] ['scale'], $return ['general']['scale'] );
		$this->filter_options ( $return ['general']['options'] ['privacy'], $return ['general']['privacy'] );
		$this->filter_options ( $return ['general']['options'] ['status'], $return ['general']['status'] );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function contactme_readWPfilter($return,$form) {
		$return ['data_collection']['table'] ['name'] = $this->table ( "{$this->application()->slug}_{$form}" )->name ();
		if ($return ['data_collection']['post'] ['name'] == "") {
			$return ['data_collection']['post'] ['name'] = $form;
		}
		$return = $this->read_general($return,$form);
		$return = $this->read_field_definitions($return,$form);
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function value($field_definition,$form) {
		$field = $field_definition ['field'];
		$return = '';
		if ($this->request()->is_post ($form)) {
			if (isset ( $_POST [$field] )) {
				$return = $_POST [$field];
			}
		} else {
			switch ($field_definition ['type']) {
				case 'email' :
					$return = $this->user ()->user_email;
					break;
				case 'name' :
					$return = $this->user ()->display_name;
					break;
				case 'submit' :
					$return = $field_definition ['question'];
					break;
			}
		}
		if (is_array ( $return )) {
			foreach ( $return as &$value ) {
				$value = trim(stripslashes ( $value ));
			}
			unset ( $value );
		} else {
			$return = trim(stripslashes ( $return ));
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_recapture_cache = null;
	private function error($field_definition, $field_definitions, $form) {
		$field = $field_definition ['field'];
		$return = '0';
		if (! is_admin ()) {
			if ($this->request()->is_post ($form)) {
				/**
				* recaptcha has to use its own field and is manadtory anyway for skip the 
				* check for recaptcha
				**/
				if(!in_array($field_definition['type'],array('recaptcha','number_captcha','cookie')))
				{
					if ($field_definition ['mandatory'] != '') {
						if (! isset ( $_POST [$field] )) {
							$return = '1';
						} else {
							if(is_array($_POST[$field]))
							{
								$return = 1;
								foreach($_POST[$field] as $post)
								{
									if(!empty($post))
									{
										$return = 0;
										break;
									}
								}
							}
							else
							{
								if (empty($_POST [$field])) {
									$return = 1;
								}
							}
						}
					}
				}
				if ($return == '0') {
					switch ($field_definition ['type']) {
						case 'number_captcha' :
							if (isset($_POST [$field] ['question']) && $this->captchaEncode ( $_POST [$field] ['question'] ) != $_POST [$field] ['answer']) {
								$return = 1;
							}
							break;
						case 'recaptcha' :
							if (null === $this->_recapture_cache) {
								$recaptcha = new contactme_recaptcha ( $this->application () );
								if(isset($_POST ['recaptcha_challenge_field']))
								{
									$this->_recapture_cache = $recaptcha->is_valid ( $_POST ['recaptcha_challenge_field'], $_POST ['recaptcha_response_field'] );
								}
								else
								{
									$this->_recapture_cache = 1;
								}
							}
							if (! $this->_recapture_cache) {
								$return = 1;
							}
							break;
						case 'cookie' :
							if (isset ( $_COOKIE [$this->application ()->slug . '_' . strtolower ( $form ).'_'.$this->user()->user_login] )) {
								$return = 1;
							}
							break;
					}
				}
			} else {
				switch ($field_definition ['type']) {
					case 'cookie' :
						if (isset ( $_COOKIE [$this->application ()->slug . '_' . strtolower ( $form ).'_'.$this->user()->user_login] )) {
							$return = 1;
						}
						break;
				}
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function field_name($field_definition) {
		$return = "{$field_definition['field']}";
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function field_id($field_definition) {
		$return = "formfield_{$field_definition['field']}";
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	protected $captcha = null;
	private function captcha() {
		if (null === $this->captcha) {
			$num1 = mt_rand ( 0, 10 );
			$num2 = mt_rand ( 0, 10 );
			$question = sprintf ( 'The sum of %d and %d is:', $num1, $num2 );
			$answer = $this->captchaEncode ( $num1 + $num2 );
			$this->captcha = new stdClass ();
			$this->captcha->num1 = $num1;
			$this->captcha->num2 = $num2;
			$this->captcha->answer = $answer;
		}
		return $this->captcha;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function captchaEncode($value) {
		$salt = 'fgdksd483ujhfk85gj549guqyglhglakhg';
		return md5 ( $salt . $value );
	}	
	public function read_field_definitions($return, $form = null) {
		$types = $this->application ()->settings['types'];
		$data_collection = $return['data_collection'];
/*****************************************************************************************
* work out which fields a form is required to have.
*****************************************************************************************/
		$required=array();
		$modes = array('form','results','email','post','table');
		foreach($types as $key=>$value)
		{
			foreach($modes as $mode)
			{
				if($data_collection[$mode]['do']==='checked' && $value[$mode]['required']===true)
				{
					$required[$key]=$value;
					break;
				}
			}
		}
/*****************************************************************************************
* work out which required fields the form does not have.
*****************************************************************************************/
		foreach($return['field_definitions'] as $field)
		{
			unset($required[$field['type']]);
		}
/*****************************************************************************************
* add the missing required fields.
*****************************************************************************************/
		foreach($required as $key=>$value)
		{
			$return['field_definitions'][]['type']=$key;
		}
/*****************************************************************************************
* fill in the default type info 
* number the fields is they don't already have numbers
*****************************************************************************************/
		$cnt = 1;
		foreach ( $return['field_definitions'] as $key => &$value ) {
			if (isset ( $types [$value ['type']] )) {
				$value = bv48fv_data_array::merge ( $types [$value ['type']], $value );
				if(null===$value['field'])
				{
					$value ['field'] = $cnt ++;
				}
			} else {
				unset ( $return ['field_definitions'][$key] );
			}
		}
		unset ( $value );
/*****************************************************************************************
* remove fields that do not have the needed data collection type
*****************************************************************************************/
		foreach($return['field_definitions'] as $key=>$value)
		{
			foreach($modes as $mode)
			{
				if($value[$mode]['needs']===true && $data_collection[$mode]['do']==='')
				{
					unset($return['field_definitions'][$key]);
				}
			}
		}
/*****************************************************************************************
* if there are no quiz quiz questions remove the quiz header.
*****************************************************************************************/
		$remove = true;
		foreach($return['field_definitions'] as $key=>$value)
		{
			if($value['type']==='quiz_question')
			{
				$remove = false;
				break;
			}
		}
		if($remove === true)
		{
			foreach($return['field_definitions'] as $key=>$value)
			{
				if($value['type']==='quiz_header')
				{
					unset($return['field_definitions'][$key]);
					break;
				}
			}
		}
/*****************************************************************************************
* Reorder and renumber the fields as set default quiz_questions.
* make sure options are array
*****************************************************************************************/
		usort ( $return['field_definitions'], array ($this, 'field_definitions_sort' ) );
		$cnt = 1;
		$fcnt=1;
		$quiz = 0;
		foreach ( $return['field_definitions'] as &$value ) {
			if(null===$value['fixed_field'])
			{
				$value ['field'] = $cnt ++;
			}
			else
			{
				$value ['field'] = '_'.$fcnt ++;
			}
			if($value['type']=='quiz_question')
			{
				$quiz++;
				if(null===$value['question'])
				{
					$value['question']="Question {$quiz}";
				}
			}
			if(isset($value['options']))
			{
				$value['options'] = apply_filters('contactme_field_options',$value['options']);
			}
		}
		unset ( $value );
/*****************************************************************************************
* fill in calculated fields
*****************************************************************************************/
		$quiz_header = null;
		$quiz_questions = 0;
		$quiz_correct = 0;
		foreach ( $return['field_definitions'] as $key => &$value ) {
			if($value['type']==='quiz_header')
			{
				$quiz_header = &$value;
			}
			$value ['value'] = $this->value ( $value ,$form);
			$value ['error'] = $this->error ( $value, $return, $form );
			if(isset($value['options']))
			{
				$value ['multi'] = count ( $value ['options'] );
			}
			else
			{
				$value ['multi'] = 0;
			}
			$value ['field_name'] = $this->field_name ( $value );
			$value ['field_id'] = $this->field_id ( $value );
			if ($value ['type'] == 'number_captcha') {
				$value ['captcha'] = $this->captcha ();
			}
			if($value['type']==='quiz_question')
			{
				$quiz_questions++;
				if($value['value']==$value['answer'])
				{
					$quiz_correct++;
				}
			}
		}
		if(null!==$quiz_header)
		{
			$quiz_header['quiz']['questions']=$quiz_questions;
			$quiz_header['quiz']['correct']=$quiz_correct;
		}
		unset ( $value );
		return $return;
	}
/*****************************************************************************************
* Sort fields 
*****************************************************************************************/
	function field_definitions_sort($a, $b) {
		$pos_a = $a ['field'];
		$pos_b = $b ['field'];
		if (null !== $a ['fixed_field']) {
			$pos_a = $a ['fixed_field'];
		}
		if (null !== $b ['fixed_field']) {
			$pos_b = $b ['fixed_field'];
		}
		if ($pos_a == $pos_b) {
			return 0;
		}
		return ($pos_a < $pos_b) ? - 1 : 1;
	}
}		