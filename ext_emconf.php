<?php

########################################################################
# Extension Manager/Repository config file for ext "datamints_feuser".
#
# Auto generated 05-09-2013 11:08
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend User Management',
	'description' => 'User registration and edit plugin, fully configurable, custom validators, autologin, double-opt-in, admin approval, IRRE configuration, resend activation mail, redirect features, support for md5/salted md5 extensions, support for salesforce. More to come!',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.9.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Bernhard Baumgartl, datamints GmbH',
	'author_email' => 'b.baumgartl@datamints.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '4.5.0-6.1.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'xliff' => '',
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:41:{s:9:"ChangeLog";s:4:"b3a1";s:16:"de.locallang.xlf";s:4:"8061";s:19:"de.locallang_db.xlf";s:4:"a358";s:21:"ext_conf_template.txt";s:4:"a5f3";s:12:"ext_icon.gif";s:4:"cf3b";s:17:"ext_localconf.php";s:4:"7cbc";s:15:"ext_php_api.dat";s:4:"028d";s:14:"ext_tables.php";s:4:"4a2e";s:14:"ext_tables.sql";s:4:"6c9b";s:13:"locallang.xlf";s:4:"b862";s:16:"locallang_db.xlf";s:4:"40de";s:10:"README.txt";s:4:"89e6";s:14:"doc/manual.sxw";s:4:"7600";s:19:"doc/wizard_form.dat";s:4:"f7be";s:20:"doc/wizard_form.html";s:4:"7f46";s:21:"flexform/data_pi1.xml";s:4:"8577";s:26:"flexform/data_pi1_irre.xml";s:4:"d4b3";s:23:"flexform/sheet_edit.xml";s:4:"d4ac";s:27:"flexform/sheet_extended.xml";s:4:"9259";s:26:"flexform/sheet_general.xml";s:4:"9c08";s:31:"flexform/sheet_general_irre.xml";s:4:"4b2e";s:27:"flexform/sheet_redirect.xml";s:4:"9f54";s:31:"flexform/sheet_registration.xml";s:4:"b38f";s:41:"lib/class.tx_datamintsfeuser_flexform.php";s:4:"fcc9";s:43:"lib/class.tx_datamintsfeuser_salesforce.php";s:4:"7d32";s:38:"lib/class.tx_datamintsfeuser_utils.php";s:4:"ed27";s:14:"pi1/ce_wiz.gif";s:4:"5ce9";s:36:"pi1/class.tx_datamintsfeuser_pi1.php";s:4:"a850";s:44:"pi1/class.tx_datamintsfeuser_pi1_wizicon.php";s:4:"994b";s:13:"pi1/clear.gif";s:4:"cc11";s:20:"pi1/de.locallang.xlf";s:4:"1675";s:17:"pi1/locallang.xlf";s:4:"b583";s:24:"pi1/static/constants.txt";s:4:"d41d";s:20:"pi1/static/setup.txt";s:4:"e169";s:20:"res/arrow_red_up.png";s:4:"4c36";s:24:"res/datamints_feuser.css";s:4:"204c";s:30:"res/datamints_feuser_mail.html";s:4:"9463";s:16:"res/validator.js";s:4:"861e";s:20:"res/validator.min.js";s:4:"71f1";s:31:"static/salesforce/constants.txt";s:4:"d41d";s:27:"static/salesforce/setup.txt";s:4:"2df8";}',
);

?>