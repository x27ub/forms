<?php
//define('__ROOT__', (dirname(__FILE__)));

require_once(__ROOT__.'/class_auxiliary.php');

class createForm {
	// attributes for the <form> tag
	protected static $formTagParams = 
		array(	'accept-charset'=>'', // default: the page charset | UTF-8 - Character encoding for Unicode, ISO-8859-1 - Character encoding for the Latin alphabet
				'action'=>'', // default: the submitting page
				'autocomplete'=>'', // default: on
				'enctype'=>'', // default: is url-encoded
				'method'=>'get', // default: GET
				'name'=>'', // DOM usage: document.forms.name
				'novalidate'=>'', // Specifies that the browser should not validate the form.
				'target'=>'' // Specifies the target of the address in the action attribute (default: _self).			
		);
	// configuration array for the form itself
	protected static $formParams;
	/* a submit button is needed, a resest button for that matter too
	 * set classes or ids with $moreformParams of getInstance() function
	 * the div which holds the <form>, default is set here, no classes, no id's
	 */
	protected static $moreFormParams = array(
			'submitLabel'=>"",
			'submitClass'=>"", 
			'idSuffix'=>"",
			'resetLabel'=>"",
			'resetClass'=>"",
			'resetId'=>"",
			'divClass'=>"",
			'divId'=>"",
			'title'=>"<h2>The Form</h2>"
			);
	// variables accessible from all functions, no need to make them arguments of any function
	protected $hiddenVariables; // assoc name/value
	protected static $noButton = 0; // count the number of makeButton calls for the id
	protected static $separator = "@#=";
	protected static $fieldsetSep = "//";
	protected static $s_selected = "selected=\"selected\""; // XML notation change to "selected"; if needed
	protected static $s_checked = "checked=\"checked\""; // XML notation change to "checked"; if needed
	protected static $reqicon; // required icon
	protected static $defreq = "*"; // default required icon
	protected static $reqtag = "abbr"; // required tag
	protected static $reqopen; // <abbr class="reqstar" title="required field">
	protected static $reqclose;  // </abbr>
	protected static $sub_class; // class for submit
	protected static $id; // id for submit  button
	//protected static $res_id; // id for reset button
	protected static $res_class; // class for reset buttons
	protected static $div_class; // div holding the form
	protected static $div_id;
	protected static $theFormTagPara; // transfer to the function printForm()
	protected static $moreFormPara; // transfer to the function printForm(), 
									// instead of putting into the argument of it
	private static $instance; // transfer to the function printForm()

	
	function __construct($formparams, $requicon, $form_tag_para, $more_form_para) {	
		if ($formparams) self::$formParams = $formparams;
		else { // if for fieldset no string nor integer is given, notify
			self::$formParams = array('ERROR: Missing Configuration Array' => array('Missing Configuration Array' =>
					array('input' =>
							array(	'type'=>'text',
									'name'=>'errormsg',
									//'value'=> 'ERROR',
									'readonly'=>'readonly',
									'placeholder' =>'ERROR',
									'class'=>'error',
							),
					),
			),
			);
		}
		// default values or the values from the function argument (method)
		if ($form_tag_para) {
			$form_tag_params = $form_tag_para;
			self::$theFormTagPara = $form_tag_params;
		} else {
			$self = $_SERVER['SCRIPT_NAME'];
			self::$formTagParams['action'] = $self;
			$form_tag_params = self::$formTagParams;
			self::$theFormTagPara = $form_tag_params;
		}
		// default values or the values from the function argument (method)
		if ($more_form_para) {
			$more_form_params = $more_form_para;
			self::$moreFormPara = $more_form_params;
		} else {			
			$more_form_params = self::$moreFormParams;
			self::$moreFormPara = $more_form_params;
		}
		// set given values to argument or set to empty string
		($more_form_params['submitClass']) ? self::$sub_class = " class=\"".$more_form_params['submitClass']."\"" : self::$sub_class = "";
		($more_form_params['idSuffix']) ? self::$id = "_".$more_form_params['idSuffix'] : self::$id = "";
		($more_form_params['resetClass']) ? self::$res_class = " class=\"".$more_form_params['resetClass']."\"" : self::$res_class = "";
		($more_form_params['divClass']) ? self::$div_class = " class=\"".$more_form_params['divClass']."\"" : self::$div_class = "";
		($more_form_params['divId']) ? self::$div_id = " id=\"".$more_form_params['divId']."\"" : self::$div_id = "";
		($requicon) ? self::$reqicon = $requicon : self::$reqicon = self::$defreq;
 		self::$reqopen = "<".self::$reqtag." class=\"reqstar\" title=\"required field\">";
 		self::$reqclose = "</".self::$reqtag.">";
	}
	
