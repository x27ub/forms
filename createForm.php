<?php 
//phpinfo();
define('__ROOT__', (dirname(__FILE__)));

require_once(__ROOT__.'/class_auxiliary.php');
require_once(__ROOT__.'/class_htmlAssemble.php');
require_once(__ROOT__.'/class_createForm.php');
require_once(__ROOT__.'/class_createAdminForm.php');
require_once(__ROOT__.'/class_variables.php');
require_once(__ROOT__.'/class_dbOperations.php');

/* __autoload($class); */

$doc = new htmlAssemble($doctype = "<!DOCTYPE html>", $htmltype="<html>", $title="The Form", $charset="utf-8", $cssfile="form.css", $jsfile="form.js", $csspath="css/", $jspath="js/", $media = "all");

// print "<pre>";
// print_r($genFromArray);
// print "</pre>";
$self = $_SERVER['SCRIPT_NAME'];

//$formTagparams = array();
//$moreFormparams = array();

//print_r($serial_genformArray);


createForm::getInstance($demoFromArray,$icon,$formTagparams,$moreFormparams)->printForm();
//createAdminForm::getInstance($demoFromArray,$icon,$formTagparams,$moreFormparams)->printAdminForm();

//dbOperations::getInstance()->get_autoindex();

$doc->htmlfoot();
?>
