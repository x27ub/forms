<?php

require_once(__ROOT__.'/class_auxiliary.php');

$a_title = array(
		'x'=>'please choose',
		'Madam'=>'Mdm.',
		'Sir'=>'Sr.'); 
$a_sex = array(
		'x'=>'x',
		'm'=>'male',
		'f'=>'female');

$a_nationality = array('AFG'=>'Afghanistan','ALA'=>'&Aring;land','ALB'=>'Albania','DZA'=>'Algeria','ASM'=>'American Samoa','AND'=>'Andorra','AGO'=>'Angola','AIA'=>'Anguilla','ATA'=>'Antarctica','ATG'=>'Antigua and Barbuda','ARG'=>'Argentina','ARM'=>'Armenia','ABW'=>'Aruba','AUS'=>'Australia','AUT'=>'Austria','AZE'=>'Azerbaijan','BHR'=>'Bahrain','BGD'=>'Bangladesh','BRB'=>'Barbados','BLR'=>'Belarus','BEL'=>'Belgium','BLZ'=>'Belize','BEN'=>'Benin','BMU'=>'Bermuda','BTN'=>'Bhutan','BOL'=>'Bolivia','BES'=>'Bonaire, Sint Eustatius and Saba','BIH'=>'Bosnia and Herzegovina','BWA'=>'Botswana','BVT'=>'Bouvet Island','BRA'=>'Brazil','IOT'=>'British Indian Ocean Territory','VGB'=>'British Virgin Islands','BRN'=>'Brunei','BGR'=>'Bulgaria','BFA'=>'Burkina Faso','BDI'=>'Burundi','KHM'=>'Cambodia','CMR'=>'Cameroon','CAN'=>'Canada','CPV'=>'Cape Verde','CYM'=>'Cayman Islands','CAF'=>'Central African Republic','TCD'=>'Chad','CHL'=>'Chile','CHN'=>'China','CXR'=>'Christmas Island','CCK'=>'Cocos Keeling) Islands','COL'=>'Colombia','
COM'=>'Comoros','COD'=>'Congo','COG'=>'Congo-Brazzaville','COK'=>'Cook Islands','CRI'=>'Costa Rica','CIV'=>'Côte d’Ivoire','HRV'=>'Croatia','CUB'=>'Cuba','CUW'=>'Curaçao','CYP'=>'Cyprus','CZE'=>'Czech Republic','DNK'=>'Denmark','DJI'=>'Djibouti','DMA'=>'Dominica','DOM'=>'Dominican Republic','ECU'=>'Ecuador','EGY'=>'Egypt','SLV'=>'El Salvador','GNQ'=>'Equatorial Guinea','ERI'=>'Eritrea','EST'=>'Estonia','ETH'=>'Ethiopia','FLK'=>'Falkland Islands','FRO'=>'Faroes','FJI'=>'Fiji','FIN'=>'Finland','FRA'=>'France','GUF'=>'French Guiana','PYF'=>'French Polynesia','ATF'=>'French Southern Territories','GAB'=>'Gabon','GMB'=>'Gambia','GEO'=>'Georgia','DEU'=>'Germany','GHA'=>'Ghana','GIB'=>'Gibraltar','GRC'=>'Greece','GRL'=>'Greenland','GRD'=>'Grenada','GLP'=>'Guadeloupe','GUM'=>'Guam','GTM'=>'Guatemala','GGY'=>'Guernsey','GIN'=>'Guinea','GNB'=>'Guinea-Bissau','GUY'=>'Guyana','HTI'=>'Haiti','HMD'=>'Heard Island and McDonald Islands','HND'=>'Honduras','HKG'=>'Hong Kong SAR of China','HUN'=>'Hungary','ISL'=>'Iceland','IND'=>'India','IDN'=>'Indonesia','IRN'=>'Iran','IRQ'=>'Iraq','IRL'=>'Ireland','IMN'=>'Isle of Man','ISR'=>'Israel','ITA'=>'Italy','JAM'=>'Jamaica','JPN'=>'Japan','JEY'=>'Jersey','JOR'=>'Jordan','KAZ'=>'Kazakhstan','KEN'=>'Kenya','KIR'=>'Kiribati','KWT'=>'Kuwait','KGZ'=>'Kyrgyzstan','LAO'=>'Laos','LVA'=>'Latvia','LBN'=>'Lebanon','LSO'=>'Lesotho','LBR'=>'Liberia','LBY'=>'Libya','LIE'=>'Liechtenstein','LTU'=>'Lithuania','LUX'=>'Luxembourg','MAC'=>'Macao SAR of China','MKD'=>'Macedonia','MDG'=>'Madagascar','MWI'=>'Malawi','MYS'=>'Malaysia','MDV'=>'Maldives','MLI'=>'Mali','MLT'=>'Malta','MHL'=>'Marshall Islands','MTQ'=>'Martinique','MRT'=>'Mauritania','MUS'=>'Mauritius','MYT'=>'Mayotte','MEX'=>'Mexico','FSM'=>'Micronesia','MDA'=>'Moldova','MCO'=>'Monaco','MNG'=>'Mongolia','MNE'=>'Montenegro','MSR'=>'Montserrat','MAR'=>'Morocco','MOZ'=>'Mozambique','MMR'=>'Myanmar','NAM'=>'Namibia','NRU'=>'Nauru','NPL'=>'Nepal','NLD'=>'Netherlands','ANT'=>'Netherlands Antilles','NCL'=>'New Caledonia','NZL'=>'New Zealand','NIC'=>'
Nicaragua','NER'=>'Niger','NGA'=>'Nigeria','NIU'=>'Niue','NFK'=>'Norfolk Island','PRK'=>'North Korea','MNP'=>'Northern Marianas','NOR'=>'Norway','OMN'=>'Oman','PAK'=>'Pakistan','PLW'=>'Palau','PSE'=>'Palestine','PAN'=>'Panama','PNG'=>'Papua New Guinea','PRY'=>'Paraguay','PER'=>'Peru','PHL'=>'Philippines','PCN'=>'Pitcairn Islands','POL'=>'Poland','PRT'=>'Portugal','PRI'=>'Puerto Rico','QAT'=>'Qatar','REU'=>'Reunion','ROU'=>'Romania','RUS'=>'Russia','RWA'=>'Rwanda','BLM'=>'Saint Barthélemy','SHN'=>'Saint Helena, Ascension and Tristan da Cunha','KNA'=>'Saint Kitts and Nevis','LCA'=>'Saint Lucia','MAF'=>'Saint Martin','SPM'=>'Saint Pierre and Miquelon','VCT'=>'Saint Vincent and the Grenadines','WSM'=>'Samoa','SMR'=>'San Marino','STP'=>'São Tomé e Príncipe','SAU'=>'Saudi Arabia','SEN'=>'Senegal','SRB'=>'Serbia','CSG'=>'Serbia and Montenegro','SYC'=>'Seychelles','SLE'=>'Sierra Leone','SGP'=>'Singapore','SXM'=>'Sint Maarten','SVK'=>'Slovakia','SVN'=>'Slovenia','SLB'=>'Solomon Islands','SOM'=>'Somalia','ZAF'=>'South
Africa','SGS'=>'South Georgia and the South Sandwich Islands','KOR'=>'South Korea','SSD'=>'South Sudan','ESP'=>'Spain','LKA'=>'Sri Lanka','SDN'=>'Sudan','SUR'=>'Suriname','SJM'=>'Svalbard','SWZ'=>'Swaziland','SWE'=>'Sweden','CHE'=>'Switzerland','SYR'=>'Syria','TWN'=>'Taiwan','TJK'=>'Tajikistan','TZA'=>'Tanzania','THA'=>'Thailand','BHS'=>'The Bahamas','TLS'=>'Timor-Leste','TGO'=>'Togo','TKL'=>'Tokelau','TON'=>'Tonga','TTO'=>'Trinidad and Tobago','TUN'=>'Tunisia','TUR'=>'Turkey','TKM'=>'Turkmenistan','TCA'=>'Turks and Caicos Islands','TUV'=>'Tuvalu','UGA'=>'Uganda','UKR'=>'Ukraine','ARE'=>'United Arab Emirates','GBR'=>'United Kingdom','USA'=>'United States@#=selected','UMI'=>'United States Minor Outlying Islands','URY'=>'Uruguay','VIR'=>'US Virgin Islands','UZB'=>'Uzbekistan','VUT'=>'Vanuatu','VAT'=>'Vatican City','VEN'=>'Venezuela','VNM'=>'Vietnam','WLF'=>'Wallis and Futuna','ESH'=>'Western Sahara','YEM'=>'Yemen','ZMB'=>'Zambia','ZWE'=>'Zimbabwe');

$a_subscription = array(
		'm'=>'monthly',
		'3m'=>'3 months',
		'6m'=>'6 months@#=checked',
		'1a'=>'1 year',
		'2a'=>'2 years',
		'5a'=>'5 years');

$a_sponsor_id = array(
		'atnt'=>'AT&T',
		'verizon'=>'Verizon@#=checked',
		'safeway'=>'Safeway');

$a_magazines = array(
		'Magazines'=> array(
				'm1'=>'Magazine A',
				'm2'=>'Magazine B@#=selected',
				'm3'=>'Magazine C'),
		'CDs'=> array(
				'cd1'=>'CD A',
				'cd2'=>'CD B',
				'cd3'=>'CD C'),
		'Pencils'=> array(
				'p1'=>'Pencil A',
				'p2'=>'Pencil B'),
		'Lighters'=>array(
				'l1'=>'Lighter A',
				'l2'=>'Lighter B',
				'l3'=>'Lighter C',
				'l4'=>'Lighter D'));

$a_browser = array(
		"I.E."=>"Internet Explorer",
		"fire"=> "Firefox",
		"chrome"=>"Chrome",
		"opera"=>"Opera",
		"safari"=>"Safari");

$a_range = array(-50,-40,-30,-20,-10,0,10,20,30,40,50);

$icon = "***";

$accept = array('text/html','application/pdf','image/*','audio/mp3', 'video/*');
$allowfiles = array('/image/','/text/','/pdf/');
$ext = array('.jpeg','.jpg','.gif','.bmp','.png','.txt','.pdf','.ukn');
$maxfilesize = 104857600;

$self = $_SERVER['SCRIPT_NAME'];
$formTagparams = array(
		'accept-charset'=>'utf-8',
		'action'=>$self,
//		'autocomplete'=>'off',
		'enctype'=>'multipart/form-data',
		'method'=>'post',
		'name'=>'example-form',
//		'novalidate'=>'novalidate',
//		'target'=>'_blank'
);

$moreFormparams = array(
		'submitLabel'=>"Go",
		'submitClass'=>"button",
		'idSuffix'=>"form",
		'resetLabel'=>'refresh',
		'resetClass'=>"button",
		'divClass'=>"divclass",
		'divId'=>'divid',
		'title'=>"<h2>Configuration Array Form</h2>"
);

$moreFormparams_login = array(
		'submitLabel'=>"Go",
		'submitClass'=>"button",
		'idSuffix'=>"form",
		'resetLabel'=>'refresh',
		'resetClass'=>"button",
		'divClass'=>"divclass",
		'divId'=>'divid',
		'title'=>"<h2>Sign-in Form</h2>\n"
);

$dbpm = array(
		"host"=>"127.0.0.1",
		"user"=>"forms",
		"pass"=>"q3tAb5X",
		"dbname"=>"a_forms",
		"dbparams"=>"DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin"
);
// $dbpm = array(
// 			"host"=>"db635041367.db.1and1.com",
// 			"user"=>"dbo635041367",
// 			"pass"=>"y2Eg53Ac",
// 			"dbname"=>"db635041367",
// 			"dbparams"=>"DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin"
// 		);

$varsArr = array(
		"uploadPath"=>"/formsndb/",
		"prefix"=>"a",
		"suffix"=>"01",
		"salt"=>"I|1Tofu+-Wev>w.W=S\$Y+5$44HC;[<",
		"pid"=>0,
		"fid"=>1
);



//birth date range
$now = time();
$delta16 = (16*365*24*3600); // 16a
$delta40 = (60*365*24*3600); // 60a
$v = auxiliary::mk_date_range($delta16,$delta40,$now);
$d16 = $v['max'];
$d40 = $v['min'];

// config arrays forms
$demoFormArray =
// 1st fieldset
array('Record Info//id=recinfo//class=fieldset01//l_id=lid//l_class=lclass' => array(
		'Search' =>
		array('input' =>
				array(	'type'=>'search',
						'name'=>'searchname',
						//'value'=> '',
						//'required'=>'required',
						'placeholder' =>'serach term',
						'autofocus'=>'autofocus',
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 80 ) NOT NULL',
				),
		),
		'Range' =>
		array('input' =>
				array(	'type'=>'range',
						'match'	=> 'range-list',
						'optionslist' => $a_range,
						'name'=>'range',
						//'readonly'=>'readonly',
						'id'=>'rangeid',
						'min'=> -50,
						'max'=> 50,
						'step'=> 5,
						//'value'=> 10,
						'value'=> '10,60',
						//'required'=>'required',
						'class'=>'in_w1',
						'multiple'=>'multiple',
						'label_class'=>'therange',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Number' =>
		array('input' =>
				array(	'type'=>'number',
						'name'=>'evenint',
						'min'=> 0,
						'max'=> 16,
						'step'=> 2,
						'value'=> 6,
						'required'=>'required',
						'class'=>'in_w1',
						'label_class'=>'rem',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Date' =>
		array('input' =>
				array(	'type'=>'date',
						'name'=>'datename',
						//'value'=>0,
						'required'=>'required',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Datetime' =>
		array('input' =>
				array(	'type'=>'datetime-local',
						'name'=>'datetimename',
						//'value'=>0,
						'required'=>'required',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Month and Year' =>
		array('input' =>
				array(	'type'=>'month',
						'name'=>'monthnyear',
						//'value'=>0,
						'required'=>'required',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Time' =>
		array('input' =>
				array(	'type'=>'time',
						'name'=>'timename',
						//'value'=>0,
						'required'=>'required',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Week and Year' =>
		array('input' =>
				array(	'type'=>'week',
						'name'=>'weeknyear',
						//'value'=>0,
						'required'=>'required',
						'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
				)
		),
		'Pick Color' =>
		array('input' =>
				array(	'type'=>'color',
						'name'=>'color',
						'value'=>'#0000ff',
						//'required'=>'required',
						'@#db' => 'VARCHAR( 120 ) NULL DEFAULT "#ffffff"',
				)
		),
		'Datalist Example' =>
		array('input' =>
				array(	'type'=>'datalist',
						'match'	=> 'browserlist',
						'optionslist' => $a_browser,
						'name'=>'browsers',
						'required'=>'required',
						'@#db' => 'VARCHAR( 120 ) NULL DEFAULT ""',
				)
		),
		'Submit' =>
		array('input' =>
				array(	'type'=>'submit',
						'name'=>'submitform1',
						'value'=>'skip'
				),
		),
), // end first
		// 2nd fieldset
		'Personal Information//id=persinfo//class=fieldset02//l_id=pi//l_class=lc' => array(
				'Application Number' =>
				array('input' =>
						array(	'type' => 'text',
								'name' => 'applicationNo',
								//'value'=> $applicationNo,
								'pattern'=>'[A-Za-z]{3}',
								'required'=>'required',
								'class'=>'in_w1',
								//'label_class'=>'cl',
								'@#db' => 'VARCHAR( 120 ) NOT NULL',
						),
				),
				'Your First Name' =>
				array(	'input' =>
						array(	'type'=>'text',
								'name'=>'firstname',
								//'value'=> $firstname,
								'required'=>'required',
								'class'=>'in_w1',
								//'label_class'=>'cl',
								'@#db' => 'VARCHAR( 80 ) NOT NULL',
						),
				),
				'Your Last Name' =>
				array('input' =>
						array(	'type'=>'text',
								'name'=>'lastname',
								//'value'=> $lastname,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 80 ) NOT NULL',
						),
				),
				'Your Date of Birth' =>
				array('input' =>
						array(  'type'=>'date',
								'name'=>'dob',
								//'value'=> $dob,
								'required'=>'required',
								'min'=> $d16,
								'max' => $d40,
								'class'=>'in_w1',
								'@#db' => 'INT( 11 ) NOT NULL',
						),
				),
				'Sex' =>
				array('select' =>
						array(	'name'=>'a_sex',
								'options'=> $a_sex,
								'class'=>'sel_w1',
								'required'=>'required',
								'@#db' => 'VARCHAR( 10 ) NOT NULL',
						),
				),
				'Nationality' =>
				array('select' =>
						array(	'name'=>'a_nationality',
								'options'=> $a_nationality,
								'class'=>'sel_w1',
								'required'=>'required',
								'@#db' => 'VARCHAR( 4 ) NOT NULL',
						),
				),
				'Your Web-Site' =>
				array('input' =>
						array(	'type'=>'url',
								'name'=>'visitor_url',
								//'value'=>0,
								'required'=>'required',
								'@#db' => 'INT( 11 ) NOT NULL DEFAULT 0',
						)
				),
		), // end second
		// 3rd fieldset
		'How to Contact You' =>
		array( 'email' =>
				array('input' =>
						array(	'type' => 'email',
								'name'=>'email',
								//'value' => $email,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 80 ) NOT NULL',
						),
				),
				'Phone' =>
				array('input' =>
						array(	'type' => 'tel',
								'name'=>'phone',
								//'value' => $phone,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 60 ) NOT NULL',
						),
				),
		), // end third
		// 4th fieldset
		'Sign Up' => array(
				'Choose Password' =>
				array(	'input' =>
						array(	'type'=>'password',
								'name'=> 'pwd',
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 60 ) NOT NULL DEFAULT 0',
						),
				),
				'Repeat Password' =>
				array(	'input' =>
						array(	'type'=>'password',
								'name'=> 'pwd_repeated',
								'required'=>'required',
								'class'=>'in_w1',
								//'@#db' => 'VARCHAR( 60 ) NOT NULL DEFAULT 0',
						),
				),
				'Select Subscripton' =>
				array('input' =>
						array(	'type' => 'radio',
								'name'=>'subscription',
								'value'=> $a_subscription,
								'required'=>'required',
								'class'=>'radio',
								'@#db' => 'VARCHAR( 20 ) NOT NULL'
						)
				),
				'Select Your Sponsors' =>
				array('input' =>
						array(	'type' => 'checkbox',
								'name'=>'a_sponsor_id',
								'value'=>$a_sponsor_id,
								'required'=>'required',
								//'class'=>'in_w1',
								'@#db' => 'VARCHAR( 20 ) NOT NULL DEFAULT 0'
						)
				),
				'I want to have the following gifts' =>
				array('select' =>
						array(	'name' => 'magazines',
								'optgroup'=>$a_magazines,
								'multiple'=>'multiple',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 20 ) NOT NULL DEFAULT 0'
						)
				),
		), // end fourth
		// 5th fieldset
		'Comments & Files' => array( 'Comments' =>
				array('textarea' =>
						array(	'name'=>'comments',
								'default'=>'',// $comments,
								//'required'=>'required',
								'wrap'=>'soft',  // same as virtual
								//'wrap'=>'hard',  // sends to server all carriage returns
								//'wrap'=>'off' // is default, no wrap in textarea
								//'wrap'=>'virtual', // I.E. carriage return in textarea field, not sent
								//'wrap'=>'physical', // I.E. carriage return in textarea field, not sent								
								'cols' => '50',
								'rows' => '6',
								'id' => 'commentfield',
								'class'=>'in_w1',
								'@#db' => 'TEXT'
						),
				),
				'Note' =>
				array('input' =>
						array(	'type'=>'text',
								'name'=>'note',
								'value'=>'please note',
								'required'=>'required',
								'size'=>30,
								'readonly'=>'readonly',
								'@#db' => 'VARCHAR( 20 ) NOT NULL DEFAULT 0',
						)
				),
				'Upload File' =>
				array('input' =>
						array(	'type'=>'file',
								'name'=>'file_url',
								'accept'=>$accept,
								'maxfilesize'=> $maxfilesize,
								'allowed_filetypes'=> $allowfiles,
								'allowed_extensions'=> $ext,
								'multiple'=>'multiple',
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 255 ) NOT NULL',
						),
				),
				'Key Generator' =>
				array('keygen' =>
						array(	'name'=>'keygen',
								//'value'=>'wertyu56789',
								'required'=>'required',
								'@#db' => 'VARCHAR( 255 ) NOT NULL DEFAULT 0',
						)
				),
				'Submit' =>
					array('input' => 
						array(	'type'=>'submit',
								'name'=>'submitform2',
								'value'=>'Submit'
						),				
					),
		),
);


$configFormArray = array('The Configuration Array' => array(
		'Title' =>
		array('input' =>
				array(	'type'=>'text',
						'name'=>'title',
						//'value'=> '',
						'required'=>'required',
						'autofocus'=>'autofocus',
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 255 ) NOT NULL'
				),
		),
		'Variable Name' =>
		array('input' =>
				array(	'type'=>'text',
						'name'=>'varname',
						//'value'=> '',
						//'required'=>'required',						
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 255 ) NULL'
				),
		),
		'Serialized Array' =>
		array('textarea' =>
				array(	'type'=>'text',
						'name'=>'s_array',
						//'value'=> '',
						'cols' => '80',
						'rows' => '20',
						'class'=>'in_w1',
						'@#db' => 'LONGTEXT NOT NULL'
				),
		),
		'Description' =>
		array('textarea' =>
				array(	'type'=>'text',
						'name'=>'description',
						//'value'=> '',
						'cols' => '50',
						'rows' => '6',
						'required'=>'required',
						'class'=>'in_w1',
						'@#db' => 'TEXT NULL'
				),
		),
		'Select Subscripton' =>
		array('input' =>
				array(	'type' => 'radio',
						'name'=>'subscription',
						'value'=> $a_subscription,
						'required'=>'required',
						'class'=>'radio',
						'@#db' => 'VARCHAR( 20 ) NOT NULL'
				)
		),
		'I want to have the following gifts' =>
		array('select' =>
				array(	'name' => 'magazines',
						'optgroup'=>$a_magazines,
						'multiple'=>'multiple',
						'class'=>'in_w1',
						'@#db' => 'TEXT'
				)
		),
		'Choose Password' =>
		array(	'input' =>
				array(	'type'=>'password',
						'name'=> 'pwd',
						'required'=>'required',
						'class'=>'in_w1',
						'@#db' => 'VARCHAR( 60 ) NOT NULL DEFAULT 0',
				),
		),
// 		'Submit' =>
// 		array('input' =>
// 				array(	'type'=>'submit',
// 						'name'=>'submitform2',
// 						'value'=>'Submit',
// 						'@#db' => 'VARCHAR( 20 ) NOT NULL'
// 				),
// 		),
 	),
);

$moreConfig = array('More Configuration' => array('Your Title' =>
				array(	'select' =>
						array(	'name'=>'a_title',
								'options'=> $a_title,
								'class'=>'sel_w1',
								'required'=>'required',
								'autofocus'=>'autofocus',
								//'label_class'=>'cl',
								'@#db' => 'VARCHAR( 10 ) NOT NULL',
									
						),
				),
		),
		array('Address' =>
				array('input' =>
						array( 	'type'=>'text',
								'name'=>'address',
								//'value'=> $address,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 120 ) NOT NULL',
						),
				),
				'Additional, if needed' =>  array(
						'input' =>
						array(  'type' => 'text',
								'name' => 'additional',
								//'value'=> $additional,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 60 ) NOT NULL',
						),
				),
				'Zip Code' =>  array(
						'input' =>
						array( 'type' => 'text',
								'name' => 'zip',
								//'value'=> $zip,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 10 ) NOT NULL',
						)
				),
				'Place' =>  array(
						'input' =>
						array( 'type' => 'text',
								'name' => 'place',
								//'value'=> $place,
								'required'=>'required',
								'class'=>'in_w1',
								'@#db' => 'VARCHAR( 80 ) NOT NULL',
						)
				),
		)
);



$str = "The demo form which contains a sample of every HTML5 form field available to day. Please note, every browser doesn't yet support every HTML5 form field available.";
$s_array = serialize($demoFormArray);
$insertVals = array(
		'title'=>'Demo Array',
		'varname'=>'\$demoFromArray',
		'serial'=>$s_array,
		'descr'=>$str
);
?>