	public static function getInstance($formparams, $requicon=FALSE, $formTagparams=FALSE, $moreformParams=array()){
		if (is_null( self::$instance )){
			self::$instance = new self($formparams, $requicon, $formTagparams, $moreformParams);
		}
		return self::$instance;		
	}
	
	public static function printForm() {
		print self::toString(self::$theFormTagPara,self::$moreFormPara);
	}

	public static function toString($formtagpara=FALSE, $moreformpara=FALSE) {
		$chksid = isset($_SESSION['sid']) ? $_SESSION['sid'] : "0";
		$moreformparams = ($moreformpara ? $moreformpara : self::$moreFormParams);
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
		$return_string = $moreformpara['title'];
		$return_string .= "<div".$div_id.$div_class.">\n";
		$return_string .= "<form";
		$formtagparams = ($formtagpara ? $formtagpara : self::$formTagParams);
		foreach($formtagparams as $key => $value) {
			if(empty($value) OR $value === ""){
				$return_string .= "";
			}
			else {
				$return_string .= " ".$key."=\"".$value."\"";
			}
		}
		$return_string .= ">\n";
		$return_string .= "<div><input type=\"hidden\" name=\"checksid\" value=\"$chksid\"></div>";
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

	protected static function makeFieldset() {
		$fieldset_id = $legend_id = $fieldset_class = $legend_class = "";
		$return_string = "";
		// walk thru the given array: key level 0
		$form_array = self::$formParams;
		while ($formArray = each($form_array)) {
			$fieldset = $formArray['key'];
			$fieldsetInput = $formArray['key'];
			$pos = strpos($fieldsetInput, "//");
			if ($pos) {
				$split = explode(self::$fieldsetSep, $fieldsetInput);
				$fieldset_legend = $split[0];
				while ($item = each($split)) {
					$value = $item['value'];
					if (0 === strpos($value,'id=')) {
						$parts = explode('=',$value);
						$fieldset_id = " id=\"".$parts[1]."\"";
					} elseif (0 === strpos($value,'class=')) {
						$parts = explode('=',$value);
						$fieldset_class = " class=\"".$parts[1]."\"";
					} elseif (0 === strpos($value,'l_class=')) {
						$parts = explode('=',$value);
						$legend_class = " class=\"".$parts[1]."\"";
					} elseif (0 === strpos($value,'l_id=')) {
						$parts = explode('=',$value);
						$legend_id = " id=\"".$parts[1]."\"";
					} else {
						// do nothing
					}
				}
			} else {
				$fieldset_legend = $formArray['key'];
				$fieldset_id = $legend_id = $fieldset_class = $legend_class = "";
			}

			if (is_string($fieldset)) { // fieldset is set, thus generate it
				$return_string .= "\n<fieldset".$fieldset_class.$fieldset_id."><legend".$legend_class.$legend_id.">".$fieldset_legend."</legend>\n";
				$return_string .= self::makeFormFields($form_array,$fieldset);
				$return_string .= "</fieldset>\n";
			} elseif (is_int($fieldset)) { // no fieldset
				$return_string .= "<div class=\"form-container-$fieldset\">";
				$return_string .= self::makeFormFields($form_array,$fieldset);
				$return_string .= "</div>";
			} else {
				$return_string .= "<div class=\"form-container-$fieldset error\">";
				$return_string .= "ERROR: fieldsets must be integer or string only.";
				$return_string .= "</div>";
			}
		}
		return ($return_string);
	}

	protected static function makeFormFields($vararray,$varkey) {
		$return_string = "";
		$class = "";
		// walk thru the given array: key level 1
		while ($formConf = each($vararray[$varkey])) {
			$label = $formConf['key'];
			while ($type = each($vararray[$varkey][$label])) {
				$typeArray = $type['key']; // $typeArray = input, textarea (key level 2)
				//echo "<pre>";
				//print_r($type['value']);
				//echo "</pre>";
				$ifvalue = auxiliary::array_key_check($type['value'],'value');
				if ($ifvalue) $value = $type['value']['value']; // for the submit buttons
				if (auxiliary::array_key_check($type['value'], 'type')) $shape = $type['value']['type'];
				//else $shape = "";
				//endow lable with 'class' or 'label_class' if defined
				if (auxiliary::array_key_check($type['value'], 'class')) { 
					$classv = $type['value']['class'];
					$class = "class=\"$classv\"";
				}
				if (auxiliary::array_key_check($type['value'], 'label_class')) {
					$lclass = $type['value']['label_class'];
					if ($lclass === NULL) $class = "";
					elseif (is_string($lclass)) $class = "class=\"$lclass\"";
					else $class = "";				
				}
				//echo "<p>lclass = $lclass</p>"; echo "<p>class = $class</p>";
				// generate from individual form fields according to type and shape
				if ($typeArray === 'input' && ($shape === 'text' OR  $shape === 'email' OR  $shape === 'url' OR  $shape === 'tel' OR  $shape === 'search' OR  $shape === 'date' OR  $shape === 'time' OR  $shape === 'datetime' OR  $shape === 'datetime-local' OR  $shape === 'month' OR  $shape === 'week' OR $shape === 'number' OR $shape === 'color')) {
					$result = self::makeInputField($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];
				}  elseif($typeArray === 'input' AND $shape === 'datalist') {
					$result = self::makeDataList($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>\n";
					$return_string .= "<".$typeArray." "; // "<".$typeArray." type=\"datalist\" ";
					$return_string .= $result[0];
				} elseif($typeArray === 'input' AND $shape === 'file') {
					$result = self::makeUpFile($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>\n";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];					
				} elseif($typeArray === 'input' && $shape === 'password') {
					$result = self::makePassword($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];
				} elseif($typeArray === 'input' && $shape === 'submit') { // is this sensible?
					$return_string .= "<div><label $class for=\"{$label}\">".$label.":</label>";
					// the following line is not needed since <input is generated by makeButton()
					// $return_string .= "<".$typeArray." "; 
					$return_string .= self::makeButton($value);
					self::$noButton++;
				} elseif($typeArray === 'input' && $shape === 'radio') {
					$result = self::makeRadio($typeArray, $vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= $result[0];
				} elseif($typeArray === 'input' && $shape === 'checkbox') {
					$result = self::makeCheckbox($typeArray, $vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= $result[0];
				} elseif($typeArray === 'select') {
					$result = self::makeSelect($typeArray, $vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>\n"; // " </label><br>\n"; ?
					$return_string .= $result[0];
				} elseif($typeArray === 'textarea') {
					$result = self::makeTextarea($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];
				} elseif ($typeArray === 'input' && $shape === 'hidden') {
					$return_string .= "<div><".$typeArray." ";
					$return_string .= self::makeHiddenField($vararray, $varkey, $label, $typeArray);
					// these functions need still to be created 
				} elseif ($typeArray === 'input' && ($shape === 'range')) {
					$result = self::makeInputRange($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label $class for=\"{$label}\">".$label.": ".$result[1]." Step=".$result[3]['step']."</label>";
					$return_string .= "<ul style=\"list-style-type: none;\">\n<li $class style=\"display:inline;  background-color: #ffffff;  position:relative; left:-5px;\">".$result[3]['min']."</li>\n<li $class style=\"display:inline;  background-color: #ffffff;\"><".$typeArray." ";					
					$return_string .= $result[0]."</li>\n<li $class style=\"display:inline; background-color: #ffffff;\">".$result[3]['max']."</li></ul>";
				} elseif ($typeArray === 'keygen') {
					$result = self::makeInputField($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];
				} elseif ($typeArray === 'button') {
					$result = self::makeGenButton($vararray, $varkey, $label, $typeArray);
					$return_string .= "<div><label for=\"{$label}\">".$label.": ".$result[1]."</label>";
					$return_string .= "<".$typeArray." ";
					$return_string .= $result[0];
				}
				else echo "<div class=\"error\">ERROR: shape =<pre>". print_r($shape)."</pre>  not defined properly.</div>";
			} 
			$return_string .= "</div>\n";
			//$return_string .= "<pre>".$shape."</pre>";
		}
		return $return_string;
	}

	protected static function makeInputField($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
// 			print "<p>key = ".$name."</p>";
// 			print "<p>value = ".$value."</p>";
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				//$required = "<abbr class=\"reqstar\" title=\"required field\">".$value."</abbr>";
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}
	
	protected static function makeGenButton($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$content = "";
		$required = "";
		$whatType = "";
		if ($thearray[$thekey][$thelabel][$thetype]['type'] === 'button') $whatType = "button";
		elseif ($thearray[$thekey][$thelabel][$thetype]['type'] === 'submit') $whatType = "submit";
		if ($whatType === 'submit') {
			self::$noButton++;
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
				$name = $config['key'];
				$value = $config['value'];
				//print "<p>key = ".$name."</p>";
				//print "<p>value = ".$value."</p>";
				if ($name === '@#db') {
					$return_string .= '';
				} elseif ($name === 'label_class') {
					$return_string .= "";
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} elseif ($name === 'content') {
					$content = $value;
				} else {
					$return_string .= $name."=\"".$value."\" ";
				}
			}
		} elseif ($whatType === 'button') { // not all attributes are permitted
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
				$name = $config['key'];
				$value = $config['value'];
				//print "<p>key = ".$name."</p>";
				//print "<p>value = ".$value."</p>";
				if ($name === '@#db') {
					$return_string .= '';
				} elseif ($name === 'label_class') {
					$return_string .= "";
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} elseif ($name === 'content') {
					$content = $value;
				} elseif ($name === 'name' OR $name === 'value' OR $name === 'autofocus' OR $name === 'disabled' OR $name === 'form') {
					// valid attributes: name, value, autofocus, disabled, form
					$return_string .= $name."=\"".$value."\" ";
				} else {
					$return_string .= "";
				}
			}
		} else {}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">$content</button>";
		//$return_string .= "<script type=\"text/javascript\">alert('Please review: Does required make sense for this button?')</script>";
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}
	
	protected static function makeInputRange($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		$datalist = "";
		$makeDatalist = 0;
		$minMaxLabel = array('min'=>0, 'max'=>100, 'step'=>1); // the default values
		if (auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype], 'optionslist') 
				&& !auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype], 'match') ) { // optionslist is given and no match
			$makeDatalist = 1;
 			$value = $thearray[$thekey][$thelabel][$thetype]['match']='default-list';
			$return_string .= "list=\"".$value."\" ";
			$datalist .= "\n<datalist id=\"".$value."\">\n";
		}
		while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
//			print "<p>name = " . $name . "<br>value = ". $value . "</p>";
// 			echo "<pre>";
// 			print_r($return_string);
// 			echo "</pre>";
// 			echo "<pre>";
// 			print_r($datalist);
// 			echo "</pre>";
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= '';
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} elseif ($name === 'match' && $makeDatalist == 0) {
				$return_string .= "list=\"".$value."\" ";
				$datalist .= "\n<datalist id=\"".$value."\">\n";
			}  elseif ($name === 'min') {
				$return_string .= $name."=\"".$value."\" ";
				$minMaxLabel[$name]= $value;
			} elseif ($name === 'max') {
				$return_string .= $name."=\"".$value."\" ";
				$minMaxLabel[$name]= $value;
			} elseif ($name === 'step') {
				$return_string .= $name."=\"".$value."\" ";
				$minMaxLabel[$name]= $value;
			} elseif ($name === 'optionslist') {
				while ($options =  each($thearray[$thekey][$thelabel][$thetype]['optionslist'])) { //each($thearray[$thekey][$thelabel][$thetype] $typeArray,
					$itemkey = $options['key'];
					$item = $options['value'];
					$datalist .= "<option value=\"".$item."\"></option>\n";
					//$datalist .= "<option value=\"".$item."\">$item</option>\n";
				}
				$datalist .= "</datalist>\n";
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
// 		echo "<pre>";
// 		print_r($return_string);
// 		echo "</pre>";
// 		echo "<pre>";
// 		print_r($datalist);
// 		echo "</pre>";
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		$return_string .= $datalist;
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		$result[3] = $minMaxLabel;
// 		echo "<pre>";
// 		print_r($result);
// 		echo "</pre>";
		return $result;
	}
	
	protected static function makeDataList($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		$datalist = "";
		$makeDatalist = 0;
		if (!auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype], 'optionslist')) {
			$return_string = "><div class=\"error\">ERROR: Type 'datalist' needs an array for optionlist.</div>";
			$result = array();
			$result[0] = $return_string;
			return $result;
		} elseif(!auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype], 'match')) { // optionslist is given and no match
			$makeDatalist = 1;
			$value = $thearray[$thekey][$thelabel][$thetype]['match']='default-list';
			$return_string .= "list=\"".$value."\" ";
			$datalist .= "\n<datalist id=\"".$value."\">\n";
		}
		while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
			
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} elseif ($name === 'type') {
				$return_string .= "";
			} elseif ($name === 'match' && $makeDatalist == 0) {
					$return_string .= "list=\"".$value."\" ";
					$datalist .= "\n<datalist id=\"".$value."\">\n";
			} elseif ($name === 'optionslist') {
				while ($options =  each($thearray[$thekey][$thelabel][$thetype]['optionslist'])) { //each($thearray[$thekey][$thelabel][$thetype] $typeArray,
					$itemkey = $options['key'];
					$item = $options['value'];
					$datalist .= "<option value=\"".$item."\"></option>\n";
					//$datalist .= "<option value=\"".$item."\">$item</option>\n";
				}
				$datalist .= "</datalist>\n";
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		$return_string .= $datalist;
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}
	
	protected static function makeUpFile($thearray,$thekey,$thelabel,$thetype) {
		$varSuffix = "";
		$id_progress = "";
		$return_string = "";
		$required = "";
		$file_prop = array();
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} elseif ($name === 'accept') {
				if (is_array($value)) {
					$i = -1;
					$arraylength = count($config['value']);
					$acceptattr = $name;
					$return_string .= $name."=\"";
					while ($mime = each($value)) {
						$i++;
						$mimekey = $mime['key'];
						$mimeval = $mime['value'];
						if ($i < $arraylength-1) {
							$return_string .= $mimeval.", ";
						} else {
							$return_string .= $mimeval."\" ";
						}
					}
				} else {
					$return_string .= $name."=\"".$value."\" ";
				}
			} elseif ($name === 'maxfilesize') {
				$return_string .= '';
				$file_prop['maxfilesize'] = $value;
			} elseif ($name === 'allowed_filetypes') {
				$return_string .= '';
				$file_prop['allowed_filetypes'] = $value;
			} elseif ($name === 'allowed_extensions') {
				$return_string .= '';
				$file_prop['allowed_extensions'] = $value;
			} elseif ($name === 'name') {
				$return_string .= $name."=\"".$value."\" ";
				$varSuffix = "_".$value;
				$id_progress = "progress_key".$varSuffix;
				$file_prop[$name] = $value;
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		$return_string .= "\n<input type=\"hidden\" name=\"APC_UPLOAD_PROGRESS\" id=\"$id_progress\" value=\"$varSuffix\" />";
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		$result[2] = $file_prop;
		return $result;
	}

	protected static function makePassword($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
			// avoid html output for db params
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}


	protected static function makeTextarea($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		$fill = '';
		while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
			// avoid html output for db params
			if ($name === '@#db') {
				$return_string .= "";
			} elseif ($name === 'default') {
				$fill = $value;
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} elseif ($name === 'required') {
				$return_string .= $name."=\"".$value."\" ";
				$requiredIcon = self::$reqicon;
				$required = self::$reqopen.$requiredIcon.self::$reqclose;
			} else {
				$return_string .= $name."=\"".$value."\" ";
			}
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">".$fill."</textarea>";
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}

	protected static function makeRadio($theTypeArray,$thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		$config_string = "";
		$value_length = count($thearray[$thekey][$thelabel][$thetype]['value']);
		$config_length = count($thearray[$thekey][$thelabel][$thetype]);
		$radio_string = '';
		$value_string = '';
		$j = 0;
		$i = 0;
		$return_string .= "\n<ul style=\"list-style:none;\">\n"; // label and first radio button not on same line

		while ($value_array = each($thearray[$thekey][$thelabel][$thetype]['value'])) {
			$j++;
			$keyval = $value_array['key'];
			$value = $value_array['value'];
			$return_string .= "<li><".$theTypeArray." ";
			// find out if radio button ought to be checked
			if(strstr($value, self::$separator)) {
				$pos = strpos($value, self::$separator);
				$trimvalue = substr($value, 0, $pos);
				$checked = "checked=\"checked\"";
				$value_string = "value=\"".$keyval."\" ".$checked." ";
				$label = $trimvalue;
				$i++; // count the no of checked radios
			} else {
				$value_string = "value=\"".$keyval."\" ";
				$label = $value;
			}
			// write the radio buttons params
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {								
				$name = $config['key'];
				$value = $config['value'];
				$items = each($config);
				if ($config['key'] === 'value') {
					$config_string .= '';
				} elseif ($name === 'label_class') {
					$return_string .= "";
				} elseif ($config['key'] === '@#db') {
					$config_string .= '';
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} else {
					$value = $config['value'];
					$config_string .= $name."=\"".$value."\" ";
				}
			}
			$return_string .= $config_string.$value_string;
			$return_string = auxiliary::truncateReturn($return_string,1);
			if ($j < $value_length) {
				$return_string .= ">$label</li>\n"; // makes radio buttons vertical
			} else $return_string .= ">$label</li>\n</ul>"; // no <br> tag after last radio button
		}
		if ($i>1) { // no. of checked radios must be 0 or 1
			$return_string .= "<div class=\"error\">ERROR: $i radio buttons marked as checked</div>";
		}
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}

	protected static function makeCheckbox($theTypeArray,$thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		$config_string = "";
		$value_length = count($thearray[$thekey][$thelabel][$thetype]['value']);
		$config_length = count($thearray[$thekey][$thelabel][$thetype]);
		$radio_string = '';
		$value_string = '';
		$j = 0;
		$i = 0;
		$return_string .= "\n<ul style=\"list-style:none;\">\n"; // label and first check box not on same line

		while ($value_array = each($thearray[$thekey][$thelabel][$thetype]['value'])) {
			$j++;
			$keyval = $value_array['key'];
			$value = $value_array['value'];
			$return_string .= "<li><".$theTypeArray." ";
			// find out if the box ought to be checked
			if(strstr($value, self::$separator)) {
				$pos = strpos($value, self::$separator);
				$trimvalue = substr($value, 0, $pos);
				$checked = self::$s_checked;
				$value_string = "value=\"".$keyval."\" ".$checked." ";
				$label = $trimvalue;
				$i++; // no of checked checkboxes
			} else {
				$value_string = "value=\"".$keyval."\" ";
				$label = $value;
			}
			// write <input type="checkbox" ... > and other params
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {				
				$name = $config['key'];
				$value = $config['value'];
				if ($name === 'value') {
					$config_string .= '';
				} elseif ($name === 'label_class') {
					$return_string .= "";
				} elseif ($name === '@#db') {
					$config_string .= '';
				} elseif ($name === 'name') {
					$config_string .= $name."=\"".$value."[]\" "; // name=somename[] needs to be an array
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} else $config_string .= $name."=\"".$value."\" ";
			}
			$return_string .= $config_string.$value_string;
			$return_string = auxiliary::truncateReturn($return_string,1);
			if ($j < $value_length) {
				$return_string .= ">$label</li>\n"; // makes check boxes vertical
			} else $return_string .= ">$label</li>\n</ul>"; // no <br> tag after last radio button
		}
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}
	
	protected static function getValueAfterSeparator($v_item,$v_itemkey,$var){
		$attribute = $var;
		$item = $v_item;
		$itemkey = $v_itemkey;
		$return_str = "";
		
		if(strstr($item, self::$separator)) {
			$pos = strpos($item, self::$separator);
			$trimvalue = substr($item, 0, $pos);
			$selected = $attribute;
			$value_string = "value=\"".$itemkey."\" ".$selected." ";
			$label = $trimvalue; // $item without the '=selected' part
			$return_str .= "<option";
			// put a value=".." into the option's tag
			$return_str .= " ".$value_string; //" value=\"".$item."\"";
			$return_str = auxiliary::truncateReturn($return_str,1);
			$return_str .= ">$label</option>\n";
		} else {
			$return_str .= "<option";
			// put a value=".." into the option's tag or comment out
			$return_str .= " value=\"".$itemkey."\"";
			$return_str .= ">$item</option>\n";
		}
		return $return_str;
	}

	protected static function makeSelect($theTypeArray,$thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		$required = "";
		// find out if selection list is with 'optgroup' or 'options'
		if (auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype],'optgroup')) {
			if (auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype],'multiple')){
				$return_string .= "<br>\n"; // label and selection field not on same line
			}
			// 1 produce <select name= ... >
			$return_string .= "<".$theTypeArray." ";
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
				$name = $config['key'];
				$value = $config['value'];
				// avoid html output for db params
				if ($name === '@#db') {
					$return_string .= '';
				} elseif ($name === 'label_class') {
					$return_string .= "";
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} else {
					// want no 'options= ...' and [] if name
					if ($name === 'name') {
						$return_string .= $name."=\"".$value."[]\" "; // name=somename[] needs to be an array
					} elseif ($name === 'optgroup') {
						$return_string .= "";
					} else $return_string .= $name."=\"".$value."\" ";
				}
			}
			$return_string = auxiliary::truncateReturn($return_string,1);
			$return_string .= ">\n";
			// 2 produce <optgroup ... >
			while ($optgroup = each($thearray[$thekey][$thelabel][$thetype]['optgroup'])) {
				$optlabel = $optgroup['key'];
				$return_string .= "<optgroup label=\"$optlabel\">\n";
				// produce <option ...> ... </options>
				while ($items = each($thearray[$thekey][$thelabel][$thetype]['optgroup'][$optlabel])) {
					//$value = $config['value'];
					$itemkey = $items['key'];
					$item = $items['value'];
					$return_string .= self::getValueAfterSeparator($item,$itemkey,self::$s_selected);
				}
				$return_string .= "</optgroup>\n";
			}
			$return_string .= "</select>\n";
		} elseif (auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype],'options')) {
			if (auxiliary::array_key_check($thearray[$thekey][$thelabel][$thetype],'multiple')){
				$return_string .= "<br>\n"; // label and selection field not on same line
			}
			// 1 produce <select name= ... >			
			$return_string .= "<".$theTypeArray." ";
			while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
				$name = $config['key'];
				$value = $config['value'];
				// avoid html output for db params
				if ($name === '@#db') {
					$return_string .= '';
				} elseif ($name === 'required') {
					$return_string .= $name."=\"".$value."\" ";
					$requiredIcon = self::$reqicon;
					$required = self::$reqopen.$requiredIcon.self::$reqclose;
				} else {
					// want no 'options= ...' and [] if name
					if ($name === 'name') {
						$return_string .= $name."=\"".$value."[]\" "; // name="somename[]" needs to be an array
					} elseif ($name === 'options') {
						$return_string .= "";
					} else $return_string .= $name."=\"".$value."\" ";
				}
			}
			$return_string = auxiliary::truncateReturn($return_string,1);
			$return_string .= " >\n";
			$i = 0;		
			while ($options = each($thearray[$thekey][$thelabel][$thetype]['options'])) {
				$i++;
				$value = $config['value'];
				$itemkey = $options['key'];
				$item = $options['value'];
				// 2 produce <option ...> ... </options>
				$return_string .= self::getValueAfterSeparator($item,$itemkey,self::$s_selected);
			}
			$return_string .= "</select>\n";
		} else {
			$return_string .= "<".$theTypeArray."><option>Error!</option></select>";
			$return_string .= "<div class=\"error\">ERROR: configuration array must indicate";
			$return_string .= " exactly  one of the two values: 'optgroup' or 'options'. Not 'option' !</div>";
		}
		$result = array();
		$result[0] = $return_string;
		$result[1] = $required;
		return $result;
	}

	protected static function makeHiddenField($thearray,$thekey,$thelabel,$thetype) {
		$return_string = "";
		while ($config = each($thearray[$thekey][$thelabel][$thetype])) {
			$name = $config['key'];
			$value = $config['value'];
			if ($name === '@#db') {
				$return_string .= '';
			} elseif ($name === 'label_class') {
				$return_string .= "";
			} else { //? elseif ($name !== '@#db') {
				$return_string .= $name."=\"".$value."\" ";
			}
			//$return_string .= $name."=\"".$value."\" ";
		}
		$return_string = auxiliary::truncateReturn($return_string,1);
		$return_string .= ">";
		return $return_string;
	}

	protected static function makeButton($buttonType) {
		$class = self::$sub_class;
		$id = self::$id;
		$idPrefix = " id=\"";
		$res_class = self::$res_class;
		if ($buttonType === "submit") {			
			$return_string = "<input type=\"submit\" name=\"submit\" value=\"Submit\"".$class.$idPrefix."sub".$id."\">";
		} elseif ($buttonType === 'reset') {
			$return_string = "<input type=\"reset\" name=\"reset\" value=\"Reset Form\"".$res_class.$idPrefix."res".$id."\">";
		} elseif ($buttonType === "next") {
			$return_string = "<input type=\"submit\" name=\"next\" value=\"Next\"".$class.$idPrefix."next".$id."\">";
		} elseif ($buttonType AND !ctype_space($buttonType)) {
			/* 
			 * checks if $buttonType is entirely white spaces
			 * ctype_space($string)
			 * if (trim($string) == '')
			 * preg_match('/^\s*$/',$string);
			 */
			$firstLetter = substr($buttonType,0,1);
			$firstLetterUp = strtoupper($firstLetter);
			$tail = substr($buttonType,1);
			$val = $firstLetterUp;
			$val .= $tail;
		 	$return_string = "<input type=\"submit\" name=\"$buttonType\" value=\"$val\"".$class." id=\"".$buttonType."_".self::$noButton."\">";
		} else {
			$return_string = "<input type=\"submit\" name=\"submitted\" value=\"Submit\" class=\"submit\" id=\"subform_".self::$noButton."\">";
		}
		return($return_string);
	}
	
}
