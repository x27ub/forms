<?php
define('__ROOT__', (dirname(__FILE__)));

require_once(__ROOT__.'/class_dbConfig.php');

class dbOperations extends dbConnection {
// 	protected $dbConfig = array('dbhost'=>'127.0.0.1','dbuser'=>'testing','dbpass'=>'fXVp5EcCVAjxfJtA','dbname'=>'testing');
// 	protected $host;
// 	protected $user;
// 	protected $pass;
// 	protected $dbname;
	protected static $inputArray;
	protected static $params = "DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin ENGINE=InnoDB;";
	protected static $salt;
	protected static $pid;
	protected static $fid;
	
	var $format = '%d/%m/%Y %H:%M:%S';
	var $uploadPath;
	var $charset = "utf8";
	protected static $tableName;
	var $tableSuffix;
	var $ip;
	var $delimiter = "|"; // delimiter for multiple values for select or checkbox form fields
	
	public $errormsg;


	function __construct($inputArray,$relpath=FALSE,$tableName="forms",$suffix="",$salt="I|1Tofu+-Wev>w.W=S\$Y+5$44HC;[<",$pid=FALSE,$fid=FALSE) {
// 		$this->dbConfig = $dbConfig;
// 		$this->host = $dbConfig['dbhost'];
// 		$this->user = $dbConfig['dbuser'];
// 		$this->pass = $dbConfig['dbpass'];
// 		$this->dbname = $dbConfig['dbname'];
		parent::__construct();
		$tmp = $this->baseDir();
		if ($relpath) {
			$this->uploadPath = $relpath;
		} else $this->uploadPath = "/";
		if ($pid) $this->pid = $pid;
		if ($fid) $this->fid = $fid;
		$this->tableName = $tableName;
		$this->inputArray = $inputArray;
		$this->tableSuffix = $suffix;
		$this->salt = $salt;
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->errormsg = (isset($this->errormsg) ? $this->errormsg : '');
		//$conn = new dbConnection($parameters);
		//$conn = new mysqli($dbConfig['dbhost'], $dbConfig['dbuser'], $dbConfig['dbpass']);
		//$query = "CREATE DATABASE IF NOT EXISTS `".$this->dbname."`;";
		//$conn->prepare($query);
		//$conn->query($query);
	}
	
	public static function getInstance($parameters=array(),$inputArray,$relpath=FALSE,$tableName="forms",$suffix="",$salt="I|1Tofu+-Wev>w.W=S\$Y+5$44HC;[<",$pid=FALSE,$fid=FALSE){
		if (is_null( self::$instance )){
			self::$instance = new self($parameters=array(),$inputArray,$relpath=FALSE,$tableName="forms",$suffix="",$salt="I|1Tofu+-Wev>w.W=S\$Y+5$44HC;[<",$pid=FALSE,$fid=FALSE);
		}
		return self::$instance;
	}
	
	
	
	
	
	
	
	

	// helper functions
	function displayErrorMsg() {
		$error = $this->errormsg;
		print "<p class=\"errormsg\">".$error."</p>";
	}
	
