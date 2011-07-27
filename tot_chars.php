<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('tops', 'fnMangosAdmin');

$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}


$xoopsOption['template_main'] = 'fnma_server_chars.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'zones' . DS . 'fnma_zone.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'characters' . DS . 'chars.php';

if(isset($_GET['page']))
{
	$p = $_GET['page'];
}
else
{
	$p = 1;
}

//===== Calc pages1 =====//
$items_per_pages = 100;
$limit_start = ($p-1) * $items_per_pages;

$fnmaZone = new Zone;
$fnmaChar = new Char;

$query = array();
$realm_info_new = getRealmById($_SESSION['selected_realm']);

$cc = 0;
// array´s
$query1 = array();

//===== Filter ==========//
if($_GET['char'] && preg_match("/[a-z]/",$_GET['char']))
{
   $filter = "WHERE `name` LIKE '".$_GET['char']."%'";
}
elseif($_GET['char'] == 1)
{
   $filter = "WHERE `name` REGEXP '^[^A-Za-z]'";
}
else
{
   $filter = '';
}

//Find total number of characters in database -- used to calculate total number of pages
$cc2 =  $fnmaDB["char"]->mysql_count("SELECT count(*) FROM `characters` $filter");
$query1 = $fnmaDB["char"]->select("SELECT * FROM `characters` $filter ORDER BY `name` LIMIT $limit_start, $items_per_pages");

$cc1 = 0;
$item_res = array();
$res_color = 1;

if($cc2 > 0)
{
	foreach ($query1 as $result1) 
	{
		if($res_color==1) 
		{
		  $res_color=2;
		}
		else
		{
		  $res_color=1;
		}
		$cc1++;
		$res_pos = $fnmaZone->getZoneName($result1['zone']);

		$char_gender = dechex($result1['gender']);
		
		$item_res[$cc1]["number"] = $cc1;
		$item_res[$cc1]["name"] = $result1['name'];
		$item_res[$cc1]["res_color"] = $res_color;
		$item_res[$cc1]["race"] = $result1['race'];
		$item_res[$cc1]["class"] = $result1['class'];
		$item_res[$cc1]["gender"] = $char_gender;
		$item_res[$cc1]["level"] = $result1['level'];
		$item_res[$cc1]["pos"] = $res_pos;
		$item_res[$cc1]["guid"] = $result1['guid'];
		
		$charInfoC = $fnmaChar->charInfo['class'][$item_res[$cc1]["class"]];
		$charInfoN = $fnmaChar->charInfo['race'][$item_res[$cc1]["race"]];
		$item_res[$cc1]["charClassInfoC"] = $charInfoC;
		$item_res[$cc1]["charClassInfoN"] = $charInfoN;
		
    }
}
unset($query1, $result1);
//dev($item_res);
$xoopsTpl->assign('data', $item_res);

$pnum = ceil($cc2 / $items_per_pages);
$pages_str = paginate($pnum, $p, "tot_chars.php");
$xoopsTpl->assign('pages_str', $pages_str);
$xoopsTpl->assign('realm_info_new', $realm_info_new[1]);

include XOOPS_ROOT_PATH."/footer.php";
?>