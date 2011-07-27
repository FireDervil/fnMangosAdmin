<?php



function getOptions4Admin() {
global $xoopsDB;
	$sql = "SELECT conf_id, conf_name, conf_value FROM ".$xoopsDB->prefix("fnma_main_config");
	$result = $xoopsDB->query($sql);
	$i=0;
	while($myrow = $xoopsDB->fetchArray($result)) {
		$arr_conf[$i]['conf_id'] = $myrow['conf_id'];
		$arr_conf[$i]['conf_name'] = $myrow['conf_name'];
		$arr_conf[$i]['conf_value'] = $myrow['conf_value'];
		$arr_conf[$i]['conf_formtype'] = $myrow['conf_formtype'];
		$i++;
	}
	return $arr_conf;
}




?>