	function genrand($id=1) { // generate pseudo random no
		$uid = $id;
		$random = doubleval((int)microtime(TRUE));
		$ts = (int)time();
		$random *= $ts;
		$random *= $uid;
		$random = $random + ip2long($this->ip);
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
	
	static function get_autoindex() {
		$tableName = $this->tableName;
		$tableName .= $this->tableSuffix;
		$query = "SELECT auto_increment FROM information_schema.tables WHERE TABLE_NAME = '".$tableName."';";
		$sql_connect = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		if ($result = $sql_connect->query($query)) {
			$row = $result->fetch_assoc();
			$auto_index = $row['auto_increment'];
		}
		return $auto_index;
	}
	
	function checkField($field=FALSE, $where=FALSE, $cond=FALSE) {
		/* usage
		 * 3 vars: SELECT $fields FROM $tableName WHERE $cond = $where;
		 * 1 vars: SELECT $fields FROM $tableName -- 2 vars: SELECT $fields FROM $tableName WHERE $field = $where;
		 * default SELECT `uid`, `sid` FROM $tableName WHERE `sid` = $sessid; 
		*/
		$tableName = $this->tableName;
		$tableName .= $this->tableSuffix;
		$sessid = isset($_POST['checksid']) ?  $_POST['checksid'] : "-1";
		$return = array();
		$query = "";
		$whereclause = "";
		$strrow = "";
		if (!$field) {
			$field = 'sid';
			$query = "SELECT `uid`, `$field` FROM $tableName WHERE `$field` = '$sessid';";
		} elseif ($field === 'MAX(`uid`)') {
			$query = "SELECT MAX(`uid`) FROM $tableName";
			// $field = "MAX(`uid`)";
		} else {
			$query = "SELECT `$field` FROM $tableName";
			if ($where === FALSE) {
			} else {
				if ($cond) {
					$whereclause = " WHERE `$cond` = '".$where."';";				
				} else {
					$whereclause = " WHERE `$field` = '".$where."';";
				}

			}
			$query .= $whereclause;
		}
		//echo "<p>the query: $query</p>";
		$sql_connect = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$result = $sql_connect->query($query);
		$rows = $result->num_rows;
		$cols = $result->field_count;
		if ($rows) { // see if there is a non void result
			while ($row = $result->fetch_assoc()) { // pick up all returned values from query
				$strrow .= $row[$field].", ";
			}
			$return = $row;
			$strrow = $this->truncateReturn($strrow, 2);
			$return[$field] = $strrow;
			$return['bool'] = TRUE;
		} else {
			$return[$field] = NULL;
			$return['bool'] = FALSE;
		}
		mysqli_close($sql_connect);
		return $return;
	}
	
	function getFileProp($thename,$theporperty) {
		/*
		 * 'maxfilesize'=>,'allowed_filetypes'=>,'allowed_extensions'=> must exist in order for it to work
		 *  makes no sense to not assign them either
		 */
		$flag = 0;
		$fileFieldCount = "";
		$fieldname = "";
		$result = array();
		$file_prop = array();
		$form_array = $this->inputArray;
		while ($formArray = each($form_array)) {
			$fieldset = $formArray['key'];
			while ($formConf = each($form_array[$fieldset])) {
				$label = $formConf['key'];
				while ($type = each($form_array[$fieldset][$label])) {
					$typeArray = $type['key'];
					while ($config = each($form_array[$fieldset][$label][$typeArray])) {
						$name = $config['key'];
						$value = $config['value'];
						if ($value === 'file' AND $name === 'type') {
							$flag++;
							$fileFieldCount .= "_";
							$lg = strlen($fileFieldCount); // how many upload fields in form
						} //else nothing
						if ($flag > 0) {
							if ($name === 'name') {
								$fieldname = $value;
								$file_prop[$fieldname] = array();
							} elseif ($name === 'maxfilesize') {
								$file_prop[$fieldname]['maxfilesize'] = $value;
							} elseif ($name === 'allowed_filetypes') {
								$file_prop[$fieldname]['allowed_filetypes'] = $value;
							} elseif ($name === 'allowed_extensions') {
								$file_prop[$fieldname]['allowed_extensions'] = $value;
								$flag = 0;
								$fieldname = "";
							} elseif ($name === 'multiple') {
								// nothing
							} elseif ($name === 'accept') {
								// nothing
							} else {
								// do nothing, $name='type' $value='file'
							}
						} else {
							break;
						}
					}
				}
			}
		}
//echo "<pre>";
//print_r($file_prop);
//echo "</pre>";
		return $file_prop[$thename][$theporperty];
	}

	function array_items($array,$key) {
		if (array_key_exists($key, $array)) return ($array[$key]);
		else return FALSE;
	}

	function array_key_check($array,$key) {
		if (array_key_exists($key, $array)) return TRUE;
		else return FALSE;
	}

	function truncateReturn($aString,$integer) {
		$length = strlen($aString);
		$aString = substr($aString, 0, $length-$integer);
		return $aString;
	}

	function value_in_array($value, $array) {
		if(in_array($value, $array)) {
			return TRUE;
		}
		foreach($array as $ar) {
			if(is_array($ar) && $this->value_in_array($value, $ar))
			return TRUE;
		}
		return FALSE;
	}

	/* produce the db fields from form's fields name
	 * put the posted values in an array 
	 */ 
	function makeFields() {
		$table_fields = '';
		$entry = '';
		$pwFieldName = '';
		$dateFieldName = '';
		$theUpfieldName = '';
		$upFieldName = array();
		$formFieldValue = array();
		$selectedValues = array();
		$postvar = array();
		$form_array = $this->inputArray;
		while ($formArray = each($form_array)) {
			$fieldset = $formArray['key'];
			while ($formConf = each($form_array[$fieldset])) {
				$label = $formConf['key'];
				while ($config = each($form_array[$fieldset][$label])) {
					$thetype = $config['key'];
					$thevalue = $config['value'];
					$i = 0;
					while ($type = each($form_array[$fieldset][$label][$thetype])) { // 4th while
						$nameFields = $type['key'];
						$formFields = $type['value'];
						$inputKey = $this->array_key_check($form_array[$fieldset][$label], 'input');
						$selectKey = $this->array_key_check($form_array[$fieldset][$label], 'select');
						$textareKey = $this->array_key_check($form_array[$fieldset][$label], 'textarea');
						$typeKey = $this->array_key_check($form_array[$fieldset][$label][$thetype], 'type');
						$nameKey = $this->array_key_check($form_array[$fieldset][$label][$thetype], 'name');
						// avoid 'undefined indexes' errors
						if (!$_POST) $checkPostVar = FALSE;
						else $checkPostVar = TRUE;	
						if (!$_FILES) $checkFilesVar = FALSE;
						else $checkFilesVar = TRUE;					
						if ($inputKey) { // <input ...	key 'submit' replace by variable
							if ($typeKey) { // <input type ...
								if ($formFields === 'submit') { // <input type="submit" ...
									// create no db table field for submit buttons within fieldsets
									$table_fields .= '';
									break;
								} elseif ($formFields === 'password') { // <input type="password" ...
									// get name of password field
									$pwFieldName = $form_array[$fieldset][$label][$thetype]['name'];
								} elseif ($formFields === 'date' OR  $formFields === 'time' OR  $formFields === 'datetime' OR  $formFields === 'datetime-local') { // <input type="date/datetaime/..." ...
									// get name of date field
									$dateFieldName = $form_array[$fieldset][$label][$thetype]['name'];
								} elseif ($formFields === 'file') { // <input type="file" ...
									// get names of upload fields
									$theUpfieldName = $form_array[$fieldset][$label][$thetype]['name'];
									$upFieldName[$theUpfieldName] = $theUpfieldName;
									//echo "<pre>". print_r($upFieldName) ."</pre>";
								}  elseif ($nameFields === 'name') { // <input type="text/radio/checkbox/number/range/date/time" ... else?
									// get values from name fields only
									if ($form_array[$fieldset][$label][$thetype]['name'] === $pwFieldName){
										//echo "<br>".$formFields."<br>";
										$table_fields .= " `$formFields` ";
										$entry .= " `$formFields`, "; // create db table entry
										$formFieldValue[$pwFieldName] = addslashes(htmlspecialchars($_POST[$formFields]));
										$passwd = addslashes(htmlspecialchars($_POST[$formFields])); //$_POST[$pwFieldName];
										$postvar[$formFields] = crypt($passwd,$this->salt);
									} elseif ($form_array[$fieldset][$label][$thetype]['name'] === $dateFieldName) {
										$table_fields .= " `$formFields` ";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkPostVar) {
											$formFieldValue[$dateFieldName] = addslashes(htmlspecialchars($_POST[$formFields]));
											$dateTime = addslashes(htmlspecialchars($_POST[$formFields])); //$_POST[$datetFieldName];
											$postvar[$formFields] = strtotime($dateTime);
										}
									}  elseif ($form_array[$fieldset][$label][$thetype]['name'] === $theUpfieldName) {
										//echo "<br>".$formFields."<br>";
										$table_fields .= " `$formFields` ";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkFilesVar) {
											$formFieldValue[$theUpfieldName] = $_FILES[$theUpfieldName]['name'];
											//if ($this->array_key_check($_POST, $formFields)) {
											$file = $_FILES[$theUpfieldName]['name'];
											$postvar[$formFields] = addslashes(htmlspecialchars($file));
										}
									} else {
										$table_fields .= " `$formFields` "; // create db table field
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkPostVar) {
										$formFieldValue[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
										$postvar[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
										}
									}
								} elseif ($nameFields === '@#db') {
									$table_fields .= " $formFields,"; // needed to create db table only
								} else {
									//
									$table_fields .= ''; // all other fields \neq 'name' of <input type="text/radio/chechbox" ... else?
									$entry .= '';
								}
							} elseif ($typeKey === 'file') { // needed or not? !$typeKey === 'file'
									
							} else { // <input .... without any type declaration
								// do nothing
								$table_fields .= '';
								$entry .= '';
							}
						} elseif ($textareKey) { // <select ... OR <textarea ...
							if ($nameFields === 'name') {
								// get values from name fields only
								$table_fields .= " `$formFields` "; // create db table field
								$entry .= " `$formFields`, "; // create db table entry
								if ($checkPostVar) {
									$formFieldValue[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
									$postvar[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
								}
							} elseif ($nameFields === '@#db') {
								$table_fields .= " $formFields,"; // needed to create db table only
							} else {
								//
								$table_fields .= ''; // all other fields \neq 'name' of <input type="text/radio/chechbox" ... else?
								$entry .= '';
							}
						} elseif ($selectKey) {
							$selectedVal = '';
							if ($nameFields === 'name') {
								$table_fields .= " `$formFields` ";
								$entry .= " `$formFields`, ";
								// need to get the selected values from form fields like 'select' with optgroup
								if ($checkPostVar) {
									if (is_array($_POST[$formFields])) { // i.e 'name' is an array
										foreach ($_POST[$formFields] as $key => $value) {
											$selectedVal .= addslashes(htmlspecialchars($value))."| ";
											$_POST[$formFields][$key] = addslashes(htmlspecialchars($value));
										}
										$selectedVal = $this->truncateReturn($selectedVal, 2);
										$selectedValues[$formFields][] = $selectedVal;
									} else {										
										$selectedVal = addslashes(htmlspecialchars($formFields));									
									}
									$formFieldValue[$formFields] = $_POST[$formFields];
									$postvar[$formFields] = $selectedVal;									
								}
							} elseif ($nameFields === '@#db') {
								$table_fields .= " $formFields,"; // needed to create db table only
							} else {
								//
								$table_fields .= ''; // all other fields \neq 'name' of <input type="text/radio/chechbox" ... else?
								$entry .= '';
							}
						}
					} // end 4th while
				}
			}
		}
		$tableInfo = array();
		$tableInfo[0] = $table_fields; // create table fields with type property
		$tableInfo[1] = $entry; // for queries of table like INSERT ... Values ($entry
		$tableInfo[2] = $formFieldValue; // to be put into value field of the form itself
		$tableInfo[3] = $selectedValues; // holds all selected values from 'checkbox' or 'select' fields, key is the name
		$tableInfo[4] = $postvar; // entire $_POST array where keys are the form field's names
		$tableInfo[5] = $upFieldName; // upload fields
		return $tableInfo;
	}

	function createTable() { // get db table fields from form's fields names
		$params = $this->params;
		$tableName = $this->tableName;
		$tableName .= $this->tableSuffix;
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		$fields = $this->makeFields();
		$query = "CREATE TABLE IF NOT EXISTS `";
		$query .= $tableName;
		//$query .= "` (`uid` INT( 11 ) UNSIGNED AUTO_INCREMENT PRIMARY KEY, `crdate` INT( 11 ) UNSIGNED NOT NULL, `tstamp` INT( 11 ), `hidden` TINYINT( 1 ) NOT NULL DEFAULT '0', `deleted` TINYINT( 1 ) NOT NULL DEFAULT '0', `sid` TINYTEXT NOT NULL,";
		$query .= "` (`uid` INT( 11 ) UNSIGNED AUTO_INCREMENT PRIMARY KEY, `crdate` INT( 11 ) UNSIGNED NOT NULL, `tstamp` INT( 11 ), `sid` VARCHAR (255)  NULL,";
		$query .= $fields[0];
		$query .= "`pid` VARCHAR( 255 ) NOT NULL, `fid` INT( 11 )";
		$query .= ") ".$params; 
		//echo  "<pre>$query</pre>"; //creates table
		if (mysqli_connect_error())
		die("Cannot connect to database! ") . $sql_connect->error;
		else {
			$sql_connect->prepare($query);
			if($sql_connect->query($query) === TRUE) {
				//echo "table successfully created<br>";
				mysqli_close($sql_connect);
			} else {
				die ("cannot create table: " . $sql_connect->error);
			}
		}
	}

	function write2Table($sess=FALSE) { // fid and pid are coming from the powermail form for applicants
		$stringOFields = "";
		$delimiter = $this->delimiter;
		$tableName = $this->tableName;
		$tableName .= $this->tableSuffix;
		$crdate = time();
		$tstamp = strtotime('now');
		$fields = $this->makeFields();		
		$pieces = explode(", ", $fields[1]);
		$no = count($pieces);
		$stringmsg = "";
		$addFields = "";
		$addValues = "";
		$return = array();
		// if $fid or $pid are set
		if($this->fid) {
			$addFields = ", `fid`";
			$addValues = ", '".$this->fid."'";
		} else {
			$addFields = "";
			$addValues = "";
		}
		if($this->pid) {
			$addFields .= ", `pid`";
			$addValues .= ", '".$this-> pid."'";
		} else {
			$addFields .= "";
			$addValues .= "";
		}
		for ($i=0;$i<$no-1;$i++) {
			$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
			$stringOFields .= "$trimmed, ";
		}
		$sid = $chksid = (isset($_POST['checksid']) ? $_POST['checksid'] : -1);
		$query  = "INSERT INTO $tableName (`crdate`,  `tstamp`, `sid`, ";
		$query .= $stringOFields;
		$query = $this->truncateReturn($query, 2);
		$query .= " ".$addFields.")";
		$query .= " Values ($crdate, $tstamp, '".$sid."', ";
		while ($fieldValues = each($fields[4])) { // get all $_POST['name'] values
			$getValues = $fieldValues['value'];
			if (is_array($getValues)) { // if value is array (from checkboxes or select fields) produce a string
				$query .= "'";
				$temp = ""; // clear $temp from previous loop
				while ($multiple = each($getValues)) { // get all values $_POST['name'] array
					$temp .= addslashes(htmlspecialchars($multiple['value'])).$delimiter." ";
				}
				$query .= $this->truncateReturn($temp, 2);
				$query .= "', ";
			} else { // if value is string just do this
				$query .= "'".addslashes(htmlspecialchars($getValues))."', ";
			}
		}
		$query = $this->truncateReturn($query, 2);
		$query .= " ".$addValues.");";
		//echo  "<pre>The generated query: $query</pre>"; 
		// check if form was already submitted
		$ses = $this->checkField('sid',$_POST['checksid'],'sid');
		$unique_email = $this->checkField('email',$_POST['email'],'email');
		if (!$ses['bool'] AND !$unique_email['bool']) {
			$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
			$sql_connect->set_charset($this->charset);
			$sql_connect->prepare($query);
			if($sql_connect->query($query) === TRUE) { // inserts values into table
				$stringmsg .= "<p class=\"errormsg\" style=\"color:green;\">entry successfull<p>";
				$return['entry'] = 1;	
				mysqli_close($sql_connect);
				if ( !empty($fields[5]) ) { //check if there's at least one upload i.e. type="file" field
					$i = 0;
					$length = count($fields[5]);
					// open db connection
					$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
					$sql_connect->set_charset($this->charset);
					$query = "UPDATE $tableName SET "; // reset query
					foreach ($fields[5] as $upkey => $upvalue) {
						if (is_string($upkey)) {
							$i++;
							$returnArray = $this->uploadHandler($upvalue);
							$stringmsg .= "<p class=\"errormsg\">".$returnArray[0]."</p>";
							$query .= $upkey."= '".$returnArray[1]."'";
							if ($i < $length) {
								$query .= ", ";
							}													
						} else {
							// do nothing if key is an integer
						}
					}
					$query .= " WHERE `sid`= '$sid';";
					//echo $query;
					if($sql_connect->query($query) === TRUE) {
						$stringmsg .= "<p class=\"errormsg\" style=\"color:green;\">filenames written to table</p>";
						$return['update'] = 1;
					} else {
						$stringmsg .= "<p class=\"errormsg\">filenames query could not be executed successfully!</p>" . $sql_connect->error;
						$return['update'] = 0;						
					}
				} else {
					// do nothing
				}
			} else {				
				$stringmsg .= "<p class=\"errormsg\">cannot write 2 table</p>" . $sql_connect->error;
				$return['entry'] = 0;
			}
		} else  {
			$stringmsg .= "<p class=\"errormsg\">entry exists already</p>";
			$return['entry'] = -1;
		}
		$return['msg'] = $stringmsg;	
		//print $stringmsg;
		return $return;
	}
	
	function updateTable($uid) {
		$stringOFields = "";
		$delimiter = $this->delimiter;
		$tableName = $this->tableName;
		$tableName .= $this->tableSuffix;
		$fid = $this->fid;
		$pid = basename($_SERVER['SCRIPT_FILENAME']);
		$crdate = time();
		$tstamp = strtotime('now');
		$fields = $this->makeFields();		
		$pieces = explode(", ", $fields[1]); //try too use $fields[4] for the values
//		echo "<pre>";
//		print_r($fields[4]);
//		echo "</pre>";
		$no = count($pieces);
		$stringmsg = "";
		for ($i=0;$i<$no-1;$i++) {
			$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
			$stringOFields .= "$trimmed, ";
		}
		$files = $fields[5];;
		$query  = "UPDATE $tableName SET `tstamp`='".$tstamp."', ";
		for ($i=0;$i<$no-1;$i++) {
			$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
			$pieces[$i] = substr($pieces[$i], 2, -1);
			if ($this->array_key_check($_POST,$pieces[$i])) {
				if (is_array($_POST[$pieces[$i]])) {
						$query .= "`".$pieces[$i]."`='".$_POST[$pieces[$i]][0]."', "; //need a foreach or an each to iterate thru array
				} else { // elseif regex yyyy-mm-dd then to integer
					$query .= "`".$pieces[$i]."`='".$fields[4][$pieces[$i]]."', "; // $fields[4][$i] instead??
				}
			} elseif ($_FILES[$pieces[$i]]['name']) {
				//$query .= $pieces[$i]."='".$_FILES[$pieces[$i]]['name']."', ";
				$upname = $files[$pieces[$i]];
						$returnArray = $this->uploadHandler($upname);
						$stringmsg .= "<p class=\"errormsg\">".$returnArray[0]."</p>";
						// comma is good in case it's NOT the last field in form
						$query .= "`".$pieces[$i]."`= '".$returnArray[1]."', ";
			} else {
				$query .= "";
			}
		}
		$query = $this->truncateReturn($query, 2); //substr($query, 0, -2);
		$query .= ", `saved` = 1 WHERE `uid`='".$uid."';";
		//echo $query;
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$stringmsg = "<p class=\"errormsg\" style=\"color:green;\">successfully saved</p>";				
			} else {
				$stringmsg = "<p class=\"message\">saving failed</p>";
			}
		}
		print $stringmsg;
	}
	
