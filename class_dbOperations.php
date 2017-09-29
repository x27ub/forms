<?php 
//define('__ROOT__', (dirname(__FILE__)));

require_once(__ROOT__.'/class_dbConfig.php');
require_once(__ROOT__.'/class_auxiliary.php');

class dbOperations extends dbConnection {
	//protected static $params;
	protected static $inputArray;
	protected static $tableName;
	// default values
	protected static $varArray;
	protected static $paramArray;
	public static $messg;
	public static $messgDisplay;
	protected static $errormsg = "<div class=\"error\"><p class=\"errormsg\">ERROR: no configuration array supplied!<p></div>";
	protected static $instance;

	var $format = '%d/%m/%Y %H:%M:%S';
	var $uploadPath;
	var $charset = "utf8";
	var $ip;
	/* 
	 * delimiter for multiple values for select or checkbox form fields,
	 * if no serialization is wanted
	 */
	protected static $delimiter = ",";

	function __construct($params, $inputArr, $tablename, $variables) {
		
		parent::__construct($params);
		print parent::$message;
//		echo "<p>table name $tablename</p>";
		
		$vA = array(
			"uploadPath"=>"/",
			"prefix"=>"tx", // for use within a TYPO3 db use tx or tt
			"suffix"=>"01",
			"salt"=>"I|1Tofu+-Wev>w.W=S\$Y+5$44HC;[<",
			"pid"=>0,
			"fid"=>1);
		
		if (empty($inputArr)) {
			print self::$errormsg;
			exit("Stop here.");
		} else {
			self::$inputArray = $inputArr;
		}
		if (!empty($params)) {
			self::$paramArray = $params;
// 			foreach($params as $key => $value) {
// 				//$this->$key = $value;
// 				$key = $value;
// 			}
		} else {
			self::$paramArray = parent::$dbConfig;
// 			foreach($params as $key => $value) {
// 				$key = $value;
// 			}
		}
		($variables) ? self::$varArray = $variables : self::$varArray = $vA; // default value
		($tablename) ? self::$tableName = $tablename : self::$tableName = "formTable";  // default value
		self::$tableName = self::$varArray['prefix'] . "_" . self::$tableName;
		self::$tableName = self::$tableName . "_" . self::$varArray['suffix'];
		self::$messg = (isset(self::$messg) ? self::$messg : '');
// 		if (!empty($variables)) {
// 			echo "filled";
// 			foreach($variables as $key => $value) {
// 				self::$varArray[$key] = $value;
// 			}
// 			self::$varArray = $variables;
// 		} else {
// 			echo "empty";
// 			self::$varArray = $vA;
// 		}

// 		print ("\n<pre>params = ");
// 		print_r(self::$paramArray);
// 		print ("</pre>\n");
// 		print ("\n<pre>variables = ");
// 		print_r(self::$varArray);
// 		print ("</pre>\n");
// 		echo "<p>constructor = ". self::$tableName ."</p>";
	}

	public static function makeInstance($params, $inputArr, $tablename=FALSE, $vars=FALSE){
		$var = (!$vars ? self::$varArray : $vars);
		$table_name = ($tablename ? $tablename : self::$tableName);
		if (is_null( self::$instance )){
			self::$instance = new self($params, $inputArr, $table_name, $var);
		}
		return self::$instance;
	}
	
	/* 
	 * produce the db fields from form's fields name
	 * put the posted values in an array
	 */
	
