<?php
/*****************************************************************************************
* All the front stuff
*****************************************************************************************/
class contactme_front extends wv48fv_action {
	public function contactmeWPshortcode($atts, $content=null, $code="") {
		$form = null;
		if (isset ( $atts ['form'] ) && ! empty ( $atts ['form'] )) {
			$form = $atts ['form'];
		}
		else{
			if(is_array($atts))
			{
				foreach($atts as $form)
				{
					break;
				}
			}
		}
		return $this->application()->form($form)->render();
	}
}		