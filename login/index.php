<?php
//phpinfo();
define('__ROOT__', dirname((dirname(__FILE__))));

require_once(__ROOT__.'/class_dbConfig.php');
require_once(__ROOT__.'/class_htmlAssemble.php');
require_once(__ROOT__.'/class_variables.php');
require_once(__ROOT__.'/class_dbOperations.php');
require_once(__ROOT__.'/class_createForm.php');

//some variables
$dbpm = array(
		"host"=>"127.0.0.1",
		"user"=>"sign-in",
		"pass"=>"2f96R7s3FcM3SJ7y",
		"dbname"=>"secure_login",
		"dbparams"=>"DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin"
);

$loginFormArray =
// 1st fieldset
array('Sign-in required//id=login-data//class=fieldset-login//l_id=login-id//l_class=login-class' => array(
		'Search' =>
		array('input' =>
				array(	'type'=>'text',
						'name'=>'username',
						//'value'=> '',
						'required'=>'required',
						'placeholder' =>'your username',
						'autofocus'=>'autofocus',
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 120 ) NOT NULL',
				)
		),
		'Password' =>
		array('input' =>
				array(	'type'=>'password',
						'name'=>'passwd',
						'required'=>'required',
						'placeholder' =>'your password',
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 120 ) NOT NULL',
				)
		),
		'Forgot Password' =>
		array('button' =>
				array(	'type'=>'submit',
						'name'=>'forgot_passwd',
						'value'=>1,
						'content'=>'CONTENT',
						'formenctype'=>'application/x-www-form-urlencoded multipart/form-data text/plain',
						'formaction'=>'index.php?forgotPasswd=1',
						'formtarget'=>'_self',
						'formmethod'=>'post',
						//'@#db' => 'VARCHAR( 120 ) NOT NULL',
				)
		)
)
);

// session_start();
// $SID = session_id();
// if(empty($SID)) session_start() or die('Could not start session');
// $_SESSION['sid'] = $SID;

//print ("\n<pre>POST before = ");
//print_r($_POST);
//print_r($_SERVER);
//print_r($dbpm);
//print ("</pre><br>");

$formTagparams['action'] = $self."?id=1&fid=login";
$string = $msg = "";
$doc = new htmlAssemble(
		$doctype = "<!DOCTYPE html>", 
		$htmltype="<html>", 
		$title="Sign-in", 
		$charset="utf-8", 
		$cssfile="form.css", 
		$jsfile="form.js", 
		$csspath="../css/", 
		$jspath="../js/", 
		$media = "all");

$htmlstr = "\n<h1>The Log-in Form</h1>\n";
$htmlstr .= "<div><p>One pre-installed admin user.</p></div>\n";

//dbOperations::makeInstance($dbpm, $loginFormArray, "users", $varsArr)->createTable();
dbOperations::makeInstance($dbpm, $loginFormArray, "users")->createTable("login");

if (!($_SERVER['REQUEST_METHOD'] === "POST")) {

	//createForm::getInstance($configFormArray, $icon, $formTagparams, $moreFormparams)->printForm();
	$msg .= dbOperations::$messg;
	$form = createForm::getInstance($loginFormArray, $icon, $formTagparams, $moreFormparams_login)->toString($formTagparams, $moreFormparams_login);
	$string .= $msg;
	$string .= $htmlstr;	
	$string .= $form;
	print $string;
	
	//$dbpm = "";
	//$varsArr = "";
	//echo "<pre>";
	//print_r($varsArr);
	//print_r($configFormArray);
	//dbConnection::getInstance($dbpm);
	//echo "</pre>";

	//dbOperations::makeInstance($dbpm, $genFromArray,"",$varsArr);
	//dbOperations::makeInstance($dbpm, $genFromArray,"", $varsArr)->makeFields($varsArr);
	//dbOperations::makeInstance($dbpm, $genFromArray,"", $varsArr)->createTable($varsArr);
	//dbOperations::makeInstance($dbpm, $configFormArray,"configArrays", $varsArr)->insertSerial("configArrays",$varsArr,$insertVals);
}


$doc->htmlfoot();

?>