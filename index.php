<?php
//phpinfo();
define('__ROOT__', (dirname(__FILE__)));

require_once(__ROOT__.'/class_dbConfig.php');
require_once(__ROOT__.'/class_htmlAssemble.php');
require_once(__ROOT__.'/class_variables.php');
require_once(__ROOT__.'/class_dbOperations.php');
require_once(__ROOT__.'/class_createForm.php');

session_start();
$SID = session_id();
if(empty($SID)) session_start() or die('Could not start session');
$_SESSION['sid'] = $SID;

//print ("\n<pre>POST before = ");
//print_r($_POST);
//print_r($_SERVER);
//print_r($dbpm);
//print ("</pre><br>");

$formTagparams['action'] = $self."?id=1&fid=sub";
$string = $msg = "";
$doc = new htmlAssemble(
		$doctype = "<!DOCTYPE html>", 
		$htmltype="<html>", 
		$title="Admin Access", 
		$charset="utf-8", 
		$cssfile="form.css", 
		$jsfile="form.js", 
		$csspath="css/", 
		$jspath="js/", 
		$media = "all");

$htmlstr = "<h1>The Configuration Arrays</h1>";
$htmlstr .= "<div>\n<p>In the table there should be a demo table array and a login form array.</p>\n</div>";

dbOperations::makeInstance($dbpm, $demoFormArray, "complArrays")->createTable();

if (!($_SERVER['REQUEST_METHOD'] === "POST")) {

	//createForm::getInstance($configFormArray, $icon, $formTagparams, $moreFormparams)->printForm();
	$msg .= dbOperations::$messg;
	$form = createForm::getInstance($demoFormArray, $icon, $formTagparams, $moreFormparams)->toString($formTagparams, $moreFormparams);
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

//if ($_POST['submit'] === 'Submit') {
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$s_configFormArray = serialize($demoFormArray);
	$val = array(
			's_array'=>$s_configFormArray,
			'pid'=> 0,
			'fid'=>'configArrays',
			'submitform2'=>'sub2'
	);
	//	$val = "";
	
	$form = createForm::getInstance($demoFormArray, $icon, $formTagparams, $moreFormparams)->toString($formTagparams, $moreFormparams);
	$msg .= dbOperations::$messg;
	$string .= $msg;

// 	dbOperations::makeInstance($dbpm, $configFormArray, "dada", $varsArr)->checkField();
// 	$msg .= dbOperations::$messg;
	dbOperations::makeInstance($dbpm, $demoFormArray, "configArrays", $varsArr)->insert($val);
	$msg .= dbOperations::$messg;
	$string .= $msg;
	$string .= $htmlstr;
	$string .= $form;
	print $string;
	
// 	print ("<br><pre>POST = ");
// 	print_r($_POST);
// 	echo "<br>SESSION = ";
// 	print_r($_SESSION);
//	print_r($_SERVER);
// 	print ("</pre><br>");

// 	ob_start();
// 	print dbOperations::$messg;
// 	$buf = ob_get_contents();
// 	ob_end_clean();
// 	print $buf;
}

$doc->htmlfoot();

?>