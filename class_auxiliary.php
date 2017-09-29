<?php 

class auxiliary {
	public static function array_items($array,$key) {
		if (array_key_exists($key, $array)) return ($array[$key]);
		else return FALSE;
	}

	public static function array_key_check($array,$key) {
// 		if (array_key_exists($key, $array)) $ret = "TRUE";
// 		else $ret = "FALSE";
// 		print "<p>$ret :<br> key / array= $key / <pre>";
// 		print_r($array);
// 		print "</pre></p>";	
		if (array_key_exists($key, $array)) return TRUE;
		else return FALSE;
	}
	
	public static function checkKeyValue($array, $key, $val) {
		foreach ($array as $item)
			if (isset($item[$key]) && $item[$key] == $val) {
			return TRUE;
		}
		return FALSE;
	}

	public static function truncateReturn($aString,$integer) {
		$length = strlen($aString);
		$aString = substr($aString, 0, $length-$integer);
		return $aString;
	}
	
	public static function secureit($data) {
		if ($data) {
		$retdata = trim($data);
		$retdata = stripslashes($data);
		$retdata = htmlspecialchars($data);
		} else {
			$retdata = "no data supplied";
		}
		return $retdata;
	}
	
	public static function secnserialize($data) {
		foreach ($data as $key => $value) {
			self::secureit($value);
		}
		$retdata = self::secureit($data);
		$retdata = serialize($retdata);
		return $retdata;		
	}

	public static function mk_date_range($lower,$upper,$start) {
		$tp = $start;
		$format = '%d-%m-%Y';
		//global $format;
		$range = array();
		$maxpoint = $tp - $upper;
		$minpoint = ($tp- $lower);
		$fmaxpoint = strftime($format,$maxpoint);
		$fminpoint = strftime($format,$minpoint);
		$range['max'] = $fmaxpoint;
		$range['min'] = $fminpoint;
		return $range;
	}
	
	function displayErrorMsg() {
		$error = $this->errormsg;
		print "<p class=\"errormsg\">".$error."</p>";
	}
	
	public static function genrand($id=1) { // generate pseudo random no
		$uid = $id;
		$ip = $_SERVER['REMOTE_ADDR'];
		$random = doubleval((int)microtime(TRUE));
		$ts = (int)time();
		$random *= $ts;
		$random *= $uid;
		$random = $random + ip2long($ip);
		return (int)$random;
	}
	
	function baseDir() {
		$pathfile = $_SERVER['SCRIPT_NAME'];;
		$pathdir = $_SERVER['DOCUMENT_ROOT'];
		$dir = "";
		$script = "";
		$return = array();
		$dir = $pathdir."/";
		$return['dir'] = $dir;
		$breakfile = explode('/', $pathfile);
		for ($i=1; $i < count($breakfile); $i++) {
			if ($i == count($breakfile)-1) {
				$script = $breakfile[$i];
				$return['file'] = $script;
			} else {
				$d = $breakfile[$i]."/";
				$return['dir'] .= $d;
			}
		}
		return $return;
	}
}

?>