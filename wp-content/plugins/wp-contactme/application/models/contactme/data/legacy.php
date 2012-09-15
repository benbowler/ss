<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class contactme_data_legacy extends wv48fv_data_legacy {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $_data = null;
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function data() {
		if (null === $this->_data) {
			$data = parent::data ();
			if(count($data)!=0)
			{
				foreach ( $data as &$datum ) {
					if (isset ( $datum ['field_definitions'] )) {
						unset ( $datum ['field_definitions'] ['count'] );
						$cnt = 1;
						array_unshift ( $datum ['field_definitions'], array ('type' => 'cookie' ) );
						$datum ['field_definitions'] [] = array ('type' => 'wpuser' );
						$datum ['field_definitions'] [] = array ('type' => 'formsent', 'question' => 'Form sent @ ' );
						if (in_array ( $this->application ()->slug, array ('quizme' ) )) {
							$datum ['field_definitions'] [] = array ('type' => 'new' );
						}
						foreach ( $datum ['field_definitions'] as &$value ) {
							if ($value ['type'] != 'new') {
								$value ['field'] = $cnt;
							}
							switch ($this->application ()->slug) {
								case 'quizme' :
									if ($value ['type'] == 'radio') {
										$value ['type'] = "quiz_question";
									}
									break;
							}
							$cnt ++;
						}
						unset ( $value );
					}
					if (isset ( $datum ['views'] ['responses'] )) {
						foreach ( $datum ['views'] ['responses'] as &$value ) {
							$value = str_replace ( '<?php $this->show_results();?>', '', $value );
						}
						unset ( $value );
					}
					if(isset($datum ['data_collection']['table']['do']))
					{
						if($datum ['data_collection']['table']['do'])
						{
							$datum ['data_collection']['table']['do']='checked';
						}
					}
					if(isset($datum ['data_collection']['email']['do']))
					{
						if($datum ['data_collection']['email']['do'])
						{
							$datum ['data_collection']['email']['do']='checked';
						}
					}
					unset ( $datum ['data_collection']['table']['once'] );
					switch ($this->application ()->slug) {
						case 'quizme' :
							unset ( $datum ['graphics'] );
							unset ( $datum ['data_collection'] );
							break;
					}
					$datum['field_definitions'][]=array('type'=>'new');
				}
				unset ( $datum );
			}
			$this->_data = $data;
		}
		return $this->_data;
	
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function move() {
		foreach ( $this->data () as $key => $value ) {
			$this->data ( $key )->writePost ( $value );
		}
	}
}