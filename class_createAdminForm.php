<?php 

class createAdminForm extends createForm {
	
	private static $instance;
	
	function __construct($formparams, $requicon, $form_tag_params, $more_form_params) {
		parent::__construct($formparams, $requicon, $form_tag_params, $more_form_params);
		//self::printString($form_tag_params,$more_form_params);
	}
	
	public static function getInstance($formparams, $requicon=FALSE, $formTagparams=FALSE, $moreformParams=array()){
		if (is_null( self::$instance )){
			self::$instance = new self($formparams, $requicon, $formTagparams,$moreformParams);
		}
		return self::$instance;
	}
	
	public static function printAdminForm() {
		//print self::toString($form_tag_params,$more_form_params);
		print self::toString(self::$theFormTagPara,self::$moreFormPara);
	}
	
	public static function toString($formtagparams=FALSE, $moreformparams=FALSE) {
		foreach ($moreformparams as $key => $value){
			//$$key = $key;
			//print "<p>variable = ".$$key."<br>value = ".$value."</p>";
			$$key = $value;
			//print "<p>variable = ".$$key."<br>";
		}
		if ($divClass) $div_class = " class=\"".$divClass."\"";
				else $div_class = "";
				if ($divId) $div_id = " id=\"".$divId."\"";
				else $div_id = "";
				//generate the form
				$return_string = "<h1>The Admin Form</h1>";
				$return_string .= "<div".$div_id.$div_class.">\n";
				$return_string .= "<form";
				foreach($formtagparams as $key => $value) {
					if(empty($value) OR $value === ""){
						$return_string .= "";
					}
					else {
						$return_string .= " ".$key."=\"".$value."\"";
					}
				}
				$return_string .= ">\n";
				$return_string .= "<div><input type=\"hidden\" name=\"checksid\" value=\"$chksid\"></div>\n";
				$return_string .= self::makeButton('new');
				$return_string .= self::makeButton('previous');
				$return_string .= self::makeButton('next');
				// generate the individual fieldsets with their resp. form fields
				$return_string .= self::makeFieldset();
				$return_string .= "<br>\n";
				// default submit label is 'Submit', 'Reset' respectively
				if($submitLabel) $submitlabel = $submitLabel;
				else $submitlabel = "Submit";
				$return_string .= self::makeButton('submit');
				if ($resetLabel) $resetlabel = $resetLabel;
				else $resetlabel = "Reset";
				$return_string .= self::makeButton('reset');
				$return_string .= "\n</form>\n</div>\n";
				return ($return_string);
		}
}

?>