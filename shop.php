<?php

include('header.php');
include_once XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . DS . 'include' . DS . 'defines.php';

xoops_loadLanguage('shop', 'fnMangosAdmin');

$xoopsOption['template_main']= 'fnma_shop.html';
include XOOPS_ROOT_PATH."/header.php";
$xoTheme->addScript('modules/'.$xoopsModule->getVar("dirname").'/js/tooltip.js', null, '' );
$xoTheme->addStylesheet('modules/'.$xoopsModule->getVar("dirname").'/css/fnma.css', null, '' );


$shop_items = $fnmaDB["sys"]->select("SELECT * FROM ".$xoopsDB->prefix('fnma_shop_items')." WHERE realms=".$_SESSION['selected_realm']." OR realms='0'");

// foreach for the packege items
foreach($shop_items as $pack){
	if($pack['item_number'] > '0')
	{
		$item_name = $fnmaDB["world"]->selectCell("SELECT name FROM item_template WHERE entry='".$pack['item_number']."'");
		$xoopsTpl->assign("item_name", $item_name);
	}
	if($pack['gold'] > '0')
	{
		$newGold = parse_gold($pack['gold']);
		if($newGold['gold'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['gold']." &nbsp;<img src=\"images/icons/money/gold.gif\" alt=\"Gold\" border=\"0\">");
		}
		if($newGold['silver'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['silver']." &nbsp;<img src=\"images/icons/money/silver.gif\" alt=\"Silver\" border=\"0\">");
		}
		if($newGold['copper'] > '0')
		{
			$xoopsTpl->assign('print_gold', "".$newGold['copper']." &nbsp;<img src=\"images/icons/money/copper.gif\" alt=\"Copper\" border=\"0\">");
		}
	}
$xoopsTpl->assign('pack', $pack);

}


$xoopsTpl->assign('shop_items', $shop_items);
$xoopsTpl->assign(array(
					"lang_shop_package" => _FNMA_SHOP_PACKAGE,
					"lang_reward"		=> _FNMA_SHOP_REWARD,
					"lang_cost"			=> _FNMA_SHOP_COST,
					"lang_action"		=> _FNMA_SHOP_ACTION,
					"lang_gold"			=> _FNMA_GOLD,
					"lang_itemset"		=> _FNMA_ITEMSET,
					"lang_web_points"	=> _FNMA_WEB_POINTS,
					"lang_btn_choose"	=> _FNMA_BTN_CHOOSE
					));


include_once XOOPS_ROOT_PATH.'/footer.php';

?>