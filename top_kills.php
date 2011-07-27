<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'header.php';

xoops_loadLanguage('tops', 'fnMangosAdmin');

$op = 'main';

if (isset($_POST['op'])) {
    $op = trim($_POST['op']);
} else if (isset($_GET['op'])) {
    $op = trim($_GET['op']);
}

// some config //
$limit = 100; // Only top 50 in stats

include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'zones' . DS . 'fnma_zone.php';
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'class' . DS . 'characters' . DS . 'chars.php';

$xoopsOption['template_main'] = 'fnma_server_topkills.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );

$xoopsTpl->assign('lang_each_fighter_faction', sprintf(_MD_FNMA_EACH_FIGHTER_FACTION, $limit));

$fnmaChar = new Char;

// Get the top so many kills for each faction using the Character SDL
$ally_kills = $fnmaChar->getFactionTopKills(1, $limit);
$horde_kills = $fnmaChar->getFactionTopKills(0, $limit);

if($ally_kills != FALSE)
{
	foreach($ally_kills as $charinfo_item)
	{
		$char_rank_id = calc_character_rank($charinfo_item['totalKills']);
		$character = array(
			'name'   => $charinfo_item['name'],
			'race'   => $fnmaChar->charInfo['race'][$charinfo_item['race']],
			'class'  => $fnmaChar->charInfo['class'][$charinfo_item['class']],
			'gender' => $fnmaChar->charInfo['gender'][$charinfo_item['gender']],
			'rank'   => '',
			'level'  => $charinfo_item['level'],
			'honorable_kills'    =>  $charinfo_item['totalKills'],
			'race_icon'   => XOOPS_URL. '/modules/fnMangosAdmin/images/icons/race/'.$charinfo_item['race'].'-'.$charinfo_item['gender'].'.gif',
			'class_icon'   => XOOPS_URL.'/modules/fnMangosAdmin/images/icons/class/'.$charinfo_item['class'].'.gif',
			'rank_icon'   => XOOPS_URL. '/modules/fnMangosAdmin/images/icons/pvpranks/rank'.$char_rank_id.'.gif',
		);
		$ally_killer[] = $character;
		$xoopsTpl->assign('allykiller', $ally_killer);
	}
}		

if($horde_kills != FALSE)
{
	foreach($horde_kills as $charinfo_item)
	{
		$char_rank_id = calc_character_rank($charinfo_item['totalKills']);
		$character = array(
			'name'   => $charinfo_item['name'],
			'race'   => $fnmaChar->charInfo['race'][$charinfo_item['race']],
			'class'  => $fnmaChar->charInfo['class'][$charinfo_item['class']],
			'gender' => $fnmaChar->charInfo['gender'][$charinfo_item['gender']],
			'rank'   => '',
			'level'  => $charinfo_item['level'],
			'honorable_kills'    =>  $charinfo_item['totalKills'],
			'race_icon'   => XOOPS_URL. '/modules/fnMangosAdmin/images/icons/race/'.$charinfo_item['race'].'-'.$charinfo_item['gender'].'.gif',
			'class_icon'   => XOOPS_URL. '/modules/fnMangosAdmin/images/icons/class/'.$charinfo_item['class'].'.gif',
			'rank_icon'   => XOOPS_URL. '/modules/fnMangosAdmin/images/icons/pvpranks/rank'.$char_rank_id.'.gif',
		);
		$horde_killer[] = $character;
		$xoopsTpl->assign('hordekiller', $horde_killer);
	}
}


include XOOPS_ROOT_PATH."/footer.php";
?>