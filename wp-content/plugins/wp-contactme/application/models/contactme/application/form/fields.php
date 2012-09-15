<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class contactme_application_form_fields extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_form = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct($application, $form) {
		parent::__construct ( $application );
		$this->_form = $form;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function &field($field) {
		return $this->cache ( 'contactme_application_form_fields_field', array ($this->_form, $field ), true );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function &data() {
		return parent::data ( $this->_form );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function render($screen) {
		$data = new stdClass();
		$data->front=array();
		$data->email=array();
		$data->post=array();
		$data->table=new stdClass();
		$data->table->data=array();
		$data->table->type=array();
		$data->rss=array();
		$data->email_details=$this->email_details();
		foreach ( $this->data ()->field_definitions as $field => $definition ) {
			$this->field($field)->render($data,$screen);
		}
		if(count($data->table->data)>0)
		{
			$data->table->data['info[submission]']=0;
			$data->table->type['info[submission]']='bigint(20)';
			$this->table ()->addslashes ( $data->table->data );
		}
		return $data;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function error() {
		$return = 0;
		foreach ( $this->data ()->field_definitions as $field ) {
			if ($field ['error'] != 0 ) {
				$return = $field ['error'];
				break;
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function first_field($field, $default = null) {
		$return = $default;
		$subjects = $this->get_questions ( array ($field ) );
		foreach ( $subjects as $subject ) {
			if (trim ( $subject->$field ) != '') {
				$return = trim ( $subject->$field );
				break;
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function email_details() {
		$return = $this->get_types ( array ('email', 'name', 'cc' ) );
		foreach ( $return as $key => &$value ) {
			if (! is_email ( $value->email ['value'] )) {
				unset ( $return [$key] );
			} else {
				$value->email ['value'] = trim ( $value->email ['value'] );
				if(!isset($value->name['value']))
				{
					$value->name=$value->email;
				}
				//$value->email_question = $value->question;
				if (trim ( $value->name ['value'] ) == '') {
					$value->name ['value'] = $value->email ['value'];
				}
				$value->name ['value'] = trim ( $value->name ['value'] );
				$value->cc ['value'] = (! empty ( $value->cc ['value'] ));
			}
		}
		unset ( $value );
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function quiz_details() {
		$questions = $this->get_types ( array ('quiz_question' ) );
		$results = new stdClass ();
		$results->questions = count ( $questions );
		$results->correct = 0;
		foreach ( $questions as $key => $value ) {
			if ($value->quiz_question ['details'] ['value'] == $value->quiz_question ['details'] ['answer']) {
				$results->correct ++;
			}
		}
		return $results;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function poll_details() {
		$return = $this->get_types ( array ('poll_question' ) );
		return $return;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_types($types = array()) {
		$return = array ();
		$max = 0;
		foreach ( $types as $type ) {
			$return [$type] = array ();
			foreach ( $this->data ()->field_definitions as $key => $value ) {
				if ($type == $value ['type']) {
					$return [$type] [] = array ('value' => $value ['value'], 'question' => $value ['question'], 'details' => $value );
				}
			}
			$max = max ( $max, count ( $return [$type] ) );
		}
		foreach ( $types as $type ) {
			$return [$type] = array_pad ( $return [$type], $max, '' );
		}
		$data = array ();
		for($cnt = 0; $cnt < $max; $cnt ++) {
			$data [$cnt] = new stdClass ();
			foreach ( $types as $type ) {
				$data [$cnt]->$type = $return [$type] [$cnt];
			}
		}
		return $data;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function get_questions($questions = array()) {
		$return = array ();
		$max = 0;
		foreach ( $questions as $question ) {
			$return [$question] = array ();
			foreach ( $this->data ()->field_definitions as $key => $value ) {
				if ($value ['type']=='quiz_question') {
					$return [$question] [] = $value ['value'];
				}
			}
			$max = max ( $max, count ( $return [$question] ) );
		}
		foreach ( $questions as $question ) {
			$return [$question] = array_pad ( $return [$question], $max, '' );
		}
		$data = array ();
		for($cnt = 0; $cnt < $max; $cnt ++) {
			$data [$cnt] = new stdClass ();
			foreach ( $questions as $question ) {
				$data [$cnt]->$question = $return [$question] [$cnt];
			}
		}
		return $data;
	}
}