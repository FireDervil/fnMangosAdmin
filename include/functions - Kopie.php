<?php

defined('XOOPS_ROOT_PATH') or die('Restricted access');

function fnMangosAdmin_checkModuleAdmin()
{
if ( file_exists($GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))){
	include_once $GLOBALS['xoops']->path('/Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
	return true;
	} else {
		echo xoops_error("Error: You don't use the Frameworks \"adminmodule\".Please install this Frameworks");
		return false;
	}
		
}



function getOptions() {
global $xoopsDB;
	$sql = "SELECT conf_name, conf_value FROM ".$xoopsDB->prefix("fnma_main_config");
	$result = $xoopsDB->query($sql);
	while($myrow = $xoopsDB->fetchArray($result)) {
		$arr_conf[$myrow['conf_name']] = $myrow['conf_value'];
	}
	return $arr_conf;
}





include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.legacy.php';
?>