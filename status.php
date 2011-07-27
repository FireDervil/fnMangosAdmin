<?php

include('header.php');
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'defines.php';

xoops_loadLanguage('server', 'fnMangosAdmin');

$Realm = array();
$Realm = getRealmlist();
$i = 0;

$xoopsOption['template_main']= 'fnma_status.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );
$res_color = 1;

foreach($Realm as $i => $result)
{	
	if($res_color == 1)
	{
		$res_color = 2;
	} else {
		$res_color = 1;
	}
	
	// Define the realm type, and realm number
    $realm_type = $realm_type_def[$result['icon']];
	$realm_num = $result['id'];
	
	// Check the realm status using the check_port_status function
    if(check_port_status($result['address'], $result['port']) == TRUE)
    {
		// res image is the up arrow pretty much
        $res_img = 'Online';
		
		// Get the server population
        $population = $fnmaDB["char"]->mysql_count("SELECT COUNT(*) FROM `characters` WHERE online=1");
		
		// Get the server uptime
        $uptime = time() - $fnmaDB["logon"]->fetch_row("SELECT `starttime` FROM `uptime` WHERE `realmid`='$realm_num' ORDER BY `starttime` DESC LIMIT 1");
    } else{
		// Get the result image arrow
        $res_img = 'Offline';
        $population = 0;
        $uptime = 0;
    }
	
	// Convert uptime into a days / hours / minutes format
	if($uptime != 0) 
	{ 
		$uptime = formatTimestamp($uptime); 
	} else {
		$uptime = "N/A";
	}
	
	// Setup this realm in the array
    $Realm[$i]['res_color'] = $res_color;
    $Realm[$i]['status'] = $res_img;
    $Realm[$i]['name'] = $result['name'];
    $Realm[$i]['type'] = $realm_type;
    $Realm[$i]['population'] = population_view($population);
    $Realm[$i]['uptime'] = $uptime;
	
	$xoopsTpl->assign(array(
		"res_color"		=> $Realm[$i]['res_color'],
		"status"		=> $Realm[$i]['status'],
		"name"			=> $result['name'],
		"type"			=> $realm_type,
		"population"	=> $Realm[$i]['population'],
		"uptime"		=> $Realm[$i]['uptime'])
		);
}
	$xoopsOption['xoops_pagetitle'] = sprintf(_MD_FNMA_ALL_ABOUT, $fnmaAccount->getUsername($_SESSION['fnmaUserId']));
	$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
	
$xoopsTpl->assign(array(
	"lang_status"		=> _MD_FNMA_SERVER_STATUS,
	"lang_uptime"		=> _MD_FNMA_REALM_UPTIME,
	"lang_realm_name"	=> _MD_FNMA_SERVER_NAME,
	"lang_auslastung"	=> _MD_FNMA_REALM_POPULATION,
	"lang_realm_type"	=>	_MD_FNMA_REALM_TYPE)
	);
	


include_once XOOPS_ROOT_PATH.'/footer.php';

?>