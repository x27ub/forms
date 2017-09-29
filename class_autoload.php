<?php
function __autoload($class_name)
{
	require_once "class_".$class_name.".php";
}



// function __autoload($class_name) {
// 	if(file_exists('class_'.$class_name . '.php')) {
// 		require_once('class_'.$class_name . '.php');
// 	} else {
// 		throw new Exception("Unable to load 'class_'.$class_name.");
// 	}
// }

// function autoloadModel($className) {
// 	$filename = "models/" . $className . ".php";
// 	if (is_readable($filename)) {
// 		require $filename;
// 	}
// }
//spl_autoload_register("autoloadModel");
?>