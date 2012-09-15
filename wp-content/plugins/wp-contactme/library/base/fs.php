<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class bv48fv_fs extends bv48fv_base {
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $dirHandle = null;
	private $path = "";
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function path($path = null) {
		if(null!==$path)
		{
			$this->path = $path;
		}
		return $this->path;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private $dir = null;
	const type_file = 1;
	const type_directory = 2;
/*****************************************************************************************
* ??document??
*****************************************************************************************/	
	public function fnmatch($pattern, $string) {
		for($op = 0, $npattern = '', $n = 0, $l = strlen ( $pattern ); $n < $l; $n ++) {
			switch ($c = $pattern [$n]) {
				case '\\' :
					$npattern .= '\\' . @$pattern [++ $n];
					break;
				case '.' :
				case '+' :
				case '^' :
				case '$' :
				case '(' :
				case ')' :
				case '{' :
				case '}' :
				case '=' :
				case '!' :
				case '<' :
				case '>' :
				case '|' :
					$npattern .= '\\' . $c;
					break;
				case '?' :
				case '*' :
					$npattern .= '.' . $c;
					break;
				case '[' :
				case ']' :
				default :
					$npattern .= $c;
					if ($c == '[') {
						$op ++;
					} else if ($c == ']') {
						if ($op == 0)
							return false;
						$op --;
					}
					break;
			}
		}
		
		if ($op != 0)
			return false;
		return preg_match ( '/' . $npattern . '/i', $string );
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function dir($pattern = '*.*', $type = null, $recursionDepth = 0) {
		if (is_null ( $type )) {
			$type = self::type_directory + self::type_file;
		}
		if (is_null ( $this->dir )) {
			$this->dir = array ();
			if ($this->openDir ()) {
				while ( ($file = readdir ( $this->dirHandle )) !== false ) {
					//break;
					// exclude system pointer folders
					if (! in_array ( $file, array ('.', '..', '.svn', '.DS_Store' ) )) {
						$this->dir [] = $this->path () . DIRECTORY_SEPARATOR . $file;
					}
				}
				$this->closeDir ();
			}
		}
		//$this->debug($this->dir);
		$return = array ();
		$start = 0;
		foreach ( $this->dir as $entry ) {
			if ((is_dir ( $entry ) && ($type & self::type_directory)) || (! is_dir ( $entry ) && ($type & self::type_file))) {
				if ($this->fnmatch ( $pattern, $entry )) {
					$return [] = $entry;
				}
			}
			if (is_dir ( $entry )) {
				$child = new self ( $this->application (), $entry );
				if ($recursionDepth > 0 || is_null ( $recursionDepth )) {
					foreach ( $child->dir ( $pattern, $type, $recursionDepth - 1 ) as $sub ) {
						$return [] = $sub;
					}
				}
			}
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function relativeDir($pattern = '*.*', $type = null, $recursionDepth = 0) {
		$dir = $this->dir ( $pattern, $type, $recursionDepth );
		$start = strlen ( $this->path () ) + 1;
		$return = new bv48fv_Array ( );
		foreach ( ( array ) $dir as $item ) {
			$return [] = substr ( $item, $start );
		}
		return $return;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	public function __construct(&$application, $path = null) {
		parent::__construct ( $application );
		$this->path = $path;
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function openDir() {
		if (is_dir ( $this->path () )) {
			$this->dirHandle = opendir ( $this->path () );
		} else {
			$this->dirHandle = null;
		}
		return (! is_null ( $this->dirHandle ));
	}
/*****************************************************************************************
* ??document??
*****************************************************************************************/
	private function closeDir() {
		if (! is_null ( $this->dirHandle )) {
			closedir ( $this->dirHandle );
			$this->dirHandle = null;
		}
	}
}