<?php
/* generate html doc until <body>-tag with constructor and closing tags with htmlfoot()
 * define default values for the $dv
 * new htmlAssemble($doctype, $htmltype, $title, ..., $jspath, $media) for custom values
 */

class htmlAssemble {
	protected $docvar;	
	protected $docCont = "<!DOCTYPE html>";
	
	function __construct($doctype = FALSE, $htmltype="<html>", $title="", $charset="utf-8", $cssfile="form.css", $jsfile="form.js", $csspath="", $jspath="", $media = "all") {
		if(!$doctype) $doctype = $this->docCont;
		$this->docvar = array(
						"doctype"=>$doctype,
						"htmltype"=>$htmltype,
						"title"=>$title,
						"charset"=>$charset,
						"cssfile"=>$cssfile,
						"jsfile"=>$jsfile,
						"csspath"=>$csspath,
						"jspath"=>$jspath,
						"media"=>$media				
		);
		/*
		 * write html head
		 */
		$dv = $this->docvar;
		$head_a  = $dv['doctype']."\n".$dv['htmltype']."\n<head>\n<title>".$dv['title']."</title>\n";
		$head_a .= "<meta charset=\"".$dv['charset']."\">\n";
		$head_a .= "<link rel=\"stylesheet\" href=\"{$dv['csspath']}{$dv['cssfile']}\" media=\"{$dv['media']}\" />\n";
		$head_a .= "<script src=\"{$dv['jspath']}{$dv['jsfile']}\"></script>\n";
		$head_a .= "</head>\n<body>\n";
		print ("$head_a");
// 		echo "<pre>";
// 		print_r ($dv);
// 		echo "</pre>";
	}
	

	public function htmlfoot() {
		$htmlfoot = "\n</body>\n</html>";
		print("$htmlfoot");
	}
}

?>