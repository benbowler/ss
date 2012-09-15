<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv48fv_request extends bv48fv_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function is_post($form=null)
	{
		$return = false;
		if ($_SERVER ['REQUEST_METHOD'] == 'POST')
		{
			$return = true;
			if(null!==$form)
			{
				$return = isset($_POST['submit'][$this->application()->slug][$form]);
			}
		}
		return $return;
	}
}