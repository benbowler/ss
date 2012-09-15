<?php
require_once WPSQT_DIR.'lib/Wpsqt/Export.php';

	/**
	 * 
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Export_Csv extends Wpsqt_Export {
		
	public function output(){
		
		$csv = "";
		foreach ( $this->_data as $array ) {
			$csv .= implode(",",$array).PHP_EOL;
		}
		
		return $csv;	
	}		
		
}