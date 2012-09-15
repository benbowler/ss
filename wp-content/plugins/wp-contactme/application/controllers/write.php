<?php
/*****************************************************************************************
* filters on data writes
*****************************************************************************************/
class contactme_write extends wv48fv_action {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function field_options($return) {
		if (! empty ( $return ) && ! is_array ( $return )) {
			$return = explode ( "\n", $return );
			// do the trim after its been turned to an arryam allows for a blank leader, and will trim each option to.
			foreach ( $return as &$option ) {
				$option = trim ( $option, "\r\n\t " );
			}
			unset ( $option );
		}
		return $return;
	}
	public function contactme_writeWPfilter($return,$form) {
		foreach ( $return['field_definitions'] as $key => &$value ) {
			if(isset($value['options']))
			{
				$value['options'] = $this->field_options($value['options']);
			}
			if (isset ( $value ['delete'] ) || $value['type']=='new') {
				unset ( $return ['field_definitions'][$key] );
			}
		}
		unset ( $value );
		return $return;
	}
}		