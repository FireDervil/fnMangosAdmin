<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('tops', 'fnMangosAdmin');

$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}


$xoopsOption['template_main'] = 'fnma_server_online.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'zones' . DS . 'fnma_zone.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'characters' . DS . 'chars.php';

$fnmaZone = new Zone;
$fnmaChar = new Char;
$res_color = 1;

if(isset($_GET["page"]))
{
	$pid = $_GET["page"];
} 
else 
{
	$pid = 1;
}
$limit = 100;
$limitstart = ($pid - 1) * $limit;

$res_info = array();
$query = array();
$realm_info = getRealmById($_SESSION['selected_realm']);
foreach($realm_info as $result) 
{
	$Online_Check = check_port_status($result['address'], $result['port']);
	$xoopsTpl->assign('result', $result);
}
$cc = 0;

if($Online_Check == TRUE)
{
	$Count = $fnmaChar->getOnlineCount();
	
	$numofpgs = ($Count / $limit);
	if(gettype($Count / $limit) != "integer") 
	{
		settype($numofpgs, "integer");
		$numofpgs++;
	}
	$query = $fnmaChar->getOnlineList(0, $limitstart, $limit);
}
else
{
	$numofpgs = 0;
}
$xoopsTpl->assign('numofpgs', $numofpgs);

if($query > 0)
{
foreach($query as $result) 
{
	if($res_color==1)
	{
		$res_color=2;
	}
	else
	{
		$res_color=1;
	}
	
	$cc++;     
	$res_race = $fnmaChar->charInfo['race'][$result['race']];
	$res_class = $fnmaChar->charInfo['class'][$result['class']];
	$res_pos = $fnmaZone->getZoneName($result['zone']);

	$res_info[$cc]["number"] = $cc;
	$res_info[$cc]["res_color"] = $res_color;
	$res_info[$cc]["name"] = $result['name'];
	$res_info[$cc]["race"] = $result['race'];
	$res_info[$cc]["class"] = $result['class'];
	$res_info[$cc]["gender"] = $result['gender'];
	$res_info[$cc]["level"] = $result['level'];
	$res_info[$cc]["pos"] = $res_pos;
	$res_info[$cc]["guid"] = $result['guid'];
}
unset($query); // Free up memory.

//===== Calc pages =====//
$pnum = ceil($Count / $limit);
$pages_str = paginate($pnum, $pid, "online.php");

$xoopsTpl->assign('pages_str', $pages_str);
$xoopsTpl->assign('res_info', $res_info);
$xoopsTpl->assign('pages_str', $pages_str);
} else {
$xoopsTpl->assign('noplayer' ,1);	
}
include XOOPS_ROOT_PATH."/footer.php";
?>