	public static function makeFields() {
		$vars = self::$varArray;
		$table_fields = '';
		$entry = '';
		$submitFieldName = '';
		$pwFieldName = '';
		$dateFieldName = '';
		$theUpfieldName = '';
		$upFieldName = array();
		$formFieldValue = array();
		$selectedValues = array();
		$postvar = array();
		$form_array = self::$inputArray;
		$s=$j=$l=$m=$r=$t=0;
		while ($formArray = each($form_array)) {
			$fieldset = $formArray['key'];
			$j++;
			$s=0;
//			print "<p>j=$j fieldset ".$fieldset."</p>";
			while ($formConf = each($form_array[$fieldset])) {
				$l++;
				$label = $formConf['key'];
//				print "<p>l=$l label ".$label."</p>";
				while ($config = each($form_array[$fieldset][$label])) {
					$thetype = $config['key'];
					$thevalue = $config['value'];
					$s++;
// 					print "<p>s=$s thetype/thevalue <pre>";
// 					print_r($thetype);
// 					print "</pre> / <pre>";
// 					print_r($thevalue);
// 					print "</pre></p>";
					$i = 0;
					while ($type = each($form_array[$fieldset][$label][$thetype])) { // 4th while
						$inputKey = $selectKey = $textareKey = $typeKey = $nameKey = $dbkey = 0;
						$nameFields = $type['key'];
						$formFields = $type['value'];
						$m++;
// 						print "<p>m=$m: type = <pre>";
// 						print_r($type);
// 						print "</pre></p>";
// 						echo "<p>namefields/formFields = ".$nameFields." / <pre>";
// 						print_r($formFields);
// 						echo "</pre></p>";
						$inputKey = auxiliary::array_key_check($form_array[$fieldset][$label], 'input');
						$selectKey = auxiliary::array_key_check($form_array[$fieldset][$label], 'select');
						$textareaKey = auxiliary::array_key_check($form_array[$fieldset][$label], 'textarea');
						$typeKey = auxiliary::array_key_check($form_array[$fieldset][$label][$thetype], 'type');
						$nameKey = auxiliary::array_key_check($form_array[$fieldset][$label][$thetype], 'name');
						$dbkey = auxiliary::array_key_check($form_array[$fieldset][$label][$thetype], '@#db');
											
						// avoid 'undefined indexes' errors
						$checkPostVar = ((!$_POST) ? FALSE : TRUE);
						$checkFilesVar = ((!$_FILES) ? FALSE : TRUE);
						if ($inputKey AND $dbkey) { // <input ...	key 'submit' replace by variable
							$r++;
//							echo "<p>r = $r: </p>";
							if ($typeKey) { // <input type ...
								$t++;
//								echo "<p>t = $t: </p>";
								if ($formFields === 'submit') { // <input type="submit" ...
									// get name of submit field
									$submitFieldName = $form_array[$fieldset][$label][$thetype]['name'];
									//echo "<p>".$submitFieldName."</p>";
								} elseif ($formFields === 'password') { // <input type="password" ...
									// get name of password field
									$pwFieldName = $form_array[$fieldset][$label][$thetype]['name'];
								} elseif ($formFields === 'date' OR  $formFields === 'time' OR  $formFields === 'datetime' 
											OR  $formFields === 'datetime-local' OR $formFields === 'month' 
												OR $formFields === 'week') { // <input type="date/datetime/..." ...
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
										$table_fields .= " `$formFields` ";
//										echo "<p>pwfield: ".$formFields."</p>";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkPostVar) {
											$formFieldValue[$pwFieldName] = addslashes(htmlspecialchars($_POST[$formFields]));
											$passwd = addslashes(htmlspecialchars($_POST[$formFields])); //$_POST[$pwFieldName];
											$postvar[$formFields] = crypt($passwd,$vars['salt']);
										}
									} elseif ($form_array[$fieldset][$label][$thetype]['name'] === $dateFieldName) {
										$table_fields .= " `$formFields` ";
//										echo "<p>datefield: ".$formFields."</p>";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkPostVar) {
											$formFieldValue[$dateFieldName] = addslashes(htmlspecialchars($_POST[$formFields]));
											$dateTime = addslashes(htmlspecialchars($_POST[$formFields])); //$_POST[$datetFieldName];
											$postvar[$formFields] = strtotime($dateTime);
										}
									 } elseif ($form_array[$fieldset][$label][$thetype]['name'] === $theUpfieldName) {
										//echo "<br>".$formFields."<br>";
										$table_fields .= " `$formFields` ";
//										echo "<p>upfield: ".$formFields."</p>";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkFilesVar) {
											$formFieldValue[$theUpfieldName] = $_FILES[$theUpfieldName]['name'];
											//if (auxiliary::array_key_check($_POST, $formFields)) {
											$file = $_FILES[$theUpfieldName]['name'];
											$postvar[$formFields] = addslashes(htmlspecialchars($file));
										}
									} elseif ($form_array[$fieldset][$label][$thetype]['name'] === $submitFieldName) {
										$table_fields .= " `$formFields` ";
										$entry .= " `$formFields`, ";
										$postvar[$formFields] = $submitFieldName;
									} else {
										$table_fields .= " `$formFields` "; // create db table field
//										echo "<p>??: ".$formFields."</p>";
										$entry .= " `$formFields`, "; // create db table entry
										if ($checkPostVar) {
											$formFieldValue[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
											$postvar[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
										}
									}
								} elseif ($nameFields === '@#db') {
									$table_fields .= " $formFields,"; // needed to create db table only
//									echo "<p>@#db ".$formFields."</p>";
								} else {
									$table_fields .= ''; // all other fields \neq 'name' of <input type="text/radio/chechbox" ... else?
//									echo "<p>else ".$formFields."</p>";
									$entry .= '';
								}
							// if($typeKey)
							} 
// 							elseif ($typeKey === 'file' AND $dbkey) { 
								// needed or not? !$typeKey === 'file'
// 							} else { // <input .... without any type declaration
// 								// do nothing
// 								$table_fields .= '';
// 								$entry .= '';
// 							}
						// if ($inputKey)
						} elseif ($textareaKey) { // <select ... OR <textarea ...
							if ($nameFields === 'name') {
								// get values from name fields only
								$table_fields .= " `$formFields` "; // create db table field
//								echo "<p>textarea: ".$table_fields."</p>";
								$entry .= " `$formFields`, "; // create db table entry
								if ($checkPostVar) {
									$formFieldValue[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
									$postvar[$formFields] = addslashes(htmlspecialchars($_POST[$formFields]));
								}
							} elseif ($nameFields === '@#db') {
								$table_fields .= " $formFields,"; // needed to create db table only
//								echo "<p>@#db textarea ".$formFields."</p>";
							} else {
								$table_fields .= ''; // all other fields \neq 'name' of <input type="text/radio/chechbox" ... else?
//								echo "<p>else textarea".$table_fields."</p>";
								$entry .= '';
							}
						} elseif ($selectKey) {
							$selectedVal = '';
							if ($nameFields === 'name') {
								$table_fields .= " `$formFields` ";
//								echo "<p>select ".$table_fields."</p>";
								$entry .= " `$formFields`, ";
								// need to get the selected values from form fields like 'select' with optgroup
								if ($checkPostVar) {
									if (is_array($_POST[$formFields])) { // i.e 'name' is an array
										foreach ($_POST[$formFields] as $key => $value) {
											$selectedVal .= auxiliary::secureit($value). self::$delimiter ." ";
											$_POST[$formFields][$key] = auxiliary::secureit($value);
										}
										$selectedVal = auxiliary::truncateReturn($selectedVal, 2);
										$selectedValues[$formFields][] = $selectedVal;
									} else {
										$selectedVal = auxiliary::secureit($formFields);
									}
									$formFieldValue[$formFields] = $_POST[$formFields];
									$postvar[$formFields] = $selectedVal;
								}
							} elseif ($nameFields === '@#db') {
								$table_fields .= " $formFields,"; // needed to create db table only
//								echo "<p>@#db select ".$table_fields."</p>";
							} else {
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
// 		print ("\n<pre>tableInfo[] = ");
// 		print_r($tableInfo);
// 		print ("</pre>\n");
		return $tableInfo;
	}
	
	/*
	 *  create table with form field names and given MySQL field definitions
	 *  when dbOperations::makeInstance($dbpm, $loginFormArray, "users")->createTable("login");
	 *  $login = "login", the table login_attempts (and more if needed) is also created
	 */
	
	public static function createTable($login=FALSE) {
		$retmsg ="";
		$exists = -1;
		$dbparams = self::$paramArray["dbparams"];
		//		echo "dbparams = ". $dbparams;
		$theTableName = self::$tableName;
		// 	if (self::$varArray["prefix"]) $theTableName = self::$varArray["prefix"] . "_";
		// 	$theTableName .= self::$tableName;
		// 	if (self::$varArray["suffix"]) $theTableName .= "_" . self::$varArray["suffix"];
		$fields = self::makeFields();
		$conn = parent::mySQLconnection();
		//$conn->set_charset($dbparams);
		// check if a table already exists
		$checkTable = "SHOW TABLES LIKE '".$theTableName."'";
		if (!($conn->errno === 0)) {
			$retmsg .= "<div class=\"error\"><p class=\"errormsg\">(check tbl) Cannot connect to database! </p>" . $conn->error . ".</div>";
			self::$messg .= $retmsg;
			//return self::$messg;
			die("<div class=\"error\"><p class=\"errormsg\">d:(check tbl) Cannot connect to database! </p>" . $conn->error . ".</div>");
		} else {
			$stmt = $conn->prepare($checkTable);
			$stmt->execute();
			$stmt->store_result();
			$exists = $stmt->num_rows;
			if($exists !== 1) $exists = 0;
			$stmt->close();
			$conn->close();
		}
		// create table
		$query = "CREATE TABLE IF NOT EXISTS `";
		$query .= $theTableName;
		$query .= "` (`uid` INT( 11 ) UNSIGNED AUTO_INCREMENT PRIMARY KEY, `crdate` INT( 11 ) UNSIGNED NOT NULL, `tstamp` INT( 11 ), `hidden` TINYINT( 1 ) NOT NULL DEFAULT '0', `deleted` TINYINT( 1 ) NOT NULL DEFAULT '0', `sid` TINYTEXT NOT NULL,";
		//$query .= "` (`uid` INT( 11 ) UNSIGNED AUTO_INCREMENT PRIMARY KEY, `crdate` INT( 11 ) UNSIGNED NOT NULL, `tstamp` INT( 11 ), `sid` VARCHAR (255)  NULL,";
		$query .= $fields[0];
		$query .= " `pid` INT( 11 ) NOT NULL, `fid` VARCHAR( 255 )";
		$query .= ") ".$dbparams.";";
		if ($login === "login") {
			$a_tableName = array();
			$a_tableName[0] = $theTableName;
			$a_tableName[1] = "login_attempts";
			$a_tableName[2] = "admin_users";
			$a_query = array();
			$a_query[0] = $query;
			$a_query[1] = "CREATE TABLE IF NOT EXISTS `" . $a_tableName[1] . "` (`user_id` INT(11) NOT NULL, `time` VARCHAR(30) NOT NULL) ENGINE=InnoDB;";
			$a_query[2] = "CREATE TABLE IF NOT EXISTS `" . $a_tableName[2] . "` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `username` VARCHAR(30) NOT NULL, `email` VARCHAR(50) NOT NULL, `password` CHAR(128) NOT NULL) ENGINE=InnoDB;";
			//echo  "<pre>";
			//print_r($a_query);
			//echo "</pre>";
			$counter = 0;
			foreach ($a_query as $key => $vquery) {
				$counter++;
				//print "key = " . $key . "<br> counter = " . $counter . "<br>";
				$conn = parent::mySQLconnection();
				if ($conn->errno) {
					$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Cannot connect to database! </p>" . $conn->error . ".</div>";
					self::$messg .= $retmsg;
					die($retmsg);
				} else {
					$stmt = $conn->prepare($vquery);
					//echo  "<pre>";
					//print_r($stmt);
					//echo "</pre>";
					if($stmt->errno) {
						$retmsg = "<div class=\"error\"><p class=\"errormsg\">Cannot create table: </p>" . $conn->error . ".</div>";
						self::$messg .= $retmsg;
						die ($retmsg);
					} else {
						$stmt->execute();
						if ($exists == 0) {					
							if ($counter > 1) $retmsg = str_replace($a_tableName[$key-1], $a_tableName[$key], $retmsg);
							else $retmsg .= "<div class=\"success\"><p class=\"successmsg\">Table `" . $a_tableName[$key] . "` successfully created.</p></div>";
							self::$messg .= $retmsg;
						} else {
							if ($counter > 1) $retmsg = str_replace($a_tableName[$key-1], $a_tableName[$key], $retmsg);
							else $retmsg .= "<p>createTable(): table `" . $a_tableName[$key] . "` exists</p>";
							self::$messg .= $retmsg;
						}
						$stmt->close();
						$conn->close();
					}
				}
			}
		} else {
			$conn = parent::mySQLconnection();
			if ($conn->errno) {
				$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Cannot connect to database! </p>" . $conn->error . ".</div>";
				self::$messg .= $retmsg;
				die($retmsg);
			} else {
				$stmt = $conn->prepare($query);
				//var_dump($stmt);
				//if($stmt->errno) die("<div class=\"error\"><p class=\"errormsg\">Cannot create table: </p>" . $conn->error . ".</div>");
				//echo  "<pre>";
				//print_r($stmt);
				//echo "</pre>";
				if($stmt->errno) {
					$retmsg = "<div class=\"error\"><p class=\"errormsg\">Cannot create table: </p>" . $conn->error . ".</div>";
					self::$messg .= $retmsg;
					die ($retmsg);
				} else {
					$stmt->execute();
					if ($exists == 0) {
						$retmsg .= "<div class=\"success\">
								<p class=\"successmsg\">Table `" . $theTableName . 
									"` successfully created.</p></div>";
						self::$messg .= $retmsg;
					} else {
						$retmsg .= "<p>createTable(): the table `$theTableName` exists</p>";
						self::$messg .= $retmsg;
					}
					$stmt->close();
					$conn->close();
				}
			}
		}
		return self::$messg;
	}

	/*
	 * inserts values from form fields but the serialized array that's coming from the
	 * argument variable | what default value if argument and form field 's_array' are empty?
	 */
	
	public static function insert($val=FALSE) {
		$retmsg ="";
		($val) ? $values = $val : $values = array('s_array'=>'no value given');  // what default to use?
// 		print ("\n<pre>values = ");
// 		print_r($values);
// 		print ("</pre>\n");
		($val['pid']) ? $values['pid'] = $val['pid']  : $values['pid'] = 0;
		($val['fid']) ? $values['fid'] = $val['fid']  : $values['fid'] = 1;
		$pid = $values['pid'];
		$fid = $values['fid'];
		$theTableName = self::$tableName;
// 		if (self::$varArray["prefix"]) $theTableName = self::$varArray["prefix"] . "_";
// 		//$theTableName .= (!$table_name) ? self::$tableName : $table_name;
// 		$theTableName .= self::$tableName;
// 		if (self::$varArray["suffix"]) $theTableName .= "_" . self::$varArray["suffix"];
		$crdate = time();
		$tstamp = strtotime('now');
		$sid = (isset($_POST['checksid']) ? $_POST['checksid'] : 0); // (isset($_SESSION['sid']) ? $_SESSION['sid'] : -1);
		$sessid = (isset($_POST['checksid']) ? $_POST['checksid'] : 0);
		$checksid;
		//echo "<p>sid = $sid</p>";
		$fields = self::makeFields();
		// $entry ends with comma whitespace ", "
		$rtrimFields = rtrim($fields[1],", ");
		$pieces = explode(", ", $rtrimFields);
		$bindParams = array();
		$no = count($pieces);
		$bindstr = "";
// 		echo "<p>$no / pieces = <pre>";
// 		print_r($pieces);
// 		echo "</pre></p>";
		// check if a table exists
		$checkTable = "SHOW TABLES LIKE '".$theTableName."'";
		//echo $checkTable;
		$conn = parent::mySQLconnection();
		if ($conn->errno) {
			$retmsg .= "<div class=\"error\"><p class=\errormsg\">(check tbl) Cannot connect to database! </p>" . $conn->error ."</div>";
			self::$messg = $retmsg;
			//return self::$messg;
			die($retmsg);
		} else {
			//$tblexists = $conn->query($checkTable);
			$stmt = $conn->prepare($checkTable);
			$stmt->execute();
			$stmt->store_result();
			$numRows = $stmt->num_rows;
			if($numRows == 1) {
				$checksid = 1;
				$retmsg .= "<div class=\"success\"><p class=\"successmsg\">checksid = $checksid => OK table exists.</p></div>";
				self::$messg = $retmsg;
				$stmt->close();
				$conn->close();
			} else {
				$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Insert stmt. failed, table doesn't exist!";
				$retmsg .= "Create table first with\n <pre>dbOperations::makeInstance(\$params, \$configArrays,";
				$retmsg .= " \"tableName\", \$varsArr)->createTable();</pre></p></div>";
				self::$messg = $retmsg;
				die($retmsg);
			}
		}
		// check if entry exists
// 		$checkEntry = "SELECT `uid`, `sid` FROM " . $theTableName . " WHERE `sid` = '" . $sessid . "';";
// 		//echo $checkEntry;
// 		$conn = parent::mySQLconnection();
// 		if ($conn->errno) {
// 			$retmsg .= "<div class=\"error\"><p class=\errormsg\">(check session) Cannot connect to database! </p>" . $conn->error ."</div>";
// 			self::$messg = $retmsg;
// 			//return self::$messg;
// 			die($retmsg);
// 		} else {
// 			$stmt = $conn->prepare($checkEntry);
// 			$stmt->execute();
// 			$stmt->store_result();
// 			$numRows = $stmt->num_rows;
// 			if($numRows == 1 OR $numRows > 1) {
// 				$checksid = 1;				
// 				$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Form already submitted, entry exists.</p></div>";
// 				self::$messg = $retmsg;
// 				//return self::$messg;
// 			}
// 			else {
// 				$checksid = 0;
// 				//echo "<div class=\"success\"><p class=\"successmsg\">New entry is OK.</p></div>";
// 			}
// 			$stmt->close();
// 			$conn->close();
// 		}
		// insert statment
		$query = "INSERT INTO ".$theTableName." (`crdate`,  `tstamp`, `hidden`, `deleted`, `sid`, ";
		$query .= $fields[1];
		$query .= " `pid`, `fid`) ";
		//$query .= "VALUES ($crdate, $tstamp, 0, 0, '".$sid."', '" . $values['title'] . "', '" . $values['varname'] . "', '" . $values['serial'] . "', '" . $values['descr'] . "',  1, 1);";
		//$query .= "VALUES ($crdate, $tstamp, 0, 0, '".$sid."', ?, ?, ?, ?,  1, 1);";
		$query .= "VALUES ($crdate, $tstamp, 0, 0, '".$sid."', ";
		for ($i=1; $i <= $no; $i++) {
			$val_i = $pieces[$i-1];
			$val_i = trim($val_i, ' `');
			$query .= "?, ";
			$bindstr .= "s";
			$bindParams[0] = & $bindstr;
			
			//($_POST[$val_i]) ? $bindValues[$i] = $_POST[$val_i] : $bindValues = $values[$val_i]; //doesn't work, why?
			/*
			 * if a submit button from a form config array is not used
			 */
			$postVar_i = (isset($_POST[$val_i]) ? $_POST[$val_i] : FALSE);
// 			print "<p>$val_i</p>";			
// 			echo "<p> array: ";
// 			var_dump($postVar_i);
// 			echo "</p>";
			if ($val_i AND $postVar_i) {
				if (is_array($_POST[$val_i])) {
					$bindValue[$i] = serialize($_POST[$val_i]);  // the argument $val must use the same key as $_POST
					$bindParams[$i] = & $bindValue[$i];
				} else {
					$bindValue[$i] = auxiliary::secureit($_POST[$val_i]);
					$bindParams[$i] = & $bindValue[$i];
				}
			} else {
				$bindValue[$i] = $values[$val_i];
				$bindParams[$i] = & $bindValue[$i];
			}
		}
		print ("\n<pre>bindparam = ");
		print_r($bindParams);
		print ("</pre>\n");
		$query .= " ". $pid .", '". $fid ."');";
		//echo "<p>INSERT: ". $query . "<p>";
		$checksid = self::checkField();
		if ($checksid['bool'] === FALSE) {
			$conn = parent::mySQLconnection();
			if ($conn->errno) {
				$retmsg .= "<div class=\"error\"><p class=\errormsg\">(check tbl) Cannot connect to database! </p>" . $conn->error . "</div>";
				self::$messg = $retmsg;
				die($retmsg);
			} else {
				if ($checksid == 1) {
					$retmsg .= "<div class=\"error\"><p class=\"errormsg-l2\">Insert statement will not be executed, form already submitted.</p></div>";
					self::$messg = $retmsg;
					//return self::$messg;
				} else {
					$stmt = $conn->prepare($query);
					//$stmt->bind_param('ssss', $values['title'],  $values['varname'], $values['serial'], $values['description']);
					//$stmt->bind_param($bindstr, $_POST['title'], $_POST['varname'], $val['s_array'], $_POST['description']);
					//var_dump($stmt);
					if($stmt === FALSE) {
						$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Config array changed?<br>check table structure to match the configuration array? </p>" . $conn->error . ".</div>";
						self::$messg = $retmsg;
						//return self::$messg;
						die($retmsg);
					} else {
						call_user_func_array(array($stmt, 'bind_param'), $bindParams);
						$stmt->execute();
						//print_r($stmt);
						if ($stmt->errno) {
							$retmsg .= "<div class=\"error\"><p class=\errormsg\">(stmt) Cannot execute query! </p>" . $stmt->error . "</div>";
							self::$messg = $retmsg;
							//return self::$messg;
							die($retmsg);
						} else {
							$retmsg .= "<div class=\"success\"><p class=\"successmsg\">New entry executed successfully!</p></div>";
							self::$messg = $retmsg;
							//return self::$messg;
						}
						$affectedRows = $stmt->affected_rows;
						//echo "<p>$affectedRows</p>";
					}
					$stmt->close();
					$conn->close();
				}
			}
		} else { //$checksid['bool'] === TRUE
			self::$messg = $checksid['msg'];
		}
		return self::$messg;
		//print "the msg insert" . self::$messg;
	}

	
	function checkField($field=FALSE, $cond=FALSE, $where=FALSE) {
		/* usage: 
		 * default SELECT `uid`, `sid` FROM $tableName WHERE `sid` = $sessid;
		 * 1 vars: SELECT $fields FROM $tableName
		 * 2 vars: SELECT $fields FROM $tableName WHERE $field = $where;
		 * 3 vars: SELECT $fields FROM $tableName WHERE $cond = $where;
		 */
		$theTableName = self::$tableName;
		$sessid = isset($_POST['checksid']) ?  $_POST['checksid'] : "-1";
		is_numeric($where) ? $num = 1 : $num = 0;
		$where = trim($where);
		$return = array();
		$retmsg ="";
		$query = "";
		$whereclause = "";
		$strrow = "";
		// put $wherclause depending on $where and $cond
		if ($where === FALSE OR $where === '') {
			$whereclause = "";
		} elseif($cond) {
			if ($num == 1) $whereclause = " WHERE `$cond` = " . $where . ";";
			else $whereclause = " WHERE `$cond` = '" . $where . "';";
		} else {
			if ($num == 1) $whereclause = " WHERE `$field` = " . $where . ";";
			else $whereclause = " WHERE `$field` = '" . $where . "';";
		}
		 
		if ($field === 'MAX(`uid`)') {
			//$query = "SELECT MAX(`uid`) FROM $theTableName";
			$query = "SELECT " . $field . " FROM $theTableName";
		// no $field given
		} elseif ($field) {
			if ($whereclause) {
				if ($num == 1) $whereclause = " WHERE `$field` = " . $where . ";";
				else $whereclause = " WHERE `$field` = '" . $where . "';";
			} else { // if no $where given, $cond will be ignored
				$whereclause = ";";
			}
			$query = "SELECT `uid`, `$field` FROM $theTableName";
			$query .= $whereclause;
		} else {
			$field = "sid";
			$query = "SELECT `$field` FROM $theTableName";
			if ($whereclause) {
				$query .= $whereclause;
			} else $query .= " WHERE `sid` = '" . $sessid . "';";
		}
		//echo "<p>the query: $query</p>";
		
		$conn = parent::mySQLconnection();
		if ($conn->errno) {
			$retmsg .= "<div class=\"error\"><p class=\errormsg\">(check session) Cannot connect to database! </p>" . $conn->error ."</div>";
			self::$messg = $retmsg;
			die($retmsg);
		} else {
			$stmt = $conn->prepare($query);
			if ($stmt) {
				$stmt->execute();					
				$result = $stmt->get_result();
				$rows = $result->num_rows;
// 				print ("\n<pre>result = ");
// 				print_r($result);
// 				print ("</pre>\n");
				if($rows) {
					while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
// 						print ("\n<pre>row = ");
// 						print_r($row);
// 						print ("</pre>\n");
					 // pick up all returned values from query
						$strrow .= $row[$field].", ";
					}
					$strrow = auxiliary::truncateReturn($strrow, 2);
//					echo "<p>str $strrow</p>";
					$retmsg .= "<div class=\"error\"><p class=\"errormsg\">checkField: Form already submitted, entry exists.</p>";
					$retmsg .= "<p class=\"errormsg-l2\">Insert statement will not be executed, form already submitted.</p></div>";
					$return[$field] = $strrow;
					$return['msg'] = $retmsg;
					$return['bool'] = TRUE;
				} else {
					$retmsg .= "<div class=\"error\"><p class=\"errormsg\">checkField: Query returned an empty result.</p></div>";
					$return[$field] = NULL;
					$return['msg'] = $retmsg;
					$return['bool'] = FALSE;
				}
				$stmt->close();
				$conn->close();
			} else { // if $stmt
				$retmsg .= "<div class=\"error\"><p class=\"errormsg\">checkField(): Non valid SQL Query! Check variables.</p></div>";
				$return[$field] = NULL;
				$return['msg'] = $retmsg;
				$return['bool'] = FALSE;
			}
		}
		self::$messg = $return['msg'];
		return $return;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function write2Table($sess=FALSE) {
		$stringOFields = "";
		$theTableName = "";
		if (self::$varArray["prefix"]) $theTableName = self::$varArray["prefix"] . "_";
		$theTableName .= self::$tableName;
		if (self::$varArray["suffix"]) $theTableName .= "_" . self::$varArray["suffix"];
		$crdate = time();
		$tstamp = strtotime('now');
		$fields = self::makeFields();
		$pieces = explode(", ", $fields[1]);
		$no = count($pieces);
		$stringmsg = "";
		$addFields = "";
		$addValues = "";
		$return = array();
		// if $fid or $pid are set
		// 		if($this->fid) {
		// 			$addFields = ", `fid`";
		// 			$addValues = ", '".$this->fid."'";
		// 		} else {
		// 			$addFields = "";
		// 			$addValues = "";
		// 		}
		// 		if($this->pid) {
		// 			$addFields .= ", `pid`";
		// 			$addValues .= ", '".$this-> pid."'";
		// 		} else {
		// 			$addFields .= "";
		// 			$addValues .= "";
		// 		}
		for ($i=0;$i<$no-1;$i++) {
			$trimmed = trim($pieces[$i]); // just in case: no spaces around 'field names'
			$stringOFields .= "$trimmed, ";
		}
		//echo $stringOFields;
		$sid = $chksid = (isset($_POST['checksid']) ? $_POST['checksid'] : -1);
		$query  = "INSERT INTO $theTableName (`crdate`,  `tstamp`, `sid`, ";
		$query .= $stringOFields;
		$query = auxiliary::truncateReturn($query, 2);
		$query .= $addFields.")";
		$query .= " Values ($crdate, $tstamp, '".$sid."', ";
		while ($fieldValues = each($fields[4])) { // get all $_POST['name'] values
			$getValues = $fieldValues['value'];
			if (is_array($getValues)) { // if value is array (from checkboxes or select fields) produce a string
				$query .= "'";
				$query .= auxiliary::secnserialize($multiple['value']);
				// get the individual values of array into table separated by $delimiter
				//				$temp = ""; // clear $temp from previous loop
				// 				while ($multiple = each($getValues)) { // get all values $_POST['name'] array
				// 					$temp .= addslashes(htmlspecialchars($multiple['value'])).$delimiter." ";
				// 				}
				//				$query .= auxiliary::truncateReturn($temp, 2);
				$query .= "', ";
				} else { // if value is string just do this
					$query .= "'" . auxiliary::secureit($getValues) . "', ";
				}
			}
			$query = auxiliary::truncateReturn($query, 2);
			$query .= $addValues.");";
			//echo  "<pre>The generated query: $query</pre>";
			// check if form was already submitted
			//$ses = $this->checkField('sid',$_POST['checksid'],'sid');
			//$unique_email = $this->checkField('email',$_POST['email'],'email');
			if (!$ses['bool'] AND !$unique_email['bool'] OR TRUE) {
				//echo "true";
				$conn = parent::mySQLconnection();
				if ($conn->errno) {
					$retmsg .= "<div class=\"error\"><p class=\errormsg\">(write2table) Cannot connect to database! </p>" . $conn->error . "</div>";
					self::$messg = $retmsg;
					die($retmsg);
				} else {
					$stmt = $conn->prepare($query);
					if($stmt === FALSE) {
						$retmsg .= "<div class=\"error\"><p class=\"errormsg\">prepare failed! </p>" . $conn->error . ".</div>";
						self::$messg = $retmsg;
						die($retmsg);
					} else {
						//call_user_func_array(array($stmt, 'bind_param'), $bindParams);
						$stmt->execute();
						$retmsg .= "<div class=\"success\"><p class=\"successmsg\">(w2t) New entry executed successfully!</p></div>";
						self::$messg = $retmsg;
					}
				}
			} else  {
				$stringmsg .= "<p class=\"errormsg\">entry exists already</p>";
				$return['entry'] = -1;
			}
				
			// 			if($sql_connect->query($query) === TRUE) { // inserts values into table
			// 				$stringmsg .= "<div class=\"error\"><p class=\"errormsg\">Entry successfull<p></div>";
			// 				$return['entry'] = 1;
			// 				mysqli_close($sql_connect);
			// 				if ( !empty($fields[5]) ) { //check if there's at least one upload i.e. type="file" field
			// 					$i = 0;
			// 					$length = count($fields[5]);
			// 					// open db connection
			// 					$sql_connect = @new mysqli($this->host, $this->user, $this->pass, $this->dbname);
			// 					$sql_connect->set_charset($this->charset);
			// 					$query = "UPDATE $tableName SET "; // reset query
			// 					foreach ($fields[5] as $upkey => $upvalue) {
			// 						if (is_string($upkey)) {
			// 							$i++;
			// 							$returnArray = $this->uploadHandler($upvalue);
			// 							$stringmsg .= "<p class=\"errormsg\">".$returnArray[0]."</p>";
			// 							$query .= $upkey."= '".$returnArray[1]."'";
			// 							if ($i < $length) {
			// 								$query .= ", ";
			// 							}
			// 						} else {
			// 							// do nothing if key is an integer
			// 						}
			// 					}
			// 					$query .= " WHERE `sid`= '$sid';";
			// 					//echo $query;
			// 					if($sql_connect->query($query) === TRUE) {
			// 						$stringmsg .= "<p class=\"errormsg\" style=\"color:green;\">filenames written to table</p>";
			// 						$return['update'] = 1;
			// 					} else {
			// 						$stringmsg .= "<p class=\"errormsg\">filenames query could not be executed successfully!</p>" . $sql_connect->error;
			// 						$return['update'] = 0;
			// 					}
			// 				} else {
			// 					// do nothing
			// 				}
			// 			} else {
			// 				$stringmsg .= "<p class=\"errormsg\">cannot write 2 table</p>" . $sql_connect->error;
			// 				$return['entry'] = 0;
			// 			}
			//		}
			$return['msg'] = $retmsg;
			//print $retmsg;
			return self::$messg;
			//return $return;
		}
	
}
?>