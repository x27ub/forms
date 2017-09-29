<?php

class dbConnection {
	
	protected static $connection;
	protected static $dbConfig = array(
			"host"=>"localhost",
			"user"=>"forms",
			"pass"=>"q3tAb5X",
			"dbname"=>"forms",
			"dbparams"=>"DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin");
	protected static $trans_dbConfig;
	protected static $message = "";
	protected static $instance;
	
	function __construct($theparams) {
		$retmsg ="";
		if (!empty($theparams)) {
			$parameters = $theparams;
			self::$trans_dbConfig = $parameters;
		} else {
			$parameters = self::$dbConfig;
			self::$trans_dbConfig = $parameters;
		}
// 		print ("\n<pre>parameters = ");
// 		print_r($parameters);
// 		print ("</pre>\n");
		$DBexists = 0;
		$checkDB = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $parameters['dbname'] . "'";
		//alternativley $checkDB = "SHOW DATABASES LIKE '" . $parameters['dbname'] . "';";
		//echo "<p>$checkDB</p>";
		$conn = new mysqli($parameters['host'],$parameters['user'],$parameters['pass']);
		$stm = $conn->prepare($checkDB);
		$stm->execute();
		$stm->store_result();
		$DBexists = $stm->affected_rows;
		$conn->close();

		self::$connection = new mysqli($parameters['host'],$parameters['user'],$parameters['pass']);
// 		print ("\n<pre>connection = ");
// 		var_dump(self::$connection);
// 		print ("</pre>\n");
		if (self::$connection->connect_errno) {
			$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Connection failed: " . self::$connection->connect_error . "</p></div>";
			self::$message .= $retmsg;
			die($retmsg);
		} else {
//			echo "<p>Connected successfully.</p>";
			if ($DBexists !== 1) {
				//create db
				$query = "CREATE DATABASE IF NOT EXISTS `".$parameters['dbname']."` ".$parameters['dbparams'].";";
				//echo "<p>$query</p>";
				$stmt = self::$connection->prepare($query);
				$stmt->execute();
				// create db with error reporting, user needs all privileges
				if ($stmt->errno === 0) {
					$affectedRows = $stmt->affected_rows;
					if ($affectedRows == 1) {
						self::$message .= "<div class=\"success\"><p class=\"errormsg-l2\">DB " . $parameters['dbname'] . " created successfully!</p></div>";
					}
					else {
						$retmsg .= "<div class=\"error\"><p class=\"errormsg\">could not create " . $parameters['dbname'] . "</p></div>";
						self::$message .= $retmsg;
						die($retmsg);
					}
				} else {
					$retmsg .= "<div class=\"error\"><p class=\"errormsg\">Error creating database : " . self::$connection->connect_error . "</p></div>";
					self::$message .= $retmsg;
					die($retmsg);
				}
				$stmt->close();
				self::$connection->close();
			} else {
				$retmsg .= "<p>`" . $parameters['dbname'] . "` exists, var \$DBexists=$DBexists</p>";
				self::$message .= $retmsg;
			}
		}
	}
	
	public static function getInstance($params){
		if (is_null( self::$instance )){
			self::$instance = new self($params);
		}
		return self::$instance;
	}
	
	protected static function mySQLconnection() {
			$parameters = self::$trans_dbConfig;
		self::$connection = new mysqli($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['dbname']);
		return self::$connection;
	}
	
}

?>