	function uploadHandler($fieldName) {
		$allowed_filetypes = $this->getFileProp($fieldName, 'allowed_extensions');
		$regex = $this->getFileProp($fieldName, 'allowed_filetypes');
		$maxfilesize = $this->getFileProp($fieldName, 'maxfilesize');
		$return = (isset($return) ? $return : "");
		$filename = "";
		$dest = "";
		$basePath = $this->uploadPath;
		$returnArray = array();
		$name = $_FILES[$fieldName]['name'];
		$type = $_FILES[$fieldName]['type'];
		$tmpfile = $_FILES[$fieldName]['tmp_name'];
		$error = $_FILES[$fieldName]['error'];
		$size = $_FILES[$fieldName]['size'];

		if ($error !== UPLOAD_ERR_OK) {
			switch ($error) {
				case 1:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">1 The uploaded file exceeds the max filesize.</p>";
					$returnArray[1] = " no file 1"; // space is imnportant for if cond in getDataFromDB()
					break;
				case 2:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">2 The uploaded file exceeds the max filesize.</p>";
					$returnArray[1] = " no file 2";
					break;
				case 3:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">3 The uploaded file was only partially uploaded.</p>";
					$returnArray[1] = " no file 3";
					break;
				case 4:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">4 No file was uploaded.</p>";
					$returnArray[1] = " no file 4";
					break;
				case 6:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">6 Cannot store uploaded file.</p>";
					$returnArray[1] = " no file 6";
					break;
				case 7:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">7 Failed to write file to disk.</p>";
					$returnArray[1] = " no file 7";
					break;
				case 8:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">8 PHP stopped the file upload.</p>";
					$returnArray[1] = " no file 8";
					break;
				default:
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">Fatal upload error.</p>";
					$returnArray[1] = " no file ukn";
					break;
			}
		} elseif ($size > $maxfilesize) {
			$mfsize = $maxfilesize/1024;
			$mfsize = number_format($mfsize,2);
			$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">file size exceeds $mfsize MB</p>";
			$returnArray[1] = "file too big";
		} elseif (!$_FILES[$fieldName] OR !is_uploaded_file($tmpfile)) {
			$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">couldn't read file</p>";
			$returnArray[1] = "no filename";
		} else {
			$upfile = basename( $_FILES[$fieldName]['name']);
			$upfile = preg_replace ( '/[^a-z0-9._-]/i', '', $upfile );
			$lastdot = strripos($upfile,'.');
			if($lastdot > 0) $ext = substr($upfile, strripos($upfile,'.'), strlen($upfile)-1);
			else $ext = ".ukn";
			$basename = substr($upfile, 0, strripos($upfile,'.'));
			$filename = $basename.$ext;					
			if(!in_array($ext,$allowed_filetypes)) {
				$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">The file extension you attempted to upload is not permitted.</p>";
				$returnArray[1] = "false ext";
			} else {
				// don't overwrite an existing file
				$i = 0;
				while (file_exists($basePath.$filename)) {
					$i++;
					$filename = $basename . "-" . $i . $ext;
				}
				$dest = $this->uploadPath . $filename;
				//echo "<p>move to = $dest</p>";
				$int = -1;
				exec("clamscan --stdout " . $tmpfile, $out, $int);
				//print("int ==> ".$int);
				exec("file -bi " . $tmpfile, $out);

				foreach ($regex as $key => $value) {
					preg_match($value, $out[0], $matches);						
						if ($matches) {
							$return = $matches;
						}
				}
				if (!$return) {
					$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">file type not allowed</p>";
					$returnArray[1] = "false mime type";
				} else {					
					if(move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dest)) {
						chmod($dest, 0440);
						$returnArray[0] = $this->errormsg = "<p class=\"errormsg\" style=\"color:green;\">The file ".basename( $_FILES[$fieldName]['name'])." has been uploaded</p>";
						$returnArray[1] = $filename;
						return $returnArray;
					} else {
						$returnArray[0] = $this->errormsg = "<p class=\"errormsg\">There was an error uploading the file, please try again!</p>";
						$returnArray[1] = "upload error";
						return $returnArray;
					}
				}
			}
		}
		return $returnArray;
	}
	

	function getDataFromDB($scriptname=FALSE, $limit=FALSE, $sort=FALSE, $fields=FALSE, $where=FALSE, $table=FALSE) {
		$script = "";
		if (!$scriptname) {
			$tmp = $this->baseDir();
			$script = $tmp['file'];
		} else {
			$script = $scriptname;
		}
		if (!$limit) $limit = "";
		else $limit = " LIMIT ".$limit;
		if (!$sort) $sort = "uid";
		if (!$where) $where = "";
		else $where = " AND ".$where;
		$stringOFields = "";
		$dummy = "";
		$entries = "";
		$out = "";
		$outslim = "";
		$html = "";
		$return = array();
		$returnvalues = array();
		$format = '%d-%m-%Y';
		if (!$fields) {
			$dummy = $this->makeFields();
			$pieces = explode(", ", $dummy[1]);
			$no = count($pieces);
			for ($i=0;$i<$no-1;$i++) {
				$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
				$stringOFields .= "$trimmed, ";
			}			
			$fields = $stringOFields;
			$fields = $this->truncateReturn($stringOFields, 2); // substr($stringOFields, 0, -2);
		}
		if (!$table) $table = $this->tableName . $this->tableSuffix;
		//echo "table = ".$table;
		$query = "SELECT `uid`, `crdate`, `tstamp`, ";
		$query .= $fields;
		$query .= " FROM `".$table."` WHERE `deleted`= 0 $where ORDER BY $sort $limit";
		//echo "<p>The getDataFromDB query ".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					//generate csv and download link
					$fh = fopen($table.'.csv', 'w');
					if($result = $sql_connect->query($query)) {
						$rowcvs = $result->num_rows;
						$colcvs = $result->field_count;
						$resultcsv = $result->fetch_assoc();
						// put field names (headline)
						fputcsv($fh, array_keys($resultcsv));
						$result = $sql_connect->query($query);
						// put data from table into csv					
						date_default_timezone_set('CET');
						while($resultcsv = $result->fetch_assoc()) {
							foreach ($resultcsv as $key => $value) {
								//if($key == "uid") echo $value;
								if ($key === "geb" OR $key === "bew_ende" OR $key === "bew_beginn" OR $key === "f_foerd_beginn" OR $key === "f_foerd_ende") {
									$format = "%d.%m.%Y";
									$date = strftime($format, $value);
									$resultcsv[$key] = $date;
								//echo "<p>key: ".$key."  -> raw value: ".$value." / formatted: ".$date."</p>";
								} elseif ($key === "crdate" OR $key === "tstamp") {
									$format = "%d-%m-%Y @ %H:%M:%S";
									$datetime = strftime($format, $value);
									$resultcsv[$key] = $datetime;
								} else $resultcsv[$key] = $value;
							}
//						echo "<pre>";
//						print_r($resultcsv);
//						echo "</pre>";
						if (true) fputcsv($fh,$resultcsv);
						}						
						fclose($fh);
						$entries = $rows;
						$filename = $table . ".csv";
						// used in list view
						$out = "<div class=\"download\"><p>Download as csv file: <a class=\"download\" href=\"{$filename}\">{$filename}</a></p>\n";
						$out .= "<table class=\"download\">\n<tr>\n<th>Information</th><th>Total Count</th>\n</tr>\n<tr>\n<td>N<sub>entries</sub></td><td>".$entries."</td>\n</tr>\n<tr>\n<td>N<sub>fields</sub></td><td>".$cols."</td>\n</tr>\n</table>";
						$out .= "<!-- Rows: ".$rows."<br />Columns: ".$cols." --></p></div>\n";
						// used in single entry view
						$outslim = "<div><p>Download as csv file: <a class=\"download\" href=\"{$filename}\">{$filename}</a></p>\n";
						$outslim .= "<p>The total number of entries: N<sub>total</sub> = ".$entries."<!-- Rows: ".$rows."<br />Columns: ".$cols." --></p>\n";
						$outslim .= "<p>The total number of fields: N<sub>fields</sub> = ".$cols."</p></div>\n";
						// end csv
						$return['entries'] = $entries;
						$return['html'] = $out;
						$return['htmlslim'] = $outslim;
					} else {
						$out = "csv gerneration failed!".mysqli_connect_error();
						$entries = 0;
					}
					// generate html table
					$html = "<table id=\"results\">\n";
					if($result = $sql_connect->query($query)) {
						$input = $result->fetch_assoc();
						$header = array_keys($input);
						$html .= "<tr>";
						for ($i=0; $i<$cols; $i++) {
							$str = $header[$i];
							$h = strtoupper($str);
							if ($i == 0) {
								$html .= "<th class=\"uid\">$h</th>";
							} elseif (0 < $i AND $i <= 2) {
								$html .= "<th class=\"datetime\">$h</th>";
							} elseif ($header[$i]) {
								$html .= "<th class=\"dob\">$h</th>";
							} else 	$html .= "<th>$h</th>";
						}
						$html .= "</tr>\n";
						$result = $sql_connect->query($query);
						date_default_timezone_set('CET');
						$k = 0;
						while ($row = $result->fetch_assoc()) {
							$k++;
							if ($k % 2 == 0) {
								$html .= "<tr class=\"even\">";
							} elseif ($k % 2 == 1) {
								$html .= "<tr class=\"odd\">";
							} else $html .= "<tr>";
							for ($i=0; $i<$cols; $i++) {
								if ($header[$i]==="geb" OR $header[$i]==="bew_beginn" OR $header[$i]==="bew_ende" OR $header[$i] === "f_foerd_beginn" OR $header[$i] === "f_foerd_ende") {
									$format = "%d.%m.%Y";
									$str = $row[$header[$i]];
									$str = strftime($format, $str);
								//echo "<p>key: ($i) ".$header[$i]."  -> raw value: ".$row[$header[$i]]." / formatted: ".$str."</p>";
									$html .= "<td class=\"date\">".$str."</td>";
								} elseif ($header[$i]==="crdate" OR $header[$i]==="tstamp") {
									$format = "%d-%m-%Y @ %H:%M:%S";
									$str = $row[$header[$i]];
									$str = strftime($format, $str);
									$html .= "<td>".$str."</td>";
								} elseif ($header[$i]==="datei_url") {
									$url = $row[$header[$i]];
									if (strpos($url,"no file ") == 1) $html .= "<td>".$url."</td>";
									else $html .= "<td><a target=\"_blank\" href=\"".$this->uploadPath.$url."\">".$url."</a></td>";
								} else {
									if ($i == 0) {
										$id = $row[$header[$i]];										
										$html .= "<td><a href=\"$script?id=$id\">".$id."</a></td>";
									} else {
										$html .= "<td>".$row[$header[$i]]."</td>";
									}
								}
								$varname = $header[$i];
								$$varname = $row[$header[$i]];
								$returnvalues[$varname] = $$varname;								
							}
							$html .= "</tr>\n";
						}
						$html .= "</table>\n";
						
						$sql_connect->close();
					} else $html .= "Empty result"; // end html table
				} else {
					$out = "<p class=\"errormsg\">No data available!</p>";
					$entries = 0;
				}
			} else {
				$out = "<p>Table empty: no .csv file!</p>";
				$entries = 0;
			}
		}
		$return['entries'] = $entries;
		$return['html'] = $out;
		$return['htmlslim'] = $outslim;
		$return['table'] = $html;
		return $return;
	}
	
	function getIndividualVal($uid, $fields=FALSE, $table=FALSE) {
		$return = array();
		$stringOFields = "";
		if (!$table) $table = $this->tableName . $this->tableSuffix;
		if (!$fields) $stringOFields = "";
		else {
			$pieces = explode(", ", $fields);
			$no = count($pieces);			
			for ($i=0;$i<$no;$i++) {
				$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
				$stringOFields .= "`$trimmed`, ";
				$$trimmed = $trimmed;
			}
		}
		$query = "SELECT `uid`, `crdate`, `tstamp`, `hidden`, `deleted`, `edited`, ";
		$query .= $stringOFields;
		$query .= " `pid`, `fid` FROM `".$table."` WHERE `uid`= ".$uid." ORDER BY `uid` ASC"; //AND `deleted` = 0
		//echo "<br>".$query."</br>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				$row = $result->fetch_assoc();
				if ($fields AND $rows > 0) {
					$return['uid'] = $row['uid'];
					$return['crdate'] = $row['crdate'];
					$return['tstamp'] = $row['tstamp'];
					$return['hidden'] = $row['hidden'];
					$return['deleted'] = $row['deleted'];
					$return['edited'] = $row['edited'];
					$pieces = explode(", ", $fields);
					$no = count($pieces);
					for ($i=0;$i<$no;$i++) {
						$trimmed = trim($pieces[$i]);
						$$trimmed = $row[$trimmed];
						$return[$trimmed] = $$trimmed;
					}
						$return['pid'] = $row['pid'];
						$return['fid'] = $row['fid'];
				} else {} // what to do if '$fields' is empty
			}
		}
		return $return;
	}
	
	function getValuesFromDB($fields=FALSE, $table=FALSE) {
		$stringOFields = "";
		$dummy = "";
		$out = "";
		$max = 0;
		$return = array();
		$returnvalues = array();
		$format = '%Y-%m-%d';
		if (!$fields) {
			$dummy = $this->makeFields();
			$pieces = explode(", ", $dummy[1]);
			$no = count($pieces);
			for ($i=0;$i<$no-1;$i++) {
				$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
				$stringOFields .= "$trimmed, ";
			}
			$fields = $this->truncateReturn($stringOFields, 2); // substr($stringOFields, 0, -2); // remove last comma from field name
		}
		if (!$table) $table = $this->tableName . $this->tableSuffix;
		$query = "SELECT `uid`, ";
		$query .= $fields;
		$query .= " FROM `".$table."` ORDER BY uid ASC"; //WHERE `deleted` = 0 
		//echo "<br>The getValuesFromDB query ".$query."</br>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				//$result = $sql_connect->query($query);
				if($rows > 0) {			
					if($result = $sql_connect->query($query)) {
						$input = $result->fetch_assoc();
						$header = array_keys($input);				
						$result = $sql_connect->query($query);
						$i = 0; //$j = 0; use the actual uid's of the entries
						while ($row = $result->fetch_assoc()) {
							$j = $row['uid'];				
							for ($i=0; $i<$cols; $i++) {								
								$varname = $header[$i];
								$$varname = $row[$header[$i]];								
								$returnvalues[$j][$varname] = $$varname;
							}
						}
						$sql_connect->close();
						$out = "<p>All is good, values</p>";					
						$max = $rows;					
					} else {
						$out = "<p>2nd query failed!</p>";
						$returnvalues = -2;
						$max = 0;
					}
				} else {
					$out = "<p>Table contains no entries! getValuesFromDB</p>";
					$returnvalues = 0;
					$max = 0;
				}
			} else {
				$out = "<p>1st query failed!</p>";
				$returnvalues = -1;
				$max = 0;
			}
		}
		$return['values'] = $returnvalues;
		$return['max'] = $max;
		$return['msg'] = $out;
		return $return;
	}
	
	function fillFormValues($id,$variables) {
		$format = '%Y-%m-%d';
		$array_val = array();
		if ($variables[$id]) {
			foreach ($variables[$id] as $key => $value) {
				$array_val[$key] = $value;
			}
			return $array_val;
		} else {
			$out = "<p>There are no entries in the table ".$tableName.$suffix."!</p>";
			return $out;
		}
	}
	
	// admin form math work out
	// group 1
	function durchschn_grp1($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT `uid`, `gruppe`, (`fremdsprache`+`mathe`+`deutsch`)/3 AS `avg` FROM `$this->tableName` WHERE `gruppe` = 1".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {	
					while ($row = $result->fetch_assoc()) {
							$update = "UPDATE $this->tableName SET `durchschn_kern` = ".$row['avg']." WHERE `uid` = ".$row['uid'].";";
							//echo "<p>".$update."</p>";
							$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}				
	}
	
	function ausgangsn_grp1($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT `uid`, `gruppe`, (`durchschn_kern`+`hzb`)/2 AS `avg` FROM $this->tableName WHERE `gruppe` = 1".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ausgangsnote` = ".$row['avg']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	// group 2
	function ausgangsn_grp2($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT  `uid`, `gruppe`, `studleistungen_b` AS `avg` FROM $this->tableName WHERE `gruppe` = 2".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ausgangsnote_b` = ".$row['avg']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
	function ects_grp2($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT `uid`, `gruppe`, (`soll_ects_b`-`ects_b`) AS `diff` FROM $this->tableName WHERE `gruppe` = 2".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ects_abw_b` = ".$row['diff']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
	
	// group 3
	function ausgangsn_grp3($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT  `uid`, `gruppe`, `note_bachelor_c` AS `avg` FROM $this->tableName WHERE `gruppe` = 3".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ausgangsnote_c` = ".$row['avg']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
		
	// group 4
	function ausgangsn_grp4($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT  `uid`, `gruppe`, (`note_bachelor_d`+`studleistungen_d`)/2 AS `avg` FROM $this->tableName WHERE `gruppe` = 4".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ausgangsnote_d` = ".$row['avg']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
	function ects_grp4($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$query = "SELECT `uid`, `gruppe`, (`soll_ects_d`-`ects_d`) AS `diff` FROM $this->tableName WHERE `gruppe` = 4".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `ects_abw_d` = ".$row['diff']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
	// grades 'n bonuses
	function note_rangliste($letter=FALSE,$id=FALSE) {
		if (!$id) $where = ";";
		else $where = " AND `uid` = $id;";
		$grp = 0;
		if ($letter) {
			switch ($letter) {
				case 'b': $grp = 2;
				break;
				case 'c': $grp = 3;
				break;
				case 'd': $grp = 4;
				break;
			}
			$l = "_".$letter;
			} else {
			$grp = 1;
			$l = "";
		}
		$query = "SELECT `uid`, `gruppe`, (`ausgangsnote$l`-`bonus_verfahren`) AS `noterang` FROM $this->tableName WHERE `gruppe` = ".$grp.$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `note_rangliste` = ".$row['noterang']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
	
	function bonus_gesamt($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " WHERE `uid` = $id;";
		$query = "SELECT  `uid`, `gruppe`, (`bonus_preis` + `bonus_beruf_ausbildung` + `bonus_praktika` + `bonus_ges_pol_eng` + ";
		$query .= "`bonus_hs_eng` + `b_kind` + `b_krankheit` + `b_mitarbeit_betrieb` + `b_pflege_anghoeriger` +  ";
		$query .= "`b_fam_hintergrund`) AS `sum`,  `bonus_gesamt` , `bonus_verfahren` FROM  $this->tableName".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$update = "UPDATE $this->tableName SET `bonus_gesamt` = ".$row['sum']." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}

	function bonus_verfahren($id=FALSE) {
		if (!$id) $where = ";";
		else $where = " WHERE `uid` = $id;";
		$query = "SELECT  `uid`, `gruppe`, `bonus_gesamt` AS `total_b`, `bonus_verfahren` FROM  $this->tableName".$where;
		//echo "<p>".$query."</p>";
		$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
		$sql_connect->set_charset($this->charset);
		if (mysqli_connect_error())
		die("Cannot connect to database! ");
		else {
			if($result = $sql_connect->query($query)) {
				$rows = $result->num_rows;
				$cols = $result->field_count;
				if($rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$b = $row['total_b'];
						if ($b > 0.6) $b = 0.60;
						$update = "UPDATE $this->tableName SET `bonus_verfahren` = ".$b." WHERE `uid` = ".$row['uid'].";";
						//echo "<p>".$update."</p>";
						$execute = $sql_connect->query($update);
					}
				} else echo "no rows";
			}
		}
	}